<?php

namespace app\modules\Purchasing\controllers;

use Yii;
use yii\web\Controller;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\Orders\models\Order;
use app\models\User;
use app\models\Purchase;
use app\models\PurchaseSearch;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\Location;
use app\models\Vendor;
use app\models\Item;
use app\models\Itemlog;
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Receive;
use app\models\Orderlog;
use app\models\Pshipment;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\Purchasingitemrequest;
use app\vendor\PHelper;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
	const _temp_PDF_PATH = "public/temp/pdf/purchasing/";
	
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
							'index','create', 'update', 'view', 'delete',
							'search', 'sendmail', 'sendmailform',
							'generate', 'receive', 'savereceiveqty', 'receiveqty'						
						],
				'rules' => [
					[
						'actions' => [
							'index','create', 'update', 'view', 'delete',
							'search', 'sendmail', 'sendmailform',
							'generate', 'receive', 'savereceiveqty', 'receiveqty', 'delitems', 'delpurchase', 'getreceivemodel', 'changestatus'
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
	
    public function actionIndex()
    {  				
        return $this->render('index', [
        
        ]);
    }
     /**
     * This action is used to show modal title and set max quantity.
     * @param integer $id
     * @param integer $ordernumber
     * return json
     */
    public function actionGetreceivemodel($id, $ordernumber){
        $sql = "SELECT CONCAT(lv_manufacturers.name, ' ', lv_models.descrip) as title FROM `lv_models` join lv_manufacturers on lv_manufacturers.id = lv_models.manufacturer where lv_models.id = :id";
        $command = Yii::$app->db->createCommand($sql)
		->bindValue(':id', $id);
        $title = $command->queryColumn()[0];
        $number_items = Item::find()->where(['ordernumber' => $ordernumber, 'status' => 1, 'model' => $id])->count();
        $_retArray = array('title' => $title, 'quantity' => $number_items, 'itemid' => $ordernumber, 'model' => $id);
        echo json_encode($_retArray);
        exit();
    }
    
    /*
     * 
     */
    public function actionChangestatus(){
        $_post = Yii::$app->request->post();
		$_order = Order::findOne($_post['itemid']);
        $items = Item::findAll(['status' => array_search('Requested', Item::$status), 'ordernumber' => $_post['itemid'], 'model' => $_post['model']]);
        foreach($items as $key => $item){
            if($key <= ($_post['quantity'] - 1)){
                $item->status = array_search('In Stock', Item::$status);
                $item->location = $_post['location'];
                $item->conditionid = $_post['conditionid'];
                if($item->save()){
                    $orderLog = New Itemlog;
                    $orderLog->itemid = $item->id;
                    $orderLog->locationid = $_post['location'];
                    $orderLog->status = array_search('In Stock', Item::$status);
                    $orderLog->userid = Yii::$app->user->id;
                    $orderLog->created_at = date('Y-m-d H:i:s');
                    $orderLog->save();
                    $_message = 'SO#: ' . $_order->number_generated . ' Items Requested has been received successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);  
                } else {
                     $_message = 'There is problem in changing status</div>';
                     Yii::$app->getSession()->setFlash('danger', $_message);  
                }
            }
        }
        $this->redirect(['index']);
    }
    
    /**
     * This action is used to delete {items} permanently from the tables.
     * @param integer $id
     * @return redirect
     */
    public function actionDelitems($id){
    	$model = Item::findOne($id);
    	$items = Item::findAll(['status' => array_search('Requested', Item::$status), 'ordernumber' => $model->ordernumber, 'model' => $model->model]);
    	foreach($items as $item){
    		Itemlog::deleteAll(['itemid' => $item->id]);
    		$item->delete();
    	}
    	$_message = 'Items Requested has been deleted successfully!';
    	Yii::$app->getSession()->setFlash('danger', $_message);
    	return $this->redirect(['index']);
    }
    
    /**
     * This action is used to delete {prchase} permanently from the tables.
     * @param integer $id
     * @return redirect
     */
    public function actionDelpurchase($id){
    	$model = Purchase::findOne($id);
    	$model->delete();
    	Itemspurchased::deleteAll(['ordernumber' => $id]);
    	$items = Item::findAll(['purchaseordernumber' => $id]);
    	foreach($items as $item){
    		Itemlog::deleteAll(['itemid' => $item->id]);
    		$item->delete();
    	}
    	$_message = 'Purchasing Order has been deleted successfully!';
    	Yii::$app->getSession()->setFlash('danger', $_message);
    	return $this->redirect(['index']);
    }    
    
    public function actionSearch()
    {
    	$_post = Yii::$app->request->get();
    	
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['query'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		 
    		$query = $_post['query'];
    		 
    		$searchModel = new PurchaseSearch();
    		$dataProvider = $searchModel->search(['PurchaseSearch'=>['number_generated'=>trim($query)]]);
    		 
    		$html = $this->renderPartial('_porder', [
    					'dataProvider' => $dataProvider,
    				]);
    		 
    		$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}  
    }
	
	public function actionSendmail()
	{
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'message' => '');
			
			$_post = Yii::$app->request->post(); 
			//test
			//$_post = array('subject'=>'test', 'body'=>'content');
						
			$session = Yii::$app->session;
			
			//$tomail = (Yii::$app->params['mail_test']) ? Yii::$app->params['supportEmail'] : Yii::$app->params['customerEmail']; //will be $_post['to']
			
			$tomail = Yii::$app->params['customerEmail'];
			
			$filename = self::_temp_PDF_PATH . $session['__temp_porder_pdf_generated'];
			/* 
			Yii::$app->mailer->compose('_order', ['model' => $model])
				->setFrom([Yii::$app->params['supportEmail'] => 'Test Mail'])
				->setTo($tomail)
				->setSubject('This is a test mail ')
				->send();	
			*/	
			Yii::$app->mailer->compose()
				->setFrom([Yii::$app->params['adminEmail'] => 'Matthew Ebersole'])
				->setTo($tomail)
				->setSubject($_post['subject'])
				->setTextBody($_post['body'])
				->attach($filename)
				->send();
				
			//if($sent)
				$_retArray = array('success' => true, 'message' => 'Mail is sent successfully');
			echo json_encode($_retArray);
			exit();				
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
	}
	
	public function actionSendmailform()
	{
		if (Yii::$app->request->isAjax) {
			$_post = Yii::$app->request->get();
			
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			 
			$model = $this->findModel($id);
			
			$session = Yii::$app->session;
		//remove old file generated
			$old_file_generated = self::_temp_PDF_PATH . $session['__temp_porder_pdf_generated'];
			if(!is_dir($old_file_generated) && file_exists($old_file_generated))
				unlink($old_file_generated);	
		//set new file 
			$newfile = $this->generateLocalOrder($id);
			
			$session->set('__temp_porder_pdf_generated', $newfile);					
			
		//find customer to send mail
			$vendor = Vendor::findOne($model->vendor_id);
			
			$html = $this->renderAjax('_sendmailform', ['current_file'=>$newfile, 'vendor'=>$vendor, 'model'=>$model]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();			
		}
	}
	
	private function generateLocalOrder($id)
	{
    	$model = $this->findModel($id);
    	     	 
    	//$shipping_method = ShipmentMethod::findOne($model->shipping_company);
    	 
    	$shipping_company = ShippingCompany::findOne($model->shipping_company);
    	 
    	$vendor = Vendor::findOne($model->vendor_id);
    	 
    	$assetCustomer = Customer::findOne(4);
    	 
    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);
    	     	 
    	$itemspurchased = Itemspurchased::find()->where(['ordernumber'=>$model->id])->all();
    	 
    	$maxRows = 18;
    	 
    	$taxRate = 10;
    	 
    	$content = $this->renderPartial('_generate', [
    			'model'=>$model,
    			'vendor'=>$vendor,
    			'assetLocation'=>$assetLocation,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'itemspurchased'=>$itemspurchased,
    			'maxRows'=>$maxRows,
    			'taxRate'=>$taxRate
    			]);
    	 
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
		
		//$filename = base64_encode(uniqid().time()) . '.pdf';
		
		$filename = $model->number_generated . '.pdf';
		
		$targetfile = self::_temp_PDF_PATH . $filename ;
    	
    	$pdf = Yii::$app->pdf;
    	    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1);
		
		$mpdf->WriteHTML($content, 2);
    	
    	$mpdf->SetTitle('Generate PO#: ' . $model->number_generated);
    	 
    	$mpdf->SetHeader('PO#: ' . $model->number_generated);
    	
    	$mpdf->SetFooter('{PAGENO}');		
		
		$mpdf->Output($targetfile, 'F');
		
		return $filename;
	}
    
    public function actionGenerate($id)
    {
    	$model = $this->findModel($id);
    	     	 
    	//$shipping_method = ShipmentMethod::findOne($model->shipping_deliverymethod);
    	 
    	$shipping_company = ShippingCompany::findOne($model->shipping_company);
    	 
    	$vendor = Vendor::findOne($model->vendor_id);
    	 
    	$assetCustomer = Customer::findOne(4);
    	 
    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);
    	     	 
    	$itemspurchased = Itemspurchased::find()->where(['ordernumber'=>$model->id])->all();
    	 
    	$maxRows = 18;
    	 
    	$taxRate = 10;
    	 
    	$content = $this->renderPartial('_generate', [
    			'model'=>$model,
    			'vendor'=>$vendor,
    			'assetLocation'=>$assetLocation,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'itemspurchased'=>$itemspurchased,
    			'maxRows'=>$maxRows,
    			'taxRate'=>$taxRate
    			]);
    	 
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
    	 
    	$pdf = Yii::$app->pdf;
    	 
    	$pdf->content = $content;
    	 
    	$mpdf = $pdf->api; // fetches mpdf api
    	 
    	$mpdf->WriteHTML($cssContent, 1, true, true);
    	 
    	$mpdf->SetTitle('Generate PO#: ' . $model->number_generated);
    	 
    	$mpdf->SetHeader('PO#: ' . $model->number_generated);
    	 
    	$mpdf->SetFooter('{PAGENO}');
    	 
    	// return the pdf output as per the destination setting
    	return $pdf->render();    	
    }
    
    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$find = $this->findModel($id);
    		$html = $this->renderAjax('view', [
    				'model' => $find
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'title' => 'PO# ' . $find->number_generated . ' Details');
    		echo json_encode($_retArray);
    		exit();
    	} else {
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionReceive()
    {
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$find = $this->findModel($id);
    		
    		$_retArray = array('success' => true, 'title' => 'PO# ' . $find->number_generated, 'id'=>$id);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionLoad(){
		if(Yii::$app->user->identity->usertype != 1)
		{
			$query0 = Item::find()
				->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
				->where(['ordertype'=>1, 'status'=>1, 'purchaseordernumber'=>NULL, 'lv_items.deleted'=>0])
				->groupBy('model, ordernumber');
		} else {
			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
			$query0 = Item::find()
				->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
				->where(['ordertype'=>1, 'status'=>1, 'purchaseordernumber'=>NULL])
				->andWhere(['`lv_salesorders`.`customer_id`'=>$customers, 'lv_items.deleted'=>0])
				->groupBy('model, ordernumber');			
		}
    	
    	$dataProvider0 = new ActiveDataProvider([
    			'query' => $query0,
    			'pagination' => ['pageSize' => 15],
    			'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
    			]);
        $query = Purchase::find()->innerJoin('lv_items', '`lv_items`.`purchaseordernumber` = `lv_purchases`.`id`')
                    ->where(['`lv_items`.`status`'=>array_search('In Transit', Item::$status)])
                    ->andWhere('lv_purchases.deleted = 0')
					->groupBy('purchaseordernumber');
    	
    	$dataProvider = new ActiveDataProvider([ 
    			'query' => $query,
    			'pagination' => ['pageSize' => 15],
    			'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
    			]);
        $query = Purchase::find()->innerJoin('lv_items', '`lv_items`.`purchaseordernumber` = `lv_purchases`.`id`')
                    ->where(['not', ['`lv_items`.`status`' => array_search('In Transit', Item::$status)]])
                    ->andWhere('lv_purchases.deleted = 0')
					->groupBy('purchaseordernumber');
    	
    	$dataProvider3 = new ActiveDataProvider([ 
    			'query' => $query,
    			'pagination' => ['pageSize' => 50],
    			'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
    			]);
            $itemsHtml = $this->renderPartial('@app/modules/Purchasing/views/default/_items_requested', [
                        'dataProvider' => $dataProvider0,
                        'pagination' => ['pageSize' => 10],
            ]);
            
            $inactivePurchasehtml = $this->renderPartial('@app/modules/Purchasing/views/default/_inactive_purchase', [
                        'dataProvider' => $dataProvider3,
                        'pagination' => ['pageSize' => 10],
            ]);
            
            $activePurchasehtml = $this->renderPartial('@app/modules/Purchasing/views/default/_active_purchase', [
                        'dataProvider' => $dataProvider,
                        'pagination' => ['pageSize' => 10],
            ]);
            $_retArray = array('success' => true, 'items_html' => $itemsHtml,'inactive_purchasehtml' => $inactivePurchasehtml ,'active_purchasehtml' => $activePurchasehtml);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            //return view
            return $_retArray;
            exit();
        
    }
    
    public function actionLoadtype()
    {
    	$_post = Yii::$app->request->get();
    	
    	$type = $_post['type'];
    	
    	if($type == 'service')
    	{
    		if(Yii::$app->user->identity->usertype != 1)
    		{
    			$query = Item::find()
    			->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
    			->where(['ordertype'=>2, 'status'=>1, 'purchaseordernumber'=>NULL])
    			->andWhere('lv_items.deleted  = 0')
    			->groupBy('model');
    		} else {
    			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    			$query = Item::find()
    			->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
    			->where(['ordertype'=>2, 'status'=>1, 'purchaseordernumber'=>NULL])
    			->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
    			->andWhere('lv_items.deleted  = 0')
    			->groupBy('model');
    		}   		
    	}
    	else if($type == 'integration')
    	{
	    	if(Yii::$app->user->identity->usertype != 1)
	    	{
	    		$query = Item::find()
	    		->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
	    		->where(['ordertype'=>3, 'status'=>1, 'purchaseordernumber'=>NULL])
	    		->andWhere('lv_items.deleted = 0')
	    		->groupBy('model');
	    	} else {
	    		$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
	    		$query = Item::find()
	    		->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
	    		->where(['ordertype'=>3, 'status'=>1, 'purchaseordernumber'=>NULL])
	    		->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
	    		->andWhere('lv_items.deleted = 0')
	    		->groupBy('model');
	    	}
    	}
    	else if($type == 'warehouse')
    	{
	    	if(Yii::$app->user->identity->usertype != 1)
	    	{
	    		$query = Item::find()
	    		->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
	    		->where(['ordertype'=>4, 'status'=>1, 'purchaseordernumber'=>NULL])
	    		->andWhere('lv_items.deleted  = 0')
	    		->groupBy('model');
	    	} else {
	    		$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
	    		$query = Item::find()
	    		->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
	    		->where(['ordertype'=>4, 'status'=>1, 'purchaseordernumber'=>NULL])
	    		->andWhere(['`lv_salesorders`.`customer_id`'=>$customers])
	    		->andWhere('lv_items.deleted  = 0')
	    		->groupBy('model');
	    	}  	
    	}
    	
    	$dataProvider = new ActiveDataProvider([
    						'query' => $query,
    						'pagination' => ['pageSize' => 15],
    						'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
    					]);
   	
    	$html = $this->renderPartial('@app/modules/Purchasing/views/default/_wistems_requested', [
    				'dataProvider' => $dataProvider,
    				'type' => $type
    			]);
    	
    	$_retArray = array('success' => true, 'html' => $html);
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();
    }
	
	public function actionScheduledelivery()
	{		
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['item'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$itemid = $_post['item'];
			
    		$item = Item::findOne($itemid);
			
			$_model = Models::findOne($item->model);
			
			$_manufacturer = Manufacturer::findOne($_model->manufacturer);
			
            $html = $this->renderPartial('_schedule_deliveryform', [
				'model' => $item,
				'count' => Item::find()->where(['status'=>array_search('Requested', Item::$status), 'model'=>$item->model, 'ordernumber'=>$item->ordernumber])->count()
            ]);    		
    		$_retArray = array('success' => true, 'html' => $html, 'modelname' => $_manufacturer->name . ' ' . $_model->descrip);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}		
	}
	
	public function actionSavescheduledelivery()
	{
		//if (Yii::$app->request->isAjax) {
			$_post = Yii::$app->request->post();
			
			$itemid = $_post['itemId'];
			
			$_item = Item::findOne($itemid);
					
			$quantity = $_post['quantity'];
			
			$quantity = (int) $quantity;
			
			$success = false;
			
			$errors;
			
			if (empty($_item) || empty($quantity)) {
				$_retArray = array('error' => TRUE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}		
			
			$items = Item::find()->where(['status'=>array_search('Requested', Item::$status), 'model'=>$_item->model, 'ordernumber'=>$_item->ordernumber]);
			
			$count = count($items->all());
			
			$_new_row_to_inserted = 0;
			
			$_rest_quantity = 0;
			
			if($count == $quantity)
			{	
				$items = $items->all();
			}
			else if($count < $quantity)
			{
				$items = $items->all();
				$_new_row_to_inserted = $quantity-$count;
			}
			else //count > quantity
			{
				$items = $items->limit($quantity)->all();
				$_rest_quantity = $count-$quantity;
			}
			
			foreach($items as $item)
			{
				$item->status = array_search('In Transit', Item::$status);
				if($item->save()) {
					$success = true;
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('In Transit', Item::$status);
					$itemlog->itemid = $item->id;
					$itemlog->locationid = $item->location;
					$itemlog->save();				
				}
				else 
				{
					$success = false;
					$errors .= $model->errors;
				}
			}		
			//
			if($_new_row_to_inserted > 0)
			{
				for($i=0; $i<$_new_row_to_inserted; $i++)
				{
					$model = new Item;
					$model->owner_id = Yii::$app->user->id;
					$model->status = array_search('In Transit', Item::$status);
					$model->model = $_item->model;
					$model->ordernumber = $_item->ordernumber;
					$model->customer = $_item->customer;
					$model->location = $_item->location;
					if($model->save()) {
						$success = true;
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('In Transit', Item::$status);
						$itemlog->itemid = $model->id;
						$itemlog->locationid = $model->location;
						$itemlog->save();				
					}
					else 
					{
						$success = false;					
						$errors .= $model->errors;	
					}
				}
			}
			
			if ($success) 
			{
				$html = "Items has been successfully added to customer inventory!";
				$_retArray = array('success' => true, 'html' => $html, 'rest_quantity' => $_rest_quantity, 'item' => $_item->id);
			}
			else 
				$_retArray = array('error' => TRUE, 'html' => json_encode($errors));
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();		
		/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}	*/
	}
    
    public function actionSavereceiveqty($id)
    {
    	//$order = $this->findModel($id);
    	
    	$errors;

    	//if (isset($_POST['receivingqty'])){
    	//if(isset($_POST['saveReceiveQty'])){
    		//$items = $_POST['items'];
    		//$qtys = $_POST['receivingqty'];
    		$success = false;
    		//
    		//foreach ($items as $key=>$item)
    		//{
    			//$qty = $qtys[$key];
    			//$rows = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$order->id, 'model'=>$item])->all();
    			$rows = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$id])->all();
    			//$i = 0;
    			foreach($rows as $row) {
    				//echo $row->id;exit(1);
    				//if($i<$qty){
    					$row->status = array_search('Received', Item::$status);
    				//}
    				if($row->save()){
    					$success = true;
    					//track item
    					$itemlog = new Itemlog;
    					$itemlog->userid = Yii::$app->user->id;
    					$itemlog->status = array_search('Received', Item::$status);
    					$itemlog->itemid = $row->id;
                                        $itemlog->locationid = $row->location;
    					$itemlog->save();    					
    				}
    				else
    					$errors = $row->errors;
    				//$i++;
    			}
    		//}
    	//}
    	//
    	if($success === true){
    		$_message = 'Purchase Order has been updated successfully!';
    		Yii::$app->getSession()->setFlash('warning', $_message);
    	} else{
    		$_message = json_encode($errors);
    		Yii::$app->getSession()->setFlash('danger', $_message);
    	}
    	 
    	return $this->redirect(Yii::$app->request->referrer);    	
    }
    
    public function actionReceiveqty($id)
    {
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		$_post = Yii::$app->request->get();
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$id = $_post['id'];
    		$find = $this->findModel($id);
    		$html = $this->renderAjax('_receiveqty', [
    				'model' => $find
    				]);
    		$_retArray = array('success' => true, 'html' => $html);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }
    
    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($request=null)
    {
    	$data = Yii::$app->request->post();
    	 
    	$session = Yii::$app->session;
    	
    	$ordernumber = $this->generateNumber();
    	
    	$session->set('__order_number', $ordernumber); 
    	
    	//generate models
    	$_storepath = "public/autocomplete/json/purchasing/_models.json";
    	$fp = fopen($_storepath, 'w');
    	fwrite($fp, json_encode(PHelper::getMainModels()));
    	fclose($fp);
    	//
    	$item_requested = null;
    	$count_itemsrequested = null;
    	if($request!==null){
    		$request = base64_decode($request);
    		$item_requested = Item::findOne($request);
    		//var_dump($item_requested->ordernumber);
    		if($item_requested->ordernumber===null) {
    			$_message = 'Purchase Order can\'t be created. Seems Item Requested don\'t exist!';
    			Yii::$app->getSession()->setFlash('warning', $_message);
    			return Yii::$app->response->redirect(['/purchasing/index']);
    			exit(0);
    		}
    		$items_requested = Item::find()->where(['model'=>$item_requested->model, 'ordernumber'=>$item_requested->ordernumber, 'customer'=>$item_requested->customer, 'status'=>array_search('Requested', Item::$status)])->all();
    		$count_itemsrequested = Item::find()->where(['model'=>$item_requested->model, 'ordernumber'=>$item_requested->ordernumber, 'customer'=>$item_requested->customer, 'status'=>array_search('Requested', Item::$status)])->count();
    	}  	
    	
    	$_model = new Purchase();
    
    	if (count($data)>0) {
    		//var_dump($data['modelid'][0]);
    		//var_dump($data);exit(1);
    		$_existingpurchaseorder = $data['purchase'];  		
    		$ordernumber = $session['__order_number'];
    		$salesordernumber = $data['salesordernumber'];
    		$vendor = (int)$data['vendor'];
    		$estimatedtime = (!empty($data['estimatedtime'])) ? date('Y-m-d', strtotime($data['estimatedtime'])) : null;
    		$trackingnumber = $data['trackingnumber'];
    		$shippingcompany = $data['shippingcompany'];
    		$datetime = date('Y-m-d H:i:s');
    		$successorder = false;
	    	if(!empty($_existingpurchaseorder))
	    		//$_model = new Purchase();
    		//else 
    			$_model = Purchase::findOne($_existingpurchaseorder);
    		//TODO : track items added to a existing P.O
    		//save order informations.
    		//$_model->salesordernumber = $salesordernumber;
    		$_model->number_generated = $ordernumber;
    		$_model->user_id = Yii::$app->user->id;
    		$_model->vendor_id = $vendor;
    		$_model->shipping_company = $data['shippingmethod'];
    		$_model->estimated_time = $estimatedtime;
    		$_model->trackingnumber = $trackingnumber;
    		$_model->created_at = $datetime;
    		if($_model->save())
    			$successorder = true;
    		else
    			$successorder = false;
    		//log order
    		$orderlog = new Orderlog;
    		$orderlog->orderid = $_model->id;
    		$orderlog->userid = Yii::$app->user->id;
    		$orderlog->status = 1;
    		$orderlog->ordertype = 2;
    		$orderlog->save();    		
    		//save shipment
    		/*$shipment = new Pshipment;
    		$shipment->purchaseid = $_model->id;
    		$shipment->accountnumber = $data['accountnumber'];
    		$shipment->shipping_deliverymethod = $data['shippingmethod'];
    		$shipment->save();*/
    		//
    		$i=1;
    		//purchasing order from request
    		if($request!==null){
    			//echo $request;
    			$item_requested = Item::findOne($request);
    			$items_requested = Item::find()->where(['model'=>$item_requested->model, 'ordernumber'=>$item_requested->ordernumber, 'customer'=>$item_requested->customer, 'status'=>array_search('Requested', Item::$status)])->limit($data['quantity'][0])->all();
    			foreach ($items_requested as $item_requested){
    				//echo $item_requested->id . " ";
    				//$item_requested->ordernumber = null;
    				$item_requested->model = $data['modelid'][0];
    				$item_requested->purchaseordernumber = $_model->id;
    				$item_requested->status = array_search('In Transit', Item::$status);
    				if($item_requested->save()){
    					//track item
    					$itemlog = new Itemlog;
    					$itemlog->userid = Yii::$app->user->id;
    					$itemlog->status = array_search('In Transit', Item::$status);
    					$itemlog->itemid = $item_requested->id;
                                        $itemlog->locationid = $item_requested->location;
    					$itemlog->save();    					
    					$successorder = true;
    				}
    				else
    					$successorder = false;    					
    			}
    			//exit(1);
    			//
    			$order = new Itemspurchased;
    			$order->ordernumber = $_model->id;
    			$order->model = $data['modelid'][0];
    			$order->qty = $data['quantity'][0];
    			$order->price = str_replace(',','', $data['price'][0]);
    			$order->save();    			
    		}
    		//sample purchasing order
    		else {
    			foreach($data['quantity'] as $key=>$value)
    			{
    				if(!empty($data['description'][$key]))
    				{
    					$price = 0;
    					if(!empty($data['price'][$key]))
    						$price = $data['price'][$key];
    					$option_key = $i;
    					$qty = (int)$value;
    					for($i=0; $i<$qty;$i++){
    						//echo $i;
    						
	    					$item = new Item;
	    					$item->owner_id = Yii::$app->user->id;
	    					$item->purchaseordernumber = $_model->id;
	    					$item->model = $data['modelid'][$key];
	    					$item->status = array_search('In Transit', Item::$status);
	    					if($item->save()){
	    						$successorder = true;
	    						//track item
	    						$itemlog = new Itemlog;
	    						$itemlog->userid = Yii::$app->user->id;
	    						$itemlog->status = array_search('In Transit', Item::$status);
	    						$itemlog->itemid = $item->id;
	    						$itemlog->save();
	    					}
	    					else
	    						$successorder = false;
    					}//exit(1);
    					//
    					$order = new Itemspurchased;
    					$order->ordernumber = $_model->id;
    					$order->model = $data['modelid'][$key];
    					$order->qty = $qty;
    					$order->price = $price;   
    					$order->save();
    				}
    			}    			
    		}
    		
    		/*foreach($data['quantity'] as $key=>$value)
    		{
    			if(!empty($data['description'][$key]))
    			{
    				$price = 0;
    				if(!empty($data['price'][$key]))
    					$price = $data['price'][$key];
    				$option_key = $i;
    				//
    				$order = new Itemspurchased;
    				$order->ordernumber = $_model->id;
    				$order->model = $data['modelid'][$key];
    				$order->qty = $value;
    				$order->price = $price;
    				if($order->save())
    					$successorder = true;
    				else
    					$successorder = false;
    			}
    		}*/
    		//
    		if($successorder === true){
    			$_message = 'Purchase Order has been created successfully!';
    			Yii::$app->getSession()->setFlash('success', $_message);
    		} else{
    			$errors = json_encode($model->errors) . '<br/>' . json_encode($item->errors) . '<br/>' . json_encode($order->errors);
    			$_message = json_encode($errors);
    			Yii::$app->getSession()->setFlash('danger', $_message);
    		}
    		 
    		return $this->redirect(['index']);
    	} else {
    		return $this->render('create', [
	    				'model' => $_model,
	    				'ordernumber' => $ordernumber,
    					'item_requested' => $item_requested,
	    				'items_requested' => $items_requested,
	    				'count_itemsrequested' => $count_itemsrequested
    				]);
    	}
    }

    /**
     * Purchase Order number generation
     */
    private function generateNumber()
    {
    	$output = null;
    	$today = date('Y-m-d');
    	
    	$_currentuser = User::findOne(Yii::$app->user->id);
    	
    	$_initial = substr($_currentuser->firstname, 0, 1) . substr($_currentuser->lastname, 0, 1);
    	
    	//
    	$count = Purchase::find()->where(['user_id'=>Yii::$app->user->id])
    	->andWhere("date(created_at)= '$today'")->count();
    	//var_dump($count);exit(1);
    	//echo $today;
    	$count +=1;
    	
    	$_non_unique_number = $_initial . date('m') . date('d') . date('y');

    	$output =  $_non_unique_number . sprintf("%02d", $count);
    	
    	//
    	$find = Purchase::find()->where(['number_generated'=>$output])->one();
    	//var_dump($output);exit(1);
    	if ($find!==null){
			$last_order = Purchase::find()->where(['user_id'=>$_currentuser->id])->andWhere("date(created_at)= '$today'")->orderBy('number_generated DESC')->one();
			$last_order_number = str_replace($_non_unique_number, "", $last_order->number_generated);
			$last_order_number = (int) $last_order_number;
			$last_order_number +=1;
			//echo sprintf("%02d", $last_order_number);exit(1);
			$output =  $_non_unique_number . sprintf("%02d", $last_order_number);
    	}
    	return $output;
    }    
    
    /**
     *
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     * find customer model
     */
    protected function findModel($id) {
    	if (($model = Purchase::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}