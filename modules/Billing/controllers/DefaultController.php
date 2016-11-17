<?php

namespace app\modules\Billing\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Item;
use app\modules\Orders\models\Order;
use app\models\Ordertype;
use app\models\Invoices;
use app\models\Location;
use app\models\ShipmentsItems;
use app\models\Shipment;
use app\models\Customer;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use app\models\ShipmentBoxDetail;
use app\models\Itemsordered;
use app\models\Medias;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller {

    const PDF_DOWNLOAD_PATH = "/public/temp/pdf/invoice/";
    public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// We will override the default rule config with the new AccessRule class
				'ruleConfig' => [
					'class' => AccessRule::className(),
				],
				'only' => [
                                    'index', 'search', 'invoice', 'viewshipment', 'createinvoice', 'sendmailform', 'sendmailform', 'generatepdf'
						],
				'rules' => [
					[
						'actions' => [
                                                    'index', 'search', 'invoice', 'viewshipment', 'createinvoice', 'sendmailform', 'sendmailform', 'generatepdf'
						],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
							User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
						],
					]
				],
			]
		];
	}	
    
    
    public function actionIndex() {
        $_post = Yii::$app->request->get();
        if (Yii::$app->user->identity->usertype != User::REPRESENTATIVE) {
            $query = Order::find()->select('lv_salesorders.*')
                    ->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                    ->where(['status' => array_search('Shipped', Item::$status), 'lv_salesorders.deleted' => 0]);
            if(!empty($_post['type'])){
                $query->innerJoin('lv_ordertype', 'lv_ordertype.id = lv_salesorders.ordertype');
                $query->andWhere(['lv_ordertype.name' => $_post['type']]);
            }
            $query->groupBy('lv_salesorders.id');
        } else {
            $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->asArray()->all(), 'customerid');
            $query = Order::find()->select('lv_salesorders.*')
                    ->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                    ->where(['status' => array_search('Shipped', Item::$status), '`lv_salesorders`.`customer_id`' => $customers, 'lv_salesorders.deleted' => 0]);
            if(!empty($_post['type'])){
                $query->innerJoin('lv_ordertype', 'lv_ordertype.id = lv_salesorders.ordertype');
                $query->andWhere(['lv_ordertype.name' => $_post['type']]);
            }
            $query->groupBy('lv_salesorders.id');            
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            'pagination' => ['pageSize' => 15],
        ]);
        if (Yii::$app->request->isAjax) {
            $html = $this->renderPartial('_billing', ['dataProvider' => $dataProvider]);
            $_retArray = array('html' => $html);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $_retArray;
        }
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
    
    public function actionSearch(){
        $_post = Yii::$app->request->get();
        $_retArray = array('success' => FALSE, 'html' => '');
        if (!isset($_post['query'])) {
                $_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
                echo json_encode($_retArray);
                exit();
        }

        $type = $_post['query'];

        $searchModel = new \app\models\BillingSearch;
        $dataProvider = $searchModel->search(['BillingSearch'=>['number_generated'=>trim($type)]]);

        $html = $this->renderPartial('_billing', [
                        'dataProvider' => $dataProvider,
        ]);

        $_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $_retArray;
        exit();
    }
    
    public function actionInvoice($id) {
        $invoice_query = Invoices::find()->where(['orderid' => $id]);
        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoice_query,
            'pagination' => ['pageSize' => 15],
        ]);

        $notInvoiced_query = ShipmentsItems::find()
                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
                ->where(['status' => array_search('Shipped', Item::$status)])
                ->andWhere('lv_items.ordernumber = ' . $id)
                ->groupBy('lv_shipments_items.shipmentid');
        $notInvoicedDataProvider = new ActiveDataProvider([
            'query' => $notInvoiced_query,
            'pagination' => ['pageSize' => 15],
        ]);
        $countNotInvoiced = $notInvoiced_query->count();
        return $this->render('invoice', ['invoiceDataProvider' => $invoiceDataProvider, 'notInvoicedDataProvider' => $notInvoicedDataProvider, 'countNotInvoiced' => $countNotInvoiced, 'order_id' => $id]);
    }

    public function actionViewshipment($id) {
//        $shipmentItems = ShipmentsItems::findOne($id);
        $_shipment = Shipment::findOne($id);
        if ($_shipment != NULL) {
            $model = Order::findOne($_shipment->orderid);
            $shipmethod = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);
            $_company = ShippingCompany::findOne($shipmethod->shipping_company_id);
            if ($shipmethod->shipping_company_id === 1) {
                $ups = new \Ups\Entity\Service;
                $ups->setCode($shipmethod->_value);
                $__shipping_method = $ups->getName();
            } else if ($shipmethod->shipping_company_id === 3) { //Waiting DHL issues solved
            } else {
                $__shipping_method = $shipmethod->_value;
            }
            $ispallet = (strpos(strtolower($__shipping_method), 'freight') !== false) ? true : false;
            $ispallet = false;
            $_delivery_method = $_company->name . ' ' . $__shipping_method;
            $number_items = Itemsordered::find()->where(['ordernumber' => $id])->sum('qty');
            $numbers_items_readytoship = Item::find()->where(['status' => array_keys(Item::$shippingstatus), 'ordernumber' => $model->id])->count();
            $readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
            $readypercentage = round($readypercentage, 2);

            if ($ispallet) {
                $group_by = 'outgoingpalletnumber';
            } else {
                $group_by = 'outgoingboxnumber';
            }
            $items = Item::find()->innerJoin('`lv_shipments_items`', '`lv_shipments_items`.`itemid` = `lv_items`.`id`')->where(['ordernumber' => $model->id])->groupBy($group_by)->all();
            $shipmentBoxDetails = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->all();
            $trackingLink = '';
            if (strtolower($_company->name) == 'ups') {
                $trackingLink = 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $_shipment->master_trackingnumber;
            } else if (strtolower($_company->name) == 'fedex') {
                $trackingLink = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=' . $_shipment->master_trackingnumber;
            } else if (strtolower($_company->name) == 'dhls') {
                $trackingLink = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=' . $_shipment->master_trackingnumber;
            } else if (strtolower($_company->name) == 'usps') {
                $trackingLink = 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels=' . $_shipment->master_trackingnumber;
            }
            return $this->render('_viewshipment', [
                        'items' => $items,
                        'model' => $model,
                        '_delivery_method' => $_delivery_method,
                        'readypercentage' => $readypercentage,
                        '_shipment' => $_shipment,
                        'ispallet' => $ispallet,
                        'trackingLink' => $trackingLink,
                        'shipmentBoxDetails' => $shipmentBoxDetails
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreateinvoice($id) {
        $shipment_items = ShipmentsItems::find()
                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
                ->where(['status' => array_search('Shipped', Item::$status)])
                ->andWhere('lv_items.ordernumber = ' . $id)
                //->groupBy('lv_shipments_items.shipmentid')
                ->all();
        foreach ($shipment_items as $shipment_item) {
            $_item = Item::findOne($shipment_item->itemid);
            $_item->status = array_search('Ready to Invoice', Item::$status);
            $_order = Order::findOne($id);
            if ($_item->save()) {
                $generatedInvCount = Invoices::find()->where(['orderid' => $id, 'generated' => 1])->count();
                $invCount = Invoices::find()->where(['orderid' => $id])->count();
                if ($invCount == $generatedInvCount) {
                    $invoice_model = New Invoices;
                    $invoice_model->created_at = date('Y-m-d H:i:s');
                    $invoice_model->orderid = $id;
                    $invoiceName = $this->_getInvoiceName($_order);
                    $invoice_model->invoicename = $invoiceName . '_' . ($generatedInvCount + 1);
                    $invoice_model->save(false);
                }
                Yii::$app->getSession()->setFlash('success', 'Invoice has been created successfully.');
            } else {
                Yii::$app->getSession()->setFlash('danger', 'There is some problem in creating invoice.');
            }
        }
        $this->redirect(['/billing/invoice?id=' . $id]);
    }
    
    public function actionSendmailform(){
		//if (Yii::$app->request->isAjax) {
			$_post = Yii::$app->request->get();
			
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			$invoiceModel = Invoices::findOne($id);
			$model = Order::findOne($invoiceModel->orderid);
			$customer = Customer::findOne($model->customer_id);
                        $filename = $invoiceModel->invoicename.".pdf";
//                        $pdf_path = Yii::$app->request->baseUrl . self::PDF_DOWNLOAD_PATH;
//			$newfile = $pdf_path.$filename;
			$html = $this->renderPartial('_sendmailform', ['model'=>$model, 'current_file'=>$filename, 'customer'=>$customer, 'type'=>4, 'invoiceModel' => $invoiceModel]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();			
		//}		
	}
        
    public function actionSendmail(){
		//if (Yii::$app->request->isAjax) {
			//
            $_retArray = array('success' => FALSE, 'message' => '');			
            $_post = Yii::$app->request->post();
            $tomail = Yii::$app->params['supportEmail'];
//			$tomail = 'kingsunny777@gmail.com';
            $additional_address = split(";", $_post['cc']);

            $invoiceModel = Invoices::findOne($_post['invoiceId']);
            $model = Order::findOne($invoiceModel->orderid);
            $filename = self::PDF_DOWNLOAD_PATH . $invoiceModel->invoicename.'.pdf';

            $customer = Customer::findOne($model->customer_id);

            $success = false;

            $error = 0;

            $body = $this->renderPartial('_invoicemailtemplate', ['model' => $model, 'customer'=>$customer, 'content'=>$_post['body']]);

            $body = preg_replace('/(\+?[\d-\(\)\s]{8,20}[0-9]?\d)/', ' <a href="tel:$1">$1</a>', $body);

            //preg_match_all('/(\+?[\d-\(\)\s]{8,20}[0-9]?\d)/', "<a href='$1'>$1</a>", $body);

            //main email validation
            if(filter_var($tomail, FILTER_VALIDATE_EMAIL) !== false)
            {			
                    $mail = Yii::$app->mailer->compose()
                                            ->setFrom([Yii::$app->params['adminEmail'] => 'Matthew Ebersole'])
                                            ->setTo($tomail)
                                            ->setSubject($_post['subject'])
                                            ->setHtmlBody($body);
                    $mail->attach(Yii::getAlias('@webroot') . $filename);
                    //
                    $mail->send();
                    $success = true;
            }			
            else 
                    $error = 1;
            //
            if(isset($_post['cc']) && $_post['cc']!="")
            {
                    foreach($additional_address as $address)
                    {
                            //$address = trim($address);
                            $address = trim($tomail);
                            if(filter_var($address, FILTER_VALIDATE_EMAIL) !== false)
                            {
                                    $mail = Yii::$app->mailer->compose()
                                                            ->setFrom([Yii::$app->params['adminEmail'] => 'Matthew Ebersole'])
                                                            ->setTo($address)
                                                            ->setSubject($_post['subject'])
                                                            ->setHtmlBody($body);
                                                            //->attach($filename);
                                    $mail->attach(Yii::getAlias('@webroot') . $filename);
                                    $mail->send();
                                    $success = true;
                            }
                            else
                                    $error = 2;
                    }
            }
            //
            if($success)
                    $_retArray = array('success' => true, 'message' => 'Mail is sent Successfully');
            else 
            {
                    if($error==1)
                            $_message = "Invalid {To} mail adrress!";
                    else if($error==2)
                            $_message = "Wrong Email in Additional address!";
                    $_retArray = array('error' => true, 'message' => $_message);	
            }

            echo json_encode($_retArray);
            exit();				
		/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
	}
        
    public function actionGeneratepdf($id){
        $invoiceModel = Invoices::findOne($id);
        $model = Order::findOne($invoiceModel->orderid);
        $shipment = Shipment::findOne(['orderid' => $model->id]);
    	$shipping_method = ShipmentMethod::findOne($shipment->shipping_deliverymethod);
    	
    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);
    	
    	$customer = Customer::findOne($model->customer_id);
    	
    	$assetCustomer = Customer::findOne(4);
    	
    	$location = Location::findOne($model->location_id);
    	
    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);
    	
    	$_media_customer = Medias::findOne($customer->picture_id);
    	
//    	$itemsordered = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();
        $items = Item::find()->where(['ordernumber' => $model->id, 'status' => array_search('Ready to Invoice', Item::$status)])->groupBy('model')->all();
    	$maxRows = 18;
    	
    	$taxRate = 10;
    	
    	$content = $this->renderPartial('_generate', [
    			'model'=>$model, 
    			'customer'=>$customer, 
    			'assetCustomer'=>$assetCustomer,
    			'location'=>$location,
    			'assetLocation'=>$assetLocation,
    			'shipment'=>$shipment,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'_media_customer'=>$_media_customer, 
    			'items'=>$items, 
    			'maxRows'=>$maxRows,
    			'taxRate'=>$taxRate
    			]);
		//
    	$cssContent = "
    		.kv-heading-1, th, td {font-size:18px}
    		th
		 	{
		 		background: #08c;
    			color: #FFF;
    			padding: 5px;
    			padding-left: 10px;
    			padding-right: 10px;
    			text-align:center;
		 	}	
    		table {width:1350px;font-size:14px;border-collapse:collapse;margin-bottom:60px;}
    		#header_pdf th {background: none;color: #333;font-size:32px;text-align: left;}
    		#header_pdf tr{border:none;}
    		#sr_addresses tr{border:1px solid white;}
    		#header_pdf td{font-size:20px;}
    		#shipping_methods tr, #products tr {border:2px solid silver;}
    		#shipping_methods td {text-align:center;}
    		#products td {padding-right: 8px;text-align: left;}
    		table tr.no-border-row {border-bottom: none;} 
    		.pair-row {background: #BBB;}   		
    		.align_right {text-align:right;}
    		.align_left {text-align:left;}	
    		.border{border:1px solid silver;}
    		.no_border{border:none;}
                #shipping_methods td{text-align: left; padding-left: 25px}
    	";
		
    	$pdf = Yii::$app->pdf;
    	
    	$pdf->content = $content;
    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1, true, true);
        $mpdf->WriteHTML($content, 2);
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);
    	
//    	$mpdf->SetHeader('SO#: ' . $model->number_generated);
        $mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	$footerText = '<div class="footer" style="font-size: 10px; font-weight: normal; text-align: center;margin-top:15px;">
		<div class="line_two">3431 N. Industrial Dr., Simpsonville, SC 29681</div>
		<div class="line_three">Tel: 864.331.8678 E-mail: info@assetenterprises.com Web: www.assetenterprises.com</div>
	</div>';
    	$mpdf->SetFooter($footerText);
        $filename = $invoiceModel->invoicename.".pdf";
        $pdf_path = Yii::getAlias('@webroot') . self::PDF_DOWNLOAD_PATH;
        if (!is_dir($pdf_path)) {
            mkdir($pdf_path, 0777, true);
        }
	$targetfile = $pdf_path . $filename;
    	$mpdf->Output($targetfile, 'F');
        $invoiceModel->generated = 1;
        $invoiceModel->save();
        Yii::$app->getSession()->setFlash('success', 'Invoice has been generated successfully!!!');
        $this->redirect(['/billing/invoice?id='.$invoiceModel->orderid]);
	    // return the pdf output as per the destination setting
	    //return $pdf->render(); 	
    }
    
    private function _getInvoiceName($model) {
        $invoiceName = '';
        if (empty($model->number_generated)) {
            $location = Location::findOne($model->location_id);
            if (!empty($location->storenum)) {
                $invoiceName = $location->storenum;
            } else {
                $invoiceName = $location->storename;
            }
        } else {
            $invoiceName = $model->number_generated;
        }
        return $invoiceName;
    }

}
