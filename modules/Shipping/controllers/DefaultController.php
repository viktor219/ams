<?php

namespace app\modules\Shipping\controllers;

use app\modules\Orders\models\Order;
use app\models\Customer;
use app\models\Location;
use app\models\CustomerSearch;
use app\models\Shipping;
use app\models\Shipment;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\Item;
use app\models\Itemlog;
use app\models\Manufacturer;
use app\models\Medias;
use app\models\Models;
use app\models\ShipmentsItems;
use app\models\Itemsordered;
use app\models\Ordertype;
use app\models\ShippingSearch;
use app\models\User;
use app\models\ShipmentBoxDetail;
use app\components\AccessRule;
use app\models\UserHasCustomer;
use app\models\SystemSetting;
use app\models\CustomerSetting;
use app\models\SalesorderWs;
use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class DefaultController extends Controller {

	const _temp_PDF_PATH = "public/temp/pdf/salesorder/";

	const _labelreturn_PATH = "public/medias/labels/";

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
							'index', 'create', 'update', 'view', 'delete', 
							'searchinshipping', 'searchreadytoship', 'createshipment',
							'printpackinglist', 'readytoshipmodel', 'readytoship', 'printalllabel',
							'printlabel', 'loadshippingmodel', 'loadboxdimform', 'saveboxdimension', 
							'saveboxnumber', 'bulksavebox', 'createship', 'changeboxpallet', 'shipmentdetails', 
							'viewboxdimform', 'validateboxdim', 'createnow'
						],
				'rules' => [
					[
						'actions' => [
							'index', 'create', 'update', 'view', 'delete', 
							'searchinshipping', 'searchreadytoship', 'createshipment',
							'printpackinglist', 'readytoshipmodel', 'readytoship', 'printalllabel',
							'printlabel', 'loadshippingmodel', 'loadboxdimform', 'saveboxdimension', 
							'saveboxnumber', 'bulksavebox', 'createship', 'changeboxpallet',  'shipmentdetails', 
							'viewboxdimform', 'validateboxdim', 'createnow'
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
					],
					[
						'actions' => [
							'index',
						],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_TECHNICIAN
						],
					]
				],
			]
		];
	}

	public function actions() {
		return [
			// declares "searchinshipping" action
			'searchinshipping' => [
				'class' => 'app\modules\Shipping\controllers\_actions\SearchInShipping',
			],
			'searchreadytoship' => [
				'class' => 'app\modules\Shipping\controllers\_actions\SearchReadyToShip',
			],
		];
	}
    /**
     *
     * @return type
     */
	public function actionIndex() {

        /**
         * Mobile or Desktop Detetcion
         */
        /*if (Yii::$app->mobileDetect->isMobile()) {

            $_index = 'index_mobile';
        } else {*/

        $_index = 'index';
        //}
		if(Yii::$app->user->identity->usertype != 1)
		{
			$query = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
								 ->where(['status'=>array_keys(Item::$shippingallstatus)])
								 ->groupBy('ordernumber')
								 ->orderBy('id DESC');
			//
			$query1 = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
				->where(['status'=>array_search('Ready to ship', Item::$status)])
				->groupBy('ordernumber');
		} else {
			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
			$query = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
								 ->where(['status'=>array_keys(Item::$shippingallstatus)])
								 ->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
								 ->groupBy('ordernumber')
								->orderBy('id DESC');
			//
			$query1 = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
									->where(['status'=>array_search('Ready to ship', Item::$status)])
									->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
									->groupBy('ordernumber');
		}

		$dataProvider0 = new ActiveDataProvider([
							'query' => $query,
							'pagination' => ['pageSize' => 15],
						]);

		$dataProvider1 = new ActiveDataProvider([
				'query' => $query1,
				'pagination' => ['pageSize' => 15],
				]);
        return $this->render($_index, [
				'dataProvider' => $dataProvider0,
				'dataProvider1' => $dataProvider1
        	]);
    }

    /**
     * This function is used to save Box/Pallet Number.
     * @param integer $itemid
     * @param integer $number
     * @param string $type
     * @return integer
     */
    public function actionSaveboxnumber($itemid, $number, $type){
        $itemModel = Item::findOne($itemid);
        if($type == 'box'){
            $itemModel->outgoingboxnumber = $number;
        } else {
            $itemModel->outgoingpalletnumber = $number;
        }
        if($itemModel->save()){
            $error = true;
            $_message = ucfirst($type).' number has been updated successfully';
        } else {
            $error = false;
            $_message = json_encode($itemModel->errors);
        }
        echo json_encode(array('message' => $_message, 'success' => $error));
    }

    /**
     * This function is used to bulk save Box/Pallet Number for items.
     * @param string $itemids
     * @param string $numbers
     * @param string $type
     * @return integer
     */
    public function actionBulksavebox($itemids, $numbers, $type){
        $items = explode(',', $itemids);
        $values = explode(',', $numbers);
        $error = false;
        $_message = '';
        foreach($items as $key => $item){
            $itemModel = Item::findOne($item);
            if($type == 'box'){
                $itemModel->outgoingboxnumber = $values[$key];
            } else {
                $itemModel->outgoingpalletnumber = $values[$key];
            }
            if($itemModel->save()){
                $error = true;
                $_message = ucfirst($type).' number has been updated successfully';
            } else {
                $error = false;
                $_message = json_encode($itemModel->errors);
            }
        }
        echo json_encode(array('message' => $_message, 'success' => $error));
    }

    /**
     *
     * @param integer $shipmentid
     * @param integer $id
     */
    public function actionCreateship($id){
        $_post = Yii::$app->request->get();
        if(!isset($_post['shipment'])){
            $shipment = Shipment::find()
                    ->where(['orderid'=>$id])
                    ->andWhere('master_trackingnumber is NULL')
                    ->one();
            if($shipment != NULL){
                $url = Url::toRoute(['/shipping/createship', 'id'=>$id, 'shipment' => $shipment->id]);
                return $this->redirect($url);
            } else {
                $items = Item::find()->where(['status'=>[array_search('Ready to ship', Item::$status)], 'ordernumber'=>$id])->all();
                foreach($items as $item){
                    $countShipmentItem = ShipmentsItems::find()->where(['itemid' => $item->id])->count();
                    if($countShipmentItem==0){
                        $shipModel = Shipment::find()->where(['orderid' => $id])->one();
                        $newShipModel = New Shipment;
                        $newShipModel->attributes = $shipModel->attributes;
                        $newShipModel->dateshipped = NULL;
                        $newShipModel->save(false);

                        $shipmentItemModel = New ShipmentsItems;
                        $shipmentItemModel->shipmentid = $newShipModel->id;
                        $shipmentItemModel->itemid = $item->id;
                        $shipmentItemModel->date_added = date('Y-m-d H:i:s');
                        $shipmentItemModel->save();
                        $url = Url::toRoute(['/shipping/createship', 'id'=>$id, 'shipment' => $newShipModel->id]);
                        return $this->redirect($url);
                    }
                }
            }
        }
        $model = $this->findModel($id);
        $_shipment = Shipment::findOne($_post['shipment']);
//        $_shipment = Shipment::find()->where(['orderid' => $id])->one();
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
        $_delivery_method = $_company->name . ' ' . $__shipping_method;
//        $number_items = Itemsordered::find()->where(['ordernumber' => $id])->sum('qty');
//        $numbers_items_readytoship = Item::find()->where(['status' => array_keys(Item::$shippingstatus), 'ordernumber' => $model->id])->count();
//        $readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
//        $readypercentage = round($readypercentage, 2);
        $excludeStatus[] = array_search('In Transit', Item::$status);
		$excludeStatus[] = array_search('Delivered', Item::$status);
		$excludeStatus[] = array_search('Received', Item::$status);
        $_itemhighstatus = Item::find()->where(['ordernumber' => $model->id])->orderBy('status DESC');
        if (!empty($excludeStatus))
            $_itemhighstatus->andWhere(['not', ['status' => $excludeStatus]]);
        $highstatus = $_itemhighstatus->one()->status;
        $sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
        $completeItems = Yii::$app->db->createCommand($sql)
                ->bindValue(':customer', $model->id)
                ->bindValue(':status', $highstatus)
                ->queryAll();
        $completepercentage = (float) $completeItems[0]['completePercent'];
        //							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
        //round percentages
        if ((float) $completepercentage !== floor($completepercentage))
            $completepercentage = round($completepercentage);
        $_currentstatus = Item::$status[$highstatus];
        if($ispallet){
            $group_by = 'outgoingpalletnumber';
        } else {
            $group_by = 'outgoingboxnumber';
        }

        $items = Item::find()->where(['status'=>[array_search('Ready to ship', Item::$status)], 'ordernumber'=>$id])->all();
        $itemsList = array();
        foreach($items as $item){
                $itemsList[$item->model.'_'.$item->$group_by] = array(
                    'model' => $item->model,
                    'pallet_box_number' => $item->$group_by,
                    'customer' => $item->customer,
                );
        }
//        echo '<pre>'; print_r($itemsList); exit;
        return $this->render('_createshipment', [
//            'items' => $items,
            'itemsList' => $itemsList,
            'model' => $model,
            '_delivery_method' => $_delivery_method,
            //'readypercentage' => $readypercentage,
            '_shipment' => $_shipment,
            'highstatus' => $completepercentage . '% '.$_currentstatus,
            'ispallet' => $ispallet
        ]);
    }

    /**
     * This action is created to show the shipment details.
     * @param integer $id
     * @return type
     */
    public function actionShipmentdetails($id){
//        $shipmentItems = ShipmentsItems::findOne($id);
        $_shipment = Shipment::findOne($id);
        $model = $this->findModel($_shipment->orderid);
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
        $_delivery_method = $__shipping_method;
        $number_items = Itemsordered::find()->where(['ordernumber' => $id])->sum('qty');
        $numbers_items_readytoship = Item::find()->where(['status' => array_keys(Item::$shippingstatus), 'ordernumber' => $model->id])->count();
        $readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
        $readypercentage = round($readypercentage, 2);

        if($ispallet){
            $group_by = 'outgoingpalletnumber';
        } else {
            $group_by = 'outgoingboxnumber';
        }
        $shipmentBoxDetails = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->all();
        $trackingLink = '';
        if(strtolower($_company->name) == 'ups'){
            $trackingLink = 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums='.$_shipment->master_trackingnumber;
        } else if(strtolower($_company->name) == 'fedex'){
            $trackingLink = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers='.$_shipment->master_trackingnumber;
        } else if(strtolower($_company->name) == 'dhls'){
            $trackingLink = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB='.$_shipment->master_trackingnumber;
        } else if(strtolower($_company->name) == 'usps'){
            $trackingLink = 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels='.$_shipment->master_trackingnumber;
        }

        $items = Item::find()->innerJoin('`lv_shipments_items`', '`lv_shipments_items`.`itemid` = `lv_items`.`id`')->where(['lv_shipments_items.shipmentid'=>$id])->all();
        $itemsList = array();
        foreach($items as $item){
                $itemsList[$item->model.'_'.$item->$group_by] = array(
                    'model' => $item->model,
                    'pallet_box_number' => $item->$group_by,
                    'customer' => $item->customer,
                );
        }
        $excludeStatus[] = array_search('In Transit', Item::$status);
	$excludeStatus[] = array_search('Delivered', Item::$status);
	$excludeStatus[] = array_search('Received', Item::$status);
        $_itemhighstatus = Item::find()->where(['ordernumber' => $model->id])->orderBy('status DESC');
        if (!empty($excludeStatus))
            $_itemhighstatus->andWhere(['not', ['status' => $excludeStatus]]);
        $highstatus = $_itemhighstatus->one()->status;
        $sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
        $completeItems = Yii::$app->db->createCommand($sql)
                ->bindValue(':customer', $model->id)
                ->bindValue(':status', $highstatus)
                ->queryAll();
        $completepercentage = (float) $completeItems[0]['completePercent'];
        //							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
        //round percentages
        if ((float) $completepercentage !== floor($completepercentage))
            $completepercentage = round($completepercentage);
        $_currentstatus = Item::$status[$highstatus];
        return $this->render('_shipmentdetails', [
//            'items' => $items,
            'itemsList' => $itemsList,
            'model' => $model,
            '_delivery_method' => $_delivery_method,
            'readypercentage' => $readypercentage,
            '_shipment' => $_shipment,
            'ispallet' => $ispallet,
            'trackingLink' => $trackingLink,
            'highstatus' => $completepercentage . '% '.$_currentstatus,
            'shipmentBoxDetails' => $shipmentBoxDetails,
        	'id' => $id
        ]);
    }

    public function actionAllprintlabel($id)
    {
    	$_shipment = Shipment::findOne($id);

    	$shipmentBoxDetail = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->one();

    	$extension = ".pdf";

    	$filename = $_shipment->master_trackingnumber . ".png";

    	//svar_dump($filename);exit();

    	$path = self::_labelreturn_PATH . $filename;

    	if(!file_exists(Yii::getAlias('@webroot') . '/' . $path))
    		file_put_contents(Yii::getAlias('@webroot') . '/' . $path, base64_decode($shipmentBoxDetail->label_image));

    	$cssContent = "
	    	@page {
	    		font-family: Arial
	    	}
    	";

    	$content = $this->renderPartial('_returnedlabel_generate', [
    			'_file'=>$path,
    			]);

    	$targetfile = self::_temp_PDF_PATH . $_shipment->master_trackingnumber . $extension;

    	$pdf = \Yii::$app->pdf;

    	$pdf->content = $content;

    	$mpdf = $pdf->api; // fetches mpdf api

    	$mpdf->WriteHTML($cssContent, 1, true, true);

    	$mpdf->WriteHTML($content, 2);

    	$mpdf->SetTitle('LabelsDocument');

    	$mpdf->SetFooter("");

    	$hasservice = SalesorderWs::find()->where(['warehouse_id'=>$_shipment->orderid])->one();

    	if($hasservice !== null)
    	{
    		$mpdf->addPage();

    		$shipment = Shipment::find()->where(['orderid'=>$hasservice->service_id])->one();

    		$shipmentBoxDetail = ShipmentBoxDetail::find()->where(['shipmentid' => $shipment->id])->one();

    		$labelname = $shipment->master_trackingnumber;

    		$filename = $labelname . ".png";

    		$path = self::_labelreturn_PATH . $filename;

    		$encodedfile = base64_decode($shipmentBoxDetail->label_image);

    		if(!file_exists(Yii::getAlias('@webroot') . '/' . $path))
    			file_put_contents(Yii::getAlias('@webroot') . '/' . $path, $encodedfile);

	    	$content = $this->renderPartial('_returnedlabel_generate', [
	    				'_file'=>$path,
	    			]);

	    	$pdf = \Yii::$app->pdf;

	    	$pdf->content = $content;

	    	$mpdf = $pdf->api; // fetches mpdf api

	    	$mpdf->WriteHTML($cssContent, 1, true, true);

	    	$mpdf->WriteHTML($content, 2);

	    	$mpdf->SetTitle('Labels Document');

	    	$mpdf->SetFooter("");
    	}
    	//
    	$mpdf->addPage();

    	$model = $this->findModel($_shipment->orderid);

    	$shipping_method = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);

    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);

    	$customer = Customer::findOne($model->customer_id);

    	$assetCustomer = Customer::findOne(4);

    	$location = Location::findOne($model->location_id);

    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);

    	$_media_customer = Medias::findOne($customer->picture_id);

    	$itemsordered = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();

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
    			'itemsordered'=>$itemsordered,
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
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);

    	//    	$mpdf->SetHeader('SO#: ' . $model->number_generated);
    	$mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	$footerText = '<div class="footer" style="font-size: 10px; font-weight: normal; text-align: center;margin-top:15px;">
		<div class="line_two">3431 N. Industrial Dr., Simpsonville, SC 29681</div>
		<div class="line_three">Tel: 864.331.8678 E-mail: info@assetenterprises.com Web: www.assetenterprises.com</div>
	</div>';
    	$mpdf->SetFooter($footerText);

    	return $pdf->render();
    }

    public function actionGeneratelabel($id)
    {
    	$_shipment = Shipment::findOne($id);

    	$shipmentBoxDetail = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->one();

    	//var_dump($shipmentBoxDetail->label_image);exit();

    	$filename = $_shipment->master_trackingnumber . ".png";

    	$path = self::_labelreturn_PATH . $filename;

    	if(!file_exists(Yii::getAlias('@webroot') . '/' . $path))
    	    file_put_contents(Yii::getAlias('@webroot') . '/' . $path, base64_decode($shipmentBoxDetail->label_image));

    	$extension = ".pdf";

    	$cssContent = "
	    	.kv-heading-1, th, td {font-size:18px}
	    	@page {
	    		font-family: Arial
	    	}
    	";

		$content = $this->renderPartial('_returnedlabel_generate', [
					'_file'=>$path,
				]);

    	$targetfile = self::_temp_PDF_PATH . $_shipment->master_trackingnumber . '.pdf';

    	/*$content = $this->renderPartial('_returnedlabel_generate', [
    	'models'=>$models,
    	 ]);
    	*/
    	$pdf = \Yii::$app->pdf;

    	$pdf->content = $content;

    	$mpdf = $pdf->api; // fetches mpdf api

    	$mpdf->WriteHTML($cssContent, 1, true, true);

    	$mpdf->WriteHTML($content, 2);

    	$mpdf->SetTitle('Label Returned Document');

    	$mpdf->SetFooter("");

    	$mpdf->Output($targetfile, 'D');

    	//return $pdf->render();
    }

    /**
     * This action is used to change box/pallet number for the item.
     * @param integer $id
     * @param bollean $ispallet
     * @param string $number
     * @return integer
     */
    public function actionChangeboxpallet($id, $ispallet, $number){
        $_post = Yii::$app->request->get();
        $model = Item::findOne($id);
        if($ispallet){
            $model->outgoingpalletnumber = $number;
        } else {
            $model->outgoingboxnumber = $number;
        }
        if($_post['sel_number'] != ''){
            $sel_box_details = ShipmentBoxDetail::find()->where(['pallet_box_number' => $_post['sel_number'], 'modelid' => $_post['model']])->one();
            $sel_box_details->delete();
        }
        $success = $model->save();
        if($success){
            Yii::$app->getSession()->setFlash('success', 'Item has been successfully moved.');
        } else {
            Yii::$app->getSession()->setFlash('danger', 'There is some problem in moving item.');
        }
        echo $success;
    }
	public function actionReadytoshipmodel($orderid, $modelid)
	{
		$_model = Models::findOne($modelid);

		$_manufacturer = Manufacturer::findOne($_model->manufacturer);

		$_order = Order::findOne($orderid);

		$items = Item::find()->where(['status'=>array_search('In Shipping', Item::$status), 'ordernumber'=>$_order->id, 'model'=>$_model->id])->all();

		$success = true;

		foreach($items as $item)
		{
			$item->status = array_search('Ready to ship', Item::$status);

			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('Ready to ship', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->save();
                        } else {
                            $success = false;
                        }
		}

		if($success === true){
			$_message = $_manufacturer->name . ' ' . $_model->descrip . '</b> items are ready to ship now on Order : SO#'. $_order->number_generated;
			//Yii::$app->getSession()->setFlash('success', $_message);
		} else {
			$_message = json_encode($item->errors);
			//Yii::$app->getSession()->setFlash('error', $_message);
		}

		echo json_encode(array('success' => $success, 'message' => $_message));
                exit;
		//return $this->redirect(Yii::$app->request->referrer);
	}

	public function actionReadytoship($id)
	{
		$item = Item::findOne($id);

		$item->status = array_search('Ready to ship', Item::$status);

		$_model = Models::findOne($item->model);

		$_manufacturer = Manufacturer::findOne($_model->manufacturer);

		$_order = Order::findOne($item->ordernumber);

		$success = false;
		if($item->save())
		{
			$success = true;
			//track item
			$itemlog = new Itemlog;
			$itemlog->userid = Yii::$app->user->id;
			$itemlog->status = array_search('Ready to ship', Item::$status);
			$itemlog->itemid = $item->id;
			$itemlog->save();
		}

		if($success === true){
			$_message = $_manufacturer->name . ' ' . $_model->descrip . ' {'. $item->serial .'}</b> is ready to ship now on Order : SO#'. $_order->number_generated;
//			Yii::$app->getSession()->setFlash('success', $_message);
		} else {
			$_message =  json_encode($item->errors);
//			Yii::$app->getSession()->setFlash('error', $_message);
		}
//                $inShipCount = Item::find()->where(['status' => array_search('In Shipping', Item::$status), 'ordernumber'=> $item->ordernumber, 'model' => $item->model])->count();
                echo json_encode(array('success' => $success, 'message' => $_message));
//		return $this->redirect(Yii::$app->request->referrer);
	}

	public function actionCreateshipment($id)
	{
		$model = $this->findModel($id);

                $shipmentLists = Shipment::find()
                    ->where(['orderid'=>$model->id])
                    ->andWhere('master_trackingnumber is NOT NULL')
                    ->all();

                $printLists = Shipment::find()
                    ->where(['orderid'=>$model->id])
                    ->andWhere('master_trackingnumber is NULL')
                    ->all();
		$customer = $this->findCustomerModel($model->customer_id);

		$query = Item::find()->where(['status'=>[array_search('In Shipping', Item::$status), array_search('Ready to ship', Item::$status)], 'ordernumber'=>$model->id])->groupBy('model');

		/*$_models = Item::find()->select('model')->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
							 ->where(['status'=>array_keys(Item::$shippingstatus), 'ordernumber'=>$model->id])
							 ->groupBy('ordernumber', 'model');*/
//                $itemids = ArrayHelper::getColumn($query->asArray()->all(), 'id');
//                $printPackagingLists = \app\models\ShipmentsItems::find()->where(['itemid' => $itemids]);
//                if(!$printPackagingLists->count()){
                  if(!count($printLists)){
//                    $item = $query->one();
//                    if($item['id']){
                        $shipModel = Shipment::find()->where(['orderid' => $id])->one();
                        $newShipModel = New Shipment;
                        $newShipModel->shipping_deliverymethod = $shipModel->shipping_deliverymethod;
                        $newShipModel->orderid = $shipModel->orderid;
                        $newShipModel->accountnumber = $shipModel->accountnumber;
                        $newShipModel->locationid = $shipModel->locationid;
                        $newShipModel->save(false);

//                        $shipmentItemModel = New ShipmentsItems;
//                        $shipmentItemModel->shipmentid = $newShipModel->id;
//                        $shipmentItemModel->itemid = $item['id'];
//                        $shipmentItemModel->date_added = date('Y-m-d H:i:s');
//                        $shipmentItemModel->save();
//                    }
                }
                if(count($shipmentLists)){
                    $printLists = $shipmentLists;
                } else {
                    $printLists = Shipment::find()
                    ->where(['orderid'=>$model->id])
                    ->andWhere('master_trackingnumber is NULL')
                    ->limit(1)
                    ->all();
                }

//                $printLists = array();
//                foreach($printPackagingLists->all() as $key => $printPackagingList){
//                    $printLists[$printPackagingList->shipmentid] = 'Shipment '.($key+1);
//                }
		$dataProvider = new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => ['pageSize' => 15],
		]);
//        $shipmentLists = ShipmentsItems::find()
//                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
//                ->innerJoin('lv_shipments', 'lv_shipments.id = lv_shipments_items.shipmentid')
//                ->where(['ordernumber'=>$model->id])
//                ->andWhere('master_trackingnumber is NOT NULL')
//                ->groupBy('lv_shipments_items.shipmentid')->all();
        $itemsCount = Item::find()->where(['status'=>[array_search('In Shipping', Item::$status), array_search('Ready to ship', Item::$status)], 'ordernumber'=>$model->id])->count();
        $excludeStatus[] = array_search('In Transit', Item::$status);
	$excludeStatus[] = array_search('Delivered', Item::$status);
	$excludeStatus[] = array_search('Received', Item::$status);
        $_itemhighstatus = Item::find()->where(['ordernumber' => $model->id])->orderBy('status DESC');
        if (!empty($excludeStatus))
            $_itemhighstatus->andWhere(['not', ['status' => $excludeStatus]]);
        $highstatus = $_itemhighstatus->one()->status;
        $sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
        $completeItems = Yii::$app->db->createCommand($sql)
                ->bindValue(':customer', $model->id)
                ->bindValue(':status', $highstatus)
                ->queryAll();
        $completepercentage = (float) $completeItems[0]['completePercent'];
        //							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
        //round percentages
        if ((float) $completepercentage !== floor($completepercentage))
            $completepercentage = round($completepercentage);
        $_currentstatus = Item::$status[$highstatus];
        return $this->render('_addshipment', [
                    'model' => $model,
                    'customer' => $customer,
                    'printLists' => $printLists,
                    'shipmentLists' => $shipmentLists,
                    'itemsCount' => $itemsCount,
                    //'_models' => $_models,
                    'dataProvider' => $dataProvider,
                    'excludeStatus' => $excludeStatus,
                    'highstatus' => $completepercentage . '% '.$_currentstatus
        ]);
	}

	public function actionPrintalllabel($order, $model)
	{
		$_order = $this->findModel($order);

		$_model = Models::findOne($model);

		$customer = Customer::findOne($_order->customer_id);

		$items = Item::find()->where(['ordernumber'=>$_order->id, 'model'=>$_model->id, 'status'=>array_keys(Item::$shippingstatus)])->all();

		if($items != null) {
			$content = $this->renderPartial('_labelprint', [
							'order'=>$_order,
							'model'=>$_model,
							'customer'=>$customer,
							'items'=>$items,
							'showall'=>true
						]);
		}

		//set label printed for each item
		if(!empty($content))
		{
			foreach($items as $item) {
				if($item->labelprinted == 0) {
					$item->labelprinted = 1;
					if($item->save())
					{
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('Label Printing', Item::$definedstatus);
						$itemlog->itemid = $item->id;
						$itemlog->save();
					}
				}
			}
		}

		$cssContent = "
			.item-style
			{
				margin-bottom : 25px;
			}
		";

    	$pdf = Yii::$app->pdf;

    	$pdf->content = $content;

    	$mpdf = $pdf->api; // fetches mpdf api

    	$mpdf->WriteHTML($cssContent, 1, true, true);

    	$mpdf->SetTitle('Generate Label SO#: ' . $_order->number_generated);

    	$mpdf->SetHeader('Generate Label : SO#' . $_order->number_generated);

    	$mpdf->SetFooter('{PAGENO}');

	    // return the pdf output as per the destination setting
	    return $pdf->render();
	}

	public function actionPrintlabel($id)
	{
		$item = Item::findOne($id);

		$_order = $this->findModel($item->ordernumber);

		$_model = Models::findOne($item->model);

		$customer = Customer::findOne($_order->customer_id);

		if($item != null) {
			$content = $this->renderPartial('_labelprint', [
							'order'=>$_order,
							'model'=>$_model,
							'customer'=>$customer,
							'item'=>$item,
							'showall'=>false
						]);
		}

		//set label printed for each item
		if(!empty($content))
		{
			if($item->labelprinted == 0) {
				$item->labelprinted = 1;
				if($item->save())
				{
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('Label Printing', Item::$definedstatus);
					$itemlog->itemid = $item->id;
					$itemlog->save();
				}
			}
		}

		$cssContent = "
			.item-style
			{
				margin-bottom : 25px;
			}
		";

    	$pdf = Yii::$app->pdf;

    	$pdf->content = $content;

    	$mpdf = $pdf->api; // fetches mpdf api

    	$mpdf->WriteHTML($cssContent, 1, true, true);

    	$mpdf->SetTitle('Generate Label SO#: ' . $_order->number_generated);

    	$mpdf->SetHeader('Generate Label : SO#' . $_order->number_generated);

    	$mpdf->SetFooter('{PAGENO}');

	    // return the pdf output as per the destination setting
	    return $pdf->render();
	}

	public function actionLoadshippingmodel()
	{
		$_post = Yii::$app->request->get();

		$modelid = $_post['modelid'];

		$order = $this->findModel($_post['orderid']);

		$query = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$modelid, 'status'=>array_keys(Item::$shippingallstatus)]);

		$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => false,
				]);

		$html = $this->renderPartial('_loadshippingmodel', [
				'dataProvider' => $dataProvider,
				'model' => $order
				]);
		$_retArray = array('success' => true, 'html' => $html);

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}

	public function actionLoadboxdimform()
	{
		$_post = Yii::$app->request->get();

		$_model = Models::findOne($_post['modelid']);

		$_manufacturer = Manufacturer::findOne($_model->manufacturer);

		//$order = $this->findModel($_post['shipmentid']);
                $pallet_box_number = (int) $_post['pallet_box_number'];
		//$query = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_model->id, 'status'=>array_keys(Item::$shippingstatus)]);


		//if($action==1)
			//$shipmentboxdetail  = array();
		//else if($action==2)
			$shipmentboxdetail  = ShipmentBoxDetail::find()->where(['shipmentid'=>$_post['shipmentid'], 'modelid'=>$_model->id, 'pallet_box_number' => $pallet_box_number])->one();
			if($shipmentboxdetail == NULL){
                            $shipmentboxdetail = New ShipmentBoxDetail;
                            $shipmentboxdetail->shipmentid = $_post['shipmentid'];
                            $shipmentboxdetail->modelid = $_post['modelid'];
                            $shipmentboxdetail->pallet_box_number = $pallet_box_number;
                        }
//		$dataProvider = new ActiveDataProvider([
//				'query' => $query,
//				'pagination' => ['pageSize' => 15],
//				]);

		$html = $this->renderPartial('_loadboxdimension', [
					//'dataProvider' => $dataProvider,
					'shipmentid' => $_post['shipmentid'],
					'_model' => $_model,
                                        'pallet_box_number' => $pallet_box_number,
					'shipmentboxdetail' => $shipmentboxdetail,
				]);
		$_retArray = array('success' => true, 'html' => $html, 'itemname'=> $_manufacturer->name . ' ' . $_model->descrip);

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
	}

        public function actionViewboxdimform(){
            $_post = Yii::$app->request->get();
            $_model = Models::findOne($_post['modelid']);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
		//$order = $this->findModel($_post['shipmentid']);
                $pallet_box_number = (int) $_post['pallet_box_number'];
			$shipmentboxdetail  = ShipmentBoxDetail::find()->where(['shipmentid'=>$_post['shipmentid'], 'modelid'=>$_model->id, 'pallet_box_number' => $pallet_box_number])->one();
			if($shipmentboxdetail == NULL){
                            $shipmentboxdetail = New ShipmentBoxDetail;
                            $shipmentboxdetail->shipmentid = $_post['shipmentid'];
                            $shipmentboxdetail->modelid = $_post['modelid'];
                            $shipmentboxdetail->pallet_box_number = $pallet_box_number;
                        }

		$html = $this->renderPartial('_viewboxdimensions', [
					//'dataProvider' => $dataProvider,
					'shipmentid' => $_post['shipmentid'],
					'_model' => $_model,
                                        'pallet_box_number' => $pallet_box_number,
					'shipmentboxdetail' => $shipmentboxdetail,
				]);
		$_retArray = array('success' => true, 'html' => $html, 'itemname'=> $_manufacturer->name . ' ' . $_model->descrip);

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
        }

        /**
         * This function is used to check if all box/pallet dimesions are there.
         * @param integer $shipment
         * @param integer $order
         * @param boolean $ispallet
         * @return json
         */
        public function actionValidateboxdim($shipment, $order, $ispallet){
            $hasError = false;
            $html = '';
            $errorBoxNumber = array();
            $column = ($ispallet) ? 'outgoingpalletnumber' : 'outgoingboxnumber';
            $items = Item::find()->where(['ordernumber'=>$order, 'status' => array_search('Ready to ship', Item::$status)])->groupBy($column)->all();
            foreach($items as $item){
                $colName = ($ispallet) ? $item->outgoingpalletnumber: $item->outgoingboxnumber;
                $hasShipment = ShipmentBoxDetail::find()->where(['shipmentid' => $shipment, 'pallet_box_number' => $colName])->count();
                if(!$hasShipment){
                    $hasError = true;
                    $errorBoxNumber[] = $colName;
                }
            }
            if($hasError){
                $label = ($ispallet)? 'Pallet #': 'Box #';
                $html = '<ul>';
                foreach($errorBoxNumber as $errorBoxNum){
                    $html .='<li>'.$label.$errorBoxNum.'</li>';
                }
                $html .= '</ul>';
            }
            $_retArray = array('hasError' => $hasError, 'html'=> $html);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();
        }
        /**
         * @param integer $id
         * @param integer $order
         */
        public function actionCreatenow($id, $order){
            $shipment_model = Shipment::findOne($id);
            $delivery_method = ShipmentMethod::findOne($shipment_model->shipping_deliverymethod);
            $shipping_company = ShippingCompany::findOne($delivery_method->shipping_company_id);
            $model = $this->findModel($order);
            $customer = Customer::findOne($model->customer_id);
            $location = Location::findOne($model->location_id);
            //find customer account number
            if($customer->defaultshippingchoice==0)
            {
            	$_setting = SystemSetting::find()->one();
            	$_accountnumber = $_setting->account_number;
            }
            else if($customer->defaultshippingchoice==1)
            {
            	$_setting = CustomerSetting::find()->where(['customerid'=>$customer->id])->one();
            	$_accountnumber = $_setting->default_account_number;
            }
            else if($customer->defaultshippingchoice==2)
            {
            	$_setting = CustomerSetting::find()->where(['customerid'=>$customer->id])->one();
            	$_accountnumber = $_setting->secondary_account_number;
            }
			//
            $storename = $location->storename;
            $storenames = explode('-', $storename);
            $_storename = $storenames[0];
            if(empty($_storename))
            	$_storename = $storename;
            if(strlen($_storename) > 38)
            	$_storename = mb_strimwidth($_storename, 0, 38, ".");
            $shipment = new \RocketShipIt\Shipment($shipping_company->name);
            $shipment->setParameter('service', $delivery_method->_value);
            $shipment->setParameter('toCompany', $_storename);
            $shipment->setParameter('shipper', $customer->companyname);
            $shipment->setParameter('shipContact', '');
            $shipment->setParameter('shipPhone', '');
            $shipment->setParameter('shipmentDescription', "{$model->number_generated} Shipment");
            $shipment->setParameter('toAddr1', $location->address);
            $shipment->setParameter('toAddr2', "Store#: {$location->storenum}");
            $shipment->setParameter('toCity', $location->city);
            $shipment->setParameter('toState', $location->state);
            $shipment->setParameter('toCode', $location->zipcode);
            $shipment->setParameter('billThirdParty', true);
            $shipment->setParameter('thirdPartyAccount', $_accountnumber); // UPS Account Number Goes Here
            $shipment->setParameter('thirdPartyPostalCode', $location->zipcode);
            $shipment->setParameter('thirdPartyCountryCode', 'US');

//            $shipmentBoxDetails = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->groupBy('pallet_box_number')->all();
            $shipmentBoxDetails = ShipmentBoxDetail::find()->where(['shipmentid' => $id])->all();
            foreach($shipmentBoxDetails as $shipmentBoxDetail){
//                print '<br/>Length: '.$shipmentBoxDetail->length;
//                print '<br/>Depth: '.$shipmentBoxDetail->depth;
//                print '<br/>Height: '.$shipmentBoxDetail->height;
//                print '<br/>Weight: '.$shipmentBoxDetail->weight;
                $package = new \RocketShipIt\Package($shipping_company->name);
                $package->setParameter('length',$shipmentBoxDetail->length);
                $package->setParameter('width',$shipmentBoxDetail->depth);
                $package->setParameter('height',$shipmentBoxDetail->height);
                $package->setParameter('weight',$shipmentBoxDetail->weight);
                $shipment->addPackageToShipment($package);
            }
            //exit;
            $response = $shipment->submitShipment();
            if(isset($response['trk_main'])){
                $shipment_model->shipping_cost = $response['charges'];
                $shipment_model->master_trackingnumber = $response['trk_main'];
                $shipment_model->trackinglink = $delivery_method->shipping_company_id;
                $shipment_model->dateshipped = date('Y-m-d H:i:s');
                foreach($shipmentBoxDetails as $index => $shipmentBoxDetail){
                    $shipmentBoxDetail->label_image = $response['pkgs'][$index]['label_img'];
                    $shipmentBoxDetail->trackingnumber = $response['pkgs'][$index]['pkg_trk_num'];
                    $shipmentBoxDetail->label_html = $response['pkgs'][$index]['label_html'];
                    $shipmentBoxDetail->save(false);
                }
                $shipment_model->save(false);
                $items = Item::find()->where(['ordernumber'=>$order, 'status'=> array_search('Ready to ship', Item::$status)])->all();
                foreach($items as $item){
                	$item->location = $model->location_id;
                    $item->status = array_search('Shipped', Item::$status);
                	if($item->save()){
                            //track item
                            $itemlog = new Itemlog;
                            $itemlog->userid = Yii::$app->user->id;
                            $itemlog->status = array_search('Shipped', Item::$status);
                            $itemlog->itemid = $item->id;
                            $itemlog->created_at = date('Y-m-d H:i:s');
                            $itemlog->save();
                    }
                    $shipmentItemModel = ShipmentsItems::find()->where(['shipmentid' => $id, 'itemid' => $item->id])->one();
                    if($shipmentItemModel == NULL){
                        $shipmentItemModel = New ShipmentsItems;
                        $shipmentItemModel->itemid = $item->id;
                        $shipmentItemModel->shipmentid = $id;
                        $shipmentItemModel->date_added = date('Y-m-d H:i:s');
                        $shipmentItemModel->save();
                    }
                }
                //Item::updateAll(['status' => array_search('Shipped', Item::$status)], ['ordernumber' => $order, 'status'=> array_search('Ready to ship', Item::$status)]);
                $_related_service = SalesorderWs::find()->where(['warehouse_id' => $order])->one();
                if($_related_service!==null)
                	Yii::$app->common->generateLabel($order);
                $returnUrl = $this->redirect(['/shipping/shipmentdetails?id='.$id]);
            } else {
                $_message =  isset($response['error']) ? $response['error']: json_encode($response);
                Yii::$app->getSession()->setFlash('danger', $_message);
                $returnUrl = $this->redirect(Yii::$app->request->referrer);
            }
            return $returnUrl;
        }
	public function actionSaveboxdimension()
	{
		$_post = Yii::$app->request->get();

		//var_dump($_post);exit(1);

		if (Yii::$app->request->isAjax) {
			//$_retArray = array('success' => FALSE, 'html' => '');
			$shipmentId = $_post['shipmentId'];
			$modelid = $_post['modelId'];
			$weight = $_post['weight'];
			$height = $_post['height'];
			$length = $_post['length'];
			$depth = $_post['depth'];
			$pallet_box_number = (int) $_post['pallet_box_number'];
			$success = false;
                        $shipmentboxdetail  = ShipmentBoxDetail::find()->where(['shipmentid'=>$shipmentId, 'modelid'=>$modelid, 'pallet_box_number' => $pallet_box_number])->one();
			if($shipmentboxdetail == NULL){
                            $shipmentboxdetail = New ShipmentBoxDetail;
                            $shipmentboxdetail->created_at = date('Y-m-d H:i:s');
                        }
			//if($action===1)
				//$shipmentboxdetail  = new ShipmentBoxDetail;
			//else if($action===2)
				//$shipmentboxdetail  = ShipmentBoxDetail::find()->where(['orderid'=>$orderid, 'modelid'=>$modelid])->one();
			$shipmentboxdetail->shipmentid = $shipmentId;
			$shipmentboxdetail->modelid = $modelid;
			$shipmentboxdetail->weight = $weight;
			$shipmentboxdetail->height = $height;
			$shipmentboxdetail->length = $length;
			$shipmentboxdetail->depth = $depth;
                        $shipmentboxdetail->pallet_box_number = $pallet_box_number;
                        $shipmentboxdetail->modified_at = date('Y-m-d H:i:s');
			if($shipmentboxdetail->save())
				$success = true;
			else
				$errors = $shipmentboxdetail->errors;
			if($success === true){
				//$_message = '<div class="alert alert-success fade in"><strong>Success!</strong> Weight & Dimensions has been successfully saved!</div>';
				//Yii::$app->getSession()->setFlash('success', $_message);
			} else {
				$_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
				Yii::$app->getSession()->setFlash('error', $_message);
			}
                        echo $success;
			//return $this->redirect(Yii::$app->request->referrer);
 			//$_retArray = array('success' => $success);

			//Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			//return $_retArray;
			exit();
		} else {

    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
	}

	private function generateLocalOrder($id)
	{
    	$model = $this->findModel($id);

    	$shipment = Shipment::find()->where(['orderid'=>$model->id])->one();

    	$shipping_method = ShipmentMethod::findOne($shipment->shipping_deliverymethod);

    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);

    	$customer = Customer::findOne($model->customer_id);

    	$assetCustomer = Customer::findOne(4);

    	$location = Location::findOne($model->location_id);

    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);

    	$_media_customer = Medias::findOne($customer->picture_id);

    	$itemsordered = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();

    	$maxRows = 18;

    	$taxRate = 10;

    	$content = $this->renderPartial('@app/modules/Orders/views/default/_generate', [
    			'model'=>$model,
    			'customer'=>$customer,
    			'assetCustomer'=>$assetCustomer,
    			'location'=>$location,
    			'assetLocation'=>$assetLocation,
    			'shipment'=>$shipment,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'_media_customer'=>$_media_customer,
    			'itemsordered'=>$itemsordered,
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
    		#header_pdf th {background: none;color: #333;font-size:32px;}
    		#header_pdf tr{border:none;}
    		#sr_addresses tr{border:1px solid white;}
    		#header_pdf td{font-size:20px;}
    		tr {border:2px solid silver;}
    		#shipping_methods td {text-align:center;}
    		#products td {padding-right: 8px;}
    		table tr.no-border-row {border-bottom: none;}
    		.pair-row {background: #BBB;}
    		.align_right {text-align:right;}
    		.align_left {text-align:left;}
    		.border{border:1px solid silver;}
    		.no_border{border:none;}
    	";

		$filename = base64_encode(uniqid().time()) . '.pdf';

		$targetfile = self::_temp_PDF_PATH . $filename ;

    	$pdf = Yii::$app->pdf;

    	$mpdf = $pdf->api; // fetches mpdf api

    	$mpdf->WriteHTML($cssContent, 1);

		$mpdf->WriteHTML($content, 2);

    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);

    	$mpdf->SetHeader('SO#: ' . $model->number_generated);

    	$mpdf->SetFooter('{PAGENO}');

		$mpdf->Output($targetfile, 'F');

		return $filename;
	}

	public function actionPrintpackinglist($id, $shipment)
	{
    	$model = $this->findModel($id);

//        $shipment = \app\models\ShipmentsItems::findOne($shipment);
//    	$shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
    	$shipment = Shipment::findOne($shipment);
    	$shipping_method = ShipmentMethod::findOne($shipment->shipping_deliverymethod);

    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);

    	$customer = Customer::findOne($model->customer_id);

    	$assetCustomer = Customer::findOne(4);

    	$location = Location::findOne($model->location_id);

    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);

    	$_media_customer = Medias::findOne($customer->picture_id);

    	$itemsordered = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();

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
    			'itemsordered'=>$itemsordered,
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
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);

//    	$mpdf->SetHeader('SO#: ' . $model->number_generated);
        $mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	$footerText = '<div class="footer" style="font-size: 10px; font-weight: normal; text-align: center;margin-top:15px;">
		<div class="line_two">3431 N. Industrial Dr., Simpsonville, SC 29681</div>
		<div class="line_three">Tel: 864.331.8678 E-mail: info@assetenterprises.com Web: www.assetenterprises.com</div>
	</div>';
    	$mpdf->SetFooter($footerText);

	    // return the pdf output as per the destination setting
	    return $pdf->render();
	}

    /**
     *
     * @return type
     */
    /*public function actionShipments() {

        if (Yii::$app->mobileDetect->isMobile()) {

            $_index = 'shipmentindex_mobile';
        } else {

            $_index = 'shipmentindex';
        }

        $searchModel = new ShippingSearch();
        if (isset($_GET['customer']))
            $customer = $_GET['customer'];
        else
            throw new NotFoundHttpException('The requested page does not exist.');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $customer);
        $customerName = $this->findCustomerModel($customer);
        return $this->render($_index, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'customerName' => $customerName->firstname." ".$customerName->lastname
        ]);
    }*/

    /**
     *
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    protected function findCustomerModel($id) {

        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    protected function findModel($id) {

        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
