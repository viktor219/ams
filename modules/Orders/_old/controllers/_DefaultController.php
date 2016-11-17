<?php

namespace app\modules\Orders\controllers;

use Yii;
use app\modules\Orders\models\Order;
use app\modules\Orders\models\OrderSearch;
use app\models\QOrder;
use app\models\QItemsordered;
use app\models\ItemHasOption;
use app\models\QItemHasOption;
use app\models\Purchasingitemrequest;
use app\models\Orderlog;
use app\models\QOrderlog;
use app\models\Ordertype;
use app\models\Location;
use app\models\ModelOption;
use app\models\User;
use app\models\ModelAssembly;
use app\models\Item;
use app\models\Itemlog;
use app\models\Itemsordered;
use app\models\Models;
use app\models\Customer;
use app\models\Manufacturer;
use app\models\Purchase;
use app\models\Partnumber;
use app\models\Medias;
use app\vendor\Uploader;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\vendor\PHelper; 
use yii\helpers\Html;
use app\models\SystemSetting;
use app\models\SalesorderMail;
use app\models\QsalesorderMail;
use app\models\Shipment;
use app\models\QShipment;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use app\models\UserHasCustomer;

class DefaultController extends Controller
{
	const _defaultCamSerialPath = "./uploads/camera/serials/";
	
	const _temp_PDF_PATH = "public/temp/pdf/salesorder/";
	
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
							'viewpicklist', 'qogenerate', 'ogenerate', 
							'qload', 'load', 'search', 'quotetoorder',
							'rview', 'savereceiveqty', 'manageconfigurations', 'addmodel',
							'markpickedtoship', 'turninprogressorship', 'departmentdeliver',
							'pick', 'rvalidate', 'loadmodelform', 'purchaseoptionsform',
							'addpurchaseoptions', 'loadconfigurationform', 'sendmail',
							'sendmailform', 'updatemodel', 'serialform', 'refurbishform',
							'picklistreadyform', 'picklistreadymodel', 'refurbish', 
							'validateserial', 'saveserial', 'addcustomer'
						],
		    	'rules' => [
		    		[
		    			//'actions' => ['index','create', 'update', 'view', 'delete', 'viewpicklist'],
		    			'actions' => [
							'index','create', 'update', 'view', 'delete', 
							'viewpicklist', 'qogenerate', 'ogenerate', 
							'qload', 'load', 'search', 'quotetoorder',
							'rview', 'savereceiveqty', 'manageconfigurations', 'addmodel',
							'markpickedtoship', 'turninprogressorship', 'departmentdeliver',
							'pick', 'rvalidate', 'loadmodelform', 'purchaseoptionsform',
							'addpurchaseoptions', 'loadconfigurationform', 'sendmail',
							'sendmailform', 'updatemodel', 'serialform', 'refurbishform',
							'picklistreadyform', 'picklistreadymodel', 'refurbish', 
							'validateserial', 'saveserial', 'addcustomer', 'getdeleted', 'revert', 'rdelete'			
						],
		    			'allow' => true,
		    			// Allow few users
		    			'roles' => [
		    				User::TYPE_ADMIN,
		    				User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
							User::REPRESENTATIVE
		    			],
		    		]
		    	],
	    	]
        ];
    }
	
	public function actions()
	{
		return [
			// declares "error" action
			'error' => 'yii\web\ErrorAction',
			
			// declares "view" action
			'index' => [
				'class' => 'app\modules\Orders\controllers\_actions\IndexAction',
			],			

			// declares "view" action
			'view' => [
				'class' => 'app\modules\Orders\controllers\_actions\ViewAction',
			],
			
			// declares "create" action 
			'create' => [
				'class' => 'app\modules\Orders\controllers\_actions\CreateAction',
			],	

			// declares "quote to order" action 
			'quotetoorder' => [
				'class' => 'app\modules\Orders\controllers\_actions\QuoteToOrderAction',
			],	

			// declares "search" action 
			'search' => [
				'class' => 'app\modules\Orders\controllers\_actions\SearchAction',
			],	
			
			// declares "load" action 
			'load' => [
				'class' => 'app\modules\Orders\controllers\_actions\LoadAction',
			],			
			
			// declares "qload" action 
			'qload' => [
				'class' => 'app\modules\Orders\controllers\_actions\QLoadAction',
			],
			
			// declares "update" action 
			'update' => [
				'class' => 'app\modules\Orders\controllers\_actions\UpdateAction',
			],
			
			// declares "order pdf" action 
			'ogenerate' => [
				'class' => 'app\modules\Orders\controllers\_actions\GenerateSalesOrderAction',
			],	

			// declares "quote order pdf" action
			'qogenerate' => [
				'class' => 'app\modules\Orders\controllers\_actions\GenerateQuoteSalesOrderAction',
			],			
		];
	}
	
	/*public function actionSview($requestcustomer)
	{
		$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`customer_id`'=>$requestcustomer])
								->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
						
		$dataProvider = new ActiveDataProvider([
					'query' => $query,
					'pagination' => ['pageSize' => 10],
				]);	

		return $this->render('_order', [
    				'dataProvider' => $dataProvider,
    				]);
	}*/
	
	public function actionCreaterma()
	{
		$data = Yii::$app->request->post();
		
		//var_dump($data); exit();
		
		if (!empty($data)) {
			ini_set('max_execution_time', 60);
			ini_set('memory_limit', '512M');
			date_default_timezone_set('US/Eastern');
			$customer = Customer::findOne(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid);
			//
			if(!empty($data['allowwarehouseorder']))
			{
				//warehouse order
				$purchasetype = 4;
				$shipby = date('Y-m-d', strtotime("+2 weekday"));
				$number_generated = $this->generateNumber($customer, $purchasetype, $shipby);
				$datetime = date('Y-m-d H:i:s');
				$successorder = false;
				$model = new Order();
				$model->customer_id = $customer->id;
				$model->location_id = $data['rma_location'];
				$model->type = 1;
				$model->ordertype =  $purchasetype;
				$model->shipby =  $shipby;
				///---- review
				$model->number_generated = $number_generated;
				$model->created_at = $datetime;
				if($model->save())
					$successorder = true;
				else 
					$successorder = false;
				//log order
				$orderlog = new Orderlog;
				$orderlog->orderid = $model->id;
				$orderlog->userid = Yii::$app->user->id;
				$orderlog->status = 1;
				$orderlog->save();		
				if(!empty($data['modelsqty']) && is_array($data['modelsqty']))
				{
					//unserialized items
					foreach($data['modelsqty'] as $key=>$value)
					{
						$_find_model = Models::findOne($data['modelsid'][$key]);
						$_data_model = $data['modelsid'][$key];	
						//
						$in_stock = Item::find()->where(['model'=>$_data_model, 'status'=>array_search('In Stock', Item::$status)])->count();
						$__qtyrequested = 0;
						if($value>$in_stock) {
							$__qtyrequested = $value-$in_stock;
						}
						if($__qtyrequested > 0) {

							for($i=0; $i<$__qtyrequested;$i++){
								$item = new Item; 
								$item->status = 1;
								$item->model = $_data_model;
								$item->ordernumber = $model->id;
								$item->customer = $model->customer_id;
								$item->location = $model->location_id;
								$item->received = $datetime;
								$item->lastupdated = $datetime;
								//$item->notes = $data["notes"];
								if($item->save()){
									//track item
									$itemlog = new Itemlog;
									$itemlog->userid = Yii::$app->user->id;
									$itemlog->status = 1;
									$itemlog->itemid = $item->id;
									$itemlog->locationid = $item->location;
									$itemlog->save();
									$successorder = true;
								}
							}
							//
							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_data_model])->all();
							foreach($_instock_items as $_instock_item) {
								$_instock_item->status = array_search('Shipped', Item::$status);
								$_instock_item->ordernumber = $model->id;
								$_instock_item->location = $model->location_id;
								if($_instock_item->save()){
									//track item
									$itemlog = new Itemlog;
									$itemlog->userid = Yii::$app->user->id;
									$itemlog->status = array_search('Shipped', Item::$status);
									$itemlog->itemid = $_instock_item->id;
									$itemlog->locationid = $_instock_item->location;
									$itemlog->save();
									$successorder = true;
								}
							}		        					
						} else {
							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_data_model])->limit($value)->all();
							foreach($_instock_items as $_instock_item) {
								$_instock_item->status = array_search('Shipped', Item::$status);
								$_instock_item->ordernumber = $model->id;
								$_instock_item->location = $model->location_id;
								if($_instock_item->save()){
									//track item
									$itemlog = new Itemlog;
									$itemlog->userid = Yii::$app->user->id;
									$itemlog->status = array_search('Shipped', Item::$status);
									$itemlog->itemid = $_instock_item->id;
									$itemlog->locationid = $_instock_item->location;
									$itemlog->save();
									$successorder = true;
								}
							}	        					
						}
						//
						$order = new Itemsordered;
						$order->customer = $model->customer_id;
						$order->qty = $value;
						$order->price = 0;
						//$order->package_optionid = $data['package_option'][$option_key][0];
						$order->model = $_data_model;
						$order->ordernumber = $model->id;
						$order->timestamp = $datetime;
						$order->status = array_search('Shipped', Item::$status);
						//$order->notes = $data['itemnotes'][$key];
						$order->ordertype = 4;
						$order->save();
					}
				}
				//
				if(!empty($data['itemsid']) && is_array($data['itemsid']))
				{
					foreach($data['itemsid'] as $key=>$_model)
					{
						//var_dump($data['itemsid'][$key]);
						$value = $data['itemsid'][$key];
						foreach($data['itemsid'][$key] as $_key=>$itemid)
						{
							//var_dump($itemid);
							$_find_item = Item::findOne($itemid);
							$_find_model = Models::findOne($_find_item->model);
							$_find_item->serial = (!empty($data['serials'][$key][$_key])) ? $data['serials'][$key][$_key] : $_existing_item->serial;
							$_find_item->confirmed = 1;
							$_find_item->tagnum = (!empty($data['tagnumber'][$key][$_key])) ? $data['tagnumber'][$key][$_key] : $_existing_item->tagnum;							
							$_find_item->customer = $model->customer_id;
							$_find_item->status = array_search('Shipped', Item::$status);
							$_find_item->ordernumber = $model->id;
							$_find_item->location = $data['rma_location'];
							if($_find_item->save()){
								//track item
								$itemlog = new Itemlog;
								$itemlog->userid = Yii::$app->user->id;
								$itemlog->status = array_search('Shipped', Item::$status);
								$itemlog->itemid = $_find_item->id;
								$itemlog->locationid = $data['rma_location'];
								$itemlog->save();
								$successorder = true;
							}
						}
						//
						//
						$order = new Itemsordered;
						$order->customer = $model->customer_id;
						$order->qty = count($value);
						$order->price = 0;
						//$order->package_optionid = $data['package_option'][$option_key][0];
						$order->model = $key;
						$order->ordernumber = $model->id;
						$order->timestamp = $datetime;
						$order->status = array_search('Shipped', Item::$status);
						//$order->notes = $data['itemnotes'][$key];
						$order->ordertype = 4;
						$order->save();
					}
				}
			}
			//service order.
        	$purchasetype = 2;
        	$shipby = date("Y-m-d", strtotime("+10 weekday"));
        	$number_generated = $this->generateNumber($customer, $purchasetype, $shipby);
        	$datetime = date('Y-m-d H:i:s');
        	$successorder = false;
			$model = new Order();
        	$model->customer_id = $customer->id;
        	$model->location_id = $data['rma_location'];
        	$model->type = 1;
        	$model->ordertype =  $purchasetype;
        	$model->shipby =  $shipby;
        	///---- review
        	$model->number_generated = $number_generated;
			$model->created_at = $datetime;
        	if($model->save())
        		$successorder = true;
        	else 
        		$successorder = false;
        	//log order
			$orderlog = new Orderlog;
        	$orderlog->orderid = $model->id;
        	$orderlog->userid = Yii::$app->user->id;
        	$orderlog->status = 1;
        	$orderlog->save();	
			
			//
			if(!empty($data['itemsid']) && is_array($data['itemsid']))
			{
				foreach($data['itemsid'] as $key=>$_model)
				{
					//var_dump($data['itemsid'][$key]);
					$value = $data['itemsid'][$key];
					foreach($data['itemsid'][$key] as $_key=>$itemid)
					{
						//var_dump($itemid);
						$_existing_item = Item::findOne($itemid);
						$_find_item = Item::find()->where(['customer'=>$model->customer_id, 'model'=>$key, 'status'=>[array_search('Shipped', Item::$status), array_search('Complete', Item::$status)]])->one();
						$_find_model = Models::findOne($_find_item->model);
						//$_find_existing_serial_item = Item::find()->where(['serial'=>$_find_item->serial])->count();
						$_find_item->customer = $customer->id;
						$_find_item->status = array_search('In Transit', Item::$status);
						$_find_item->serial = (!empty($data['serials'][$key][$_key])) ? $data['serials'][$key][$_key] : $_existing_item->serial;
						$_find_item->confirmed = 1;
						$_find_item->tagnum = (!empty($data['tagnumber'][$key][$_key])) ? $data['tagnumber'][$key][$_key] : $_existing_item->tagnum;
						$_find_item->ordernumber = $model->id;
						$_find_item->location = $model->location_id;
						if($_find_item->save()){
							//track item
							$itemlog = new Itemlog;
							$itemlog->userid = Yii::$app->user->id;
							$itemlog->status = array_search('In Transit', Item::$status);
							$itemlog->itemid = $_find_item->id;
							$itemlog->locationid = $model->location_id;
							$itemlog->save();
							$successorder = true;
						}
					}
					//
					//
					$order = new Itemsordered;
					$order->customer = $model->customer_id;
					$order->qty = count($value);
					$order->price = 0;
					//$order->package_optionid = $data['package_option'][$option_key][0];
					$order->model = $key;
					$order->ordernumber = $model->id;
					$order->timestamp = $datetime;
					$order->status = array_search('In Transit', Item::$status);
					//$order->notes = $data['itemnotes'][$key];
					$order->ordertype = 4;
					$order->save();
				}
			}			
			
        	if($successorder === true){
				$_message = 'RMA Order has been created successfully!';
        		Yii::$app->getSession()->setFlash('success', $_message);
        	} else{
        		$errors = json_encode($model->errors) . '<br/>' . json_encode($item->errors) . '<br/>' . json_encode($order->errors);
        		$_message = $errors;
        		Yii::$app->getSession()->setFlash('danger', $_message);        		
        	}
        	
            return $this->redirect(Yii::$app->request->referrer);			
		}
	}
	
    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionRview()
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
    		$html = $this->renderAjax('_rview', [
    				'model' => $find
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'id' => $id, 'title' => 'SO# ' . $find->number_generated);
    		echo json_encode($_retArray);
    		exit();
    	} else {
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
	
    public function actionSavereceiveqty($id)
    {
		//exit(1);
    	$order = $this->findModel($id);
    	
    	$errors;

    	//if (isset($_POST['receivingqty'])){
    		//$items = $_POST['items'];
    		//$qtys = $_POST['receivingqty'];
    		$success = false;
    		//
    		//foreach ($items as $key=>$item)
    		//{
    			///$qty = $qtys[$key];
    			//$rows = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'ordernumber'=>$order->id, 'model'=>$item])->all();
    			$rows = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'ordernumber'=>$order->id])->all();
    			$i = 0;
    			foreach($rows as $row) {
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
    				$i++;
    			}
    		//}
    	//}
    	//
    	if($success === true){
    		$_message = 'Receive Quantity has been updated successfully!';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
			if(!empty($errors)) {
				$_message = json_encode($errors);
				Yii::$app->getSession()->setFlash('danger', $_message);
			}
    	}
    	 
    	return $this->redirect(Yii::$app->request->referrer);    	
    }	
	
	public function actionViewpicklist($id)
	{
		
		$order = $this->findModel($id);
		
		$customer = Customer::findOne($order->customer_id);
		
		//create customer default capture when customer.requireserialphoto=1
		if($customer->requireserialphoto==1)
		{
			$customerDir = self::_defaultCamSerialPath . hash_hmac('sha256', $customer->id, Yii::$app->params['encryptionKey']);
			if(!file_exists($customerDir) && !is_dir($customerDir))
				mkdir($customerDir);
		}
		$title = 'Picklist';
		
		$dataProvider = new ActiveDataProvider([
				'query' => Item::find()
									->andFilterWhere(['trackingnumber'=>NULL])
									->andFilterWhere(['ordernumber' => $order->id])
									->groupBy('model'),
					'sort' => ['attributes' => ['partnumber']]
				]);		
        //$itemstoconverted = Itemsordered::find()->where("date(timestamp)='2016-04-26'")->all();
        
       /* foreach ($itemstoconverted as $itemtoconverted){
        	$item = Item::find()->where([
        			'ordernumber' => $itemtoconverted->ordernumber,
        			'status' => 1,
        			'serial' => NULL
        			])->one();
        	
        	$qty = $itemtoconverted->qty;
        	
        	for($i=0; $i<$qty;$i++){
        		$model = new Item;
        		$model->status = 1;
        		$model->model = $itemtoconverted->model;
        		$model->customer = $itemtoconverted->customer;
        		if(!$item)
        			$model->location = $item->location; 
        		else 
        			$model->location = 311;
        		$model->ordernumber = $itemtoconverted->ordernumber;
        		$model->created_at = $itemtoconverted->timestamp;
        		$model->save();
        	}
        }
        */
        /*$items = Item::find()->where([
				'ordernumber' => $id,
				'status' => 3,
        		'serial' => NULL
			])->asArray()->all();*/

        return $this->render('_listitem', [
            'dataProvider' => $dataProvider,
			'title' => $title,
        	'order' => $order
        ]);		
	}
	
	public function actionManageconfigurations($id=null)
	{
		$data = Yii::$app->request->post();
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			//find option
			if(!empty($id)) {
				$model = ModelOption::findOne($id);
			}
			if($model === null)
				$model = new ModelOption;
			//
			if(!empty($id)) {
				foreach(ModelOption::findAll(['parent_id'=>$id, 'optiontype'=>2, 'idmodel'=>$data['idmodel']]) as $_option)
				{
					$_option->delete();
				}
			}
			//save parent option
			$model->name = $data['optionparentname'];
			$model->optiontype = 2;
			$model->idmodel = $data['idmodel'];
			$model->level = 1;
			$model->parent_id = 0;
			$model->checkable = 0;
			if ($model->save()) {
				foreach($data['options'] as $option)
				{
					$childmodel = new ModelOption;;
					$childmodel->name = $option;
					$childmodel->optiontype = 2;
					$childmodel->idmodel = $data['idmodel'];
					$childmodel->level = 1;
					$childmodel->parent_id = $model->id;
					$childmodel->checkable = 1;	
					if($childmodel->save())			
						$html = "Option has been added successfully";
					else 
						$html .= $childmodel->errors;
				}
			}
			if(empty($html))
				$html = json_encode($html);
			$_retArray = array('success' => true, 'html' => $html);
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}		
	}
	
	public function actionAddmodel()
	{
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			
			$model = new Models;
			
			$data = Yii::$app->request->post('Models');
			
			$model->category_id = $data['category'];
			$model->manufacturer = $data['manufacturer'];
			$model->descrip = $data['descrip'];
			$model->aei = $data['aei'];
			$model->serialized = $data['serialized'];
			$model->purchasepricing = $data['purchasepricing'];
			$model->repairpricing = $data['repairpricing'];
			$model->frupartnum = $data['frupartnum'];
			$model->manpartnum = $data['manpartnum'];
			$model->prefered_vendor = $data['prefered_vendor'];
			$model->secondary_vendor = $data['secondary_vendor'];
			$model->reorderqty = $data['reorderqty'];
			$model->palletqtylimit = $data['palletqtylimit'];
			$model->stripcharacters = $data['stripcharacters'];
			$model->checkit = $data['checkit'];
			$model->charactercount = $data['charactercount'];
			
			if ($model->save()) 
			{
				PHelper::generateModelsJson($model->id);
				$html = "Model has been added successfully";
			}
			
			$_retArray = array('success' => true, 'html' => $html, 'id' => $model->id);
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
	}
	
	public function actionMarkpickedtoship($id)
	{
		$order = $this->findModel($id);
		$items = Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])->all();
		foreach($items as $item)
		{
			$item->status = array_search('In Shipping', Item::$status);
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('In Shipping', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $item->location;
				$itemlog->save();				
			}			
		}
		return $this->redirect(Yii::$app->request->referrer);
	}
	
	/*public function actionMarkallpickedtoship($id)
	{
		$order = $this->findModel($id);
		$items = Item::find()
						->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
						->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
						//->andWhere(['requiretestingreferb'=>0, 'preowneditems'=>0])
						->all();
		foreach($items as $item)
		{
			$item->status = array_search('In Shipping', Item::$status);
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('In Shipping', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->save();				
			}			
		}
		return $this->redirect(Yii::$app->request->referrer);
	}*/
	
	public function actionTurninprogressorship($id)//two steps : 
	{
		$order = $this->findModel($id);
		if($order->ordertype==1) { //purchase order
			//cleaning & testing options => into inprogress
			$items = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
								->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
								->andwhere(['orderid'=>$order->id])->all();
			foreach($items as $item)
			{
				$item->status = array_search('In Progress', Item::$status);
				if($item->save())
				{
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('In Progress', Item::$status);
					$itemlog->itemid = $item->id;
					$itemlog->locationid = $item->location;
					$itemlog->save();				
				}			
			}
		} else if($order->ordertype==4) { //warehouse order
			/**
			 * check Are pre-owned/used items of this model sent through the cleaning department before the service labs? items
			 * check Does this model require testing before refurbishing is completed? items 
			 * which is not overrided.
			 */
			$overrideitems = \yii\helpers\ArrayHelper::getColumn(ItemHasOption::find()->where(['orderid'=>$order->id])->groupBy('itemid')->asArray()->all(), 'itemid');
			$items = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
								 ->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
								 ->andwhere(['preowneditems'=>1, 'requiretestingreferb'=>1])
								 ->andWhere(['not in ', 'model', $overrideitems])
								 ->all();			
			foreach($items as $item)
			{
				$item->status = array_search('In Progress', Item::$status);
				if($item->save())
				{
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('In Progress', Item::$status);
					$itemlog->itemid = $item->id;
					$itemlog->locationid = $item->location;
					$itemlog->save();				
				}			
			}
			/**
			 * Warehouse items overrided except 'as-is' (cleaning=1, testing=40)
			 */
			$items = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
								->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
								->andWhere(['<>', 'optionid', 1])
								->andWhere(['<>', 'optionid', 40])
								->andwhere(['orderid'=>$order->id])->all();	
			foreach($items as $item)
			{
				$item->status = array_search('In Progress', Item::$status);
				if($item->save())
				{
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('In Progress', Item::$status);
					$itemlog->itemid = $item->id;
					$itemlog->locationid = $item->location;
					$itemlog->save();				
				}			
			}								
		}
		//others items In Shipping
		$items = Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])->all();
		foreach($items as $item)
		{
			$item->status = array_search('In Shipping', Item::$status);
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('In Shipping', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $item->location;
				$itemlog->save();				
			}			
		}
		return $this->redirect(Yii::$app->request->referrer); 
	}
	
	public function actionDepartmentdeliver($id)
	{
		$order = $this->findModel($id);
		$items = Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])->all();
		foreach($items as $item)
		{
			$item->status = array_search('In Progress', Item::$status);
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('In Progress', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $item->location;
				$itemlog->save();				
			}			
		}
		return $this->redirect(Yii::$app->request->referrer);
	}
	
	public function actionPick($id, $oid)
	{//only pick quantity in the stock
		$model = Models::findOne($id);
		$order = $this->findModel($oid);
		//var_dump($model->id, $order->id);
		if($order->ordertype!==1)
			$customer = $order->customer_id;
		else
			$customer = 4;	
		$success = false;
		$errors = null;
		//find order quantity 
		$qty = Itemsordered::find()->where(['ordernumber'=>$order->id, 'model'=>$model->id])->one()->qty;
		//find reserved quantity
		$qty_reserved = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$model->id, 'status'=>array_search('Reserved', Item::$status)])->count();
		$qty_instock = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer])->count();
		$qty_instock_topick = 0;
		$qty_reservedto_pick = 0;
		if($qty_reserved < $qty)
		{
			$qty_reservedto_pick = $qty_reserved; 
			$qty_instock_topick = $qty - $qty_reserved; 
		}
		else if($qty_reserved==$qty)
			$qty_reservedto_pick = $qty;
		else 
			$qty_reservedto_pick = $qty_reserved - $qty;
		//find reserved items first
		if($qty_reservedto_pick > 0) 
		{
			$reserved = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$model->id, 'status'=>array_search('Reserved', Item::$status)])->limit($qty_reservedto_pick)->all();
			//var_dump($reserved);exit(1);
			foreach($reserved as $reserv)//epuiser la quantité réservée
			{
				$reserv->ordernumber = $order->id;
				$reserv->status = array_search('Picked', Item::$status);
				$reserv->picked = date('Y-m-d H:i:s');
				if($reserv->save())
				{
					$success = true;
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('Picked', Item::$status);
					$itemlog->itemid = $reserv->id;
					$itemlog->locationid = $reserv->location;
					$itemlog->save();				
				} else 
					$errors .= $reserv->errors;
			}
		}
		//
		if($qty_instock_topick > 0)
		{
			//find instock items now
			$instocks = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer])->limit($qty_instock_topick)->all();
			foreach($instocks as $instock)
			{
				$instock->ordernumber = $order->id;
				$instock->status = array_search('Picked', Item::$status);
				$instock->picked = date('Y-m-d H:i:s');
				if($instock->save())
				{
					$success = true;
					//track item
					$itemlog = new Itemlog;
					$itemlog->userid = Yii::$app->user->id;
					$itemlog->status = array_search('Picked', Item::$status);
					$itemlog->itemid = $instock->id;
					$itemlog->locationid = $instock->location;
					$itemlog->save();				
				} else 
					$errors .= $instock->errors;
			}
		}
		if($success === true){
			$_message = 'Quantity has been successfully picked';
			Yii::$app->getSession()->setFlash('success', $_message);
		} else {
			$_message = json_encode($errors);
			Yii::$app->getSession()->setFlash('danger', $_message);
		}		
		return $this->redirect(['viewpicklist', 'id'=>$order->id]);
	}
	
	public function actionRvalidate()
	{
		$data = Yii::$app->request->post();
		
    	$success = false;
    	
    	$errors;
		//var_dump($data);
		//exit(1);
		$qty = (int)$data['rqty'];
		$orderid = $data['rorder_id'];
		$order = Order::findOne($orderid);
		$itemid = $data['ritem_id'];
		$modelid = $data['rmodel_id'];
		$item = Item::findOne($itemid);
		//$order = Order::findOne($order_id);
		for($i=0; $i<$qty;$i++){			
			$model = new Item;
			$model->owner_id = Yii::$app->user->id;
			$model->status = array_search('Requested', Item::$status);
			$model->model = $modelid;
			$model->ordernumber = $orderid;
			$model->customer = $item->customer;
			$model->location = $item->location;
			if($model->save()) {
				$success = true;
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('Requested', Item::$status);
				$itemlog->itemid = $model->id;
				$itemlog->locationid = $order['location_id'];
				$itemlog->save();				
			}
			else 
				$errors .= $model->errors;
		}
		/*$model = new Purchasingitemrequest;
		$model->requestby = Yii::$app->user->id;
		$model->qty = $qty;
		$model->item = $item->id;
		$model->ordernumber = $item->ordernumber;*/
		if($success === true){
			$_message = 'Your request has been successfully executed!';
			Yii::$app->getSession()->setFlash('success', $_message);
		} else {
			$_message = json_encode($errors);
			Yii::$app->getSession()->setFlash('danger', $_message);
		}
		//
		//return $this->redirect(['/orders/viewpicklist', 'id'=>$item->ordernumber]);
		return $this->redirect(Yii::$app->request->referrer);
	}
	
	/*public function actionStartorder($id)
	{
		$title = 'Start Order';
		
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->select('model')->distinct(),
        ]);

        return $this->render('_listitem', [
            'dataProvider' => $dataProvider,
			'title' => $title
        ]);			
	}*/

	public function actionLoadmodelform()
	{
		$_post = Yii::$app->request->post();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			$customerId = $_post['customerid'];
			//
			$model = Models::findOne($id);
			
			$manufacturer = Manufacturer::findOne($model->manufacturer);
			
			$customer = Customer::findOne($customerId);
							
			$html = $this->renderAjax('_editmodelform', [
							'model'=>$model,
							'manufacturer'=>$manufacturer,
							'customer'=>$customer
						]);
			$_retArray = array('success' => true, 'html' => $html, 'itemname'=>$manufacturer->name . ' ' . $model->descrip);
			echo json_encode($_retArray);
			exit();
		}		
	}
	
	public function actionPurchaseoptionsform()
	{
		$_post = Yii::$app->request->get();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['order']) || !isset($_post['model'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$orderid = $_post['order'];
			$order = $this->findModel($orderid);
			$modelid = $_post['model'];
			//
			$model = Models::findOne($modelid);
			
			$manufacturer = Manufacturer::findOne($model->manufacturer);
										
			$html = $this->renderAjax('_purchaseoptionsform', [
							'model'=>$model,
							'manufacturer'=>$manufacturer,
							'order'=>$order
						]);
			$_retArray = array('success' => true, 'html' => $html, 'itemname'=>$manufacturer->name . ' ' . $model->descrip);
			echo json_encode($_retArray);
			exit();
		}				
	}
	
	public function actionDeleted(){
		$query = Order::find()->select('lv_salesorders.*')
					   ->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
						//->where(['lv_salesorders.ordertype' => Ordertype::findOne(['name'=>$type])->id])
					   ->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.deleted' => 1]);
		if($customerid != 0) 
			$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
		$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
	}
	
	public function actionAddpurchaseoptions()
	{
		$_post = Yii::$app->request->post();
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			$orderid = $_post['orderId'];
			$modelid = $_post['modelId'];
			$success = false;
			
			if(!empty($_post['cleaning_option']))
			{
				$cleaning = ItemHasOption::find()->innerJoin('lv_model_options', '`lv_model_options`.`id` = `lv_item_has_option`.`optionid`')
					->where(['orderid'=>$orderid])
					->andWhere(['itemid'=>$modelid])
					->andWhere(['optiontype'=>1, 'parent_id'=>0])
					->one();
				if($cleaning===null)
					$cleaning = new ItemHasOption;
				$cleaning->orderid = $orderid;
				$cleaning->itemid = $modelid;
				$cleaning->optionid = $_post['cleaning_option'];
				if($cleaning->save())
					$success = true;
			}
			//
			if(!empty($_post['testing_option']))
			{
				$testing = ItemHasOption::find()->innerJoin('lv_model_options', '`lv_model_options`.`id` = `lv_item_has_option`.`optionid`')
					->where(['orderid'=>$order->id])
					->andWhere(['itemid'=>$model->id])
					->andWhere(['optiontype'=>3, 'parent_id'=>0])
					->one();
				if($testing===null)
					$testing = new ItemHasOption;
				$testing->orderid = $orderid;
				$testing->itemid = $modelid;
				$testing->optionid = $_post['testing_option'];
				if($testing->save())
					$success = true;
			}
			//
			if ($success) 
				$html = "Option has been updated successfully";
			
			$_retArray = array('success' => true, 'html' => $html);
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}		
	}
	
	public function actionLoadconfigurationform($optionid = null)
	{
		$_post = Yii::$app->request->get();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			$row = $_post['row'];
			//
			$model = Models::findOne($id);
			
			$manufacturer = Manufacturer::findOne($model->manufacturer);
			if(empty($optionid)) {
				$html = $this->renderAjax('_newconfigurationform', [
								'model'=>$model,
								'manufacturer'=>$manufacturer,
								'row'=>$row
							]);
			} else {
				$option = ModelOption::findOne($optionid);
				$html = $this->renderAjax('_newconfigurationform', [
								'model'=>$model,
								'manufacturer'=>$manufacturer,
								'row'=>$row,
								'option'=>$option
							]);				
			}
			$_retArray = array('success' => true, 'html' => $html);
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();	
		}
	}
	
	public function actionSendmail()
	{
		//if (Yii::$app->request->isAjax) {
			//
			$_retArray = array('success' => FALSE, 'message' => '');
			
			$_post = Yii::$app->request->post(); 

			$session = Yii::$app->session;
			
			$tomail = Yii::$app->params['supportEmail'];
			
			$additional_address = split(";", $_post['cc']);
			
			if(isset($_post['orderId']))
			{
				$model = $this->findModel($_post['orderId']);
				$filename = self::_temp_PDF_PATH . $session['__temp_order_pdf_generated'];
			}
			else if(isset($_post['qorderId']))	
			{
				$model = $this->findQModel($_post['qorderId']);
				$filename = self::_temp_PDF_PATH . $session['__temp_qorder_pdf_generated'];
			}
			
			$customer = Customer::findOne($model->customer_id);
			
			$success = false;
			
			$error = 0;
			
			$body = $this->renderPartial('_ordermailtemplate', ['model' => $model, 'customer'=>$customer, 'content'=>$_post['body']]);
			
			//main email validation
			if(filter_var($tomail, FILTER_VALIDATE_EMAIL) !== false)
			{			
				$mail = Yii::$app->mailer->compose()
							->setFrom([Yii::$app->params['adminEmail'] => 'Matthew Ebersole'])
							->setTo($tomail)
							->setSubject($_post['subject'])
							->setHtmlBody($body)
							->attach($filename);		
				if(!empty($model->orderfile))
					$mail->attach(Yii::getAlias('@webroot') . "/uploads/orders/" . Medias::findOne($model->orderfile)->filename);
				//
				$mail->send();
					$success = true;
				//
				if(isset($_post['orderId']))
				{
					$mail = new SalesorderMail;
					$mail->orderid = $_post['orderId'];
				}
				else if(isset($_post['qorderId']))	
				{
					$mail = new QsalesorderMail;
					$mail->orderid = $_post['qorderId'];
				}
				//track mail sent
				$mail->email = $tomail;
				$mail->save();
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
									->setHtmlBody($body)
									->attach($filename);
						//
						if(!empty($model->orderfile))
							$mail->attach(Yii::getAlias('@webroot') . "/uploads/orders/" . Medias::findOne($model->orderfile)->filename);								
						//
						$mail->send();
							$success = true;
						//
						if(isset($_post['orderId']))
						{
							$mail = new SalesorderMail;
							$mail->orderid = $_post['orderId'];
						}
						else if(isset($_post['qorderId']))	
						{
							$mail = new QsalesorderMail;
							$mail->orderid = $_post['qorderId'];
						}
						//track mail sent
						$mail->email = $tomail;
						$mail->save();				
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
	
	public function actionQsendmailform()
	{
		//if (Yii::$app->request->isAjax) {
			$_post = Yii::$app->request->get();
			
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			
			$model = $this->findQModel($id);
			
			$session = Yii::$app->session;
		//remove old file generated
			$old_file_generated = self::_temp_PDF_PATH . $session['__temp_qorder_pdf_generated'];
			if(!is_dir($old_file_generated) && file_exists($old_file_generated))
				unlink($old_file_generated);	
		//set new file 
			$newfile = $this->generateLocalQOrder($id);
			
			$session->set('__temp_qorder_pdf_generated', $newfile);					
			
		//find customer to send mail
			$customer = Customer::findOne($model->customer_id);
			
			$html = $this->renderAjax('_sendmailform', ['model'=>$model, 'current_file'=>$newfile, 'customer'=>$customer, 'type'=>2]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();			
		//}		
	}
	
	public function actionSendmailform()
	{
		//if (Yii::$app->request->isAjax) {
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
			$old_file_generated = self::_temp_PDF_PATH . $session['__temp_order_pdf_generated'];
			if(!is_dir($old_file_generated) && file_exists($old_file_generated))
				unlink($old_file_generated);	
		//set new file 
			$newfile = $this->generateLocalOrder($id);
			
			$session->set('__temp_order_pdf_generated', $newfile);					
			
		//find customer to send mail
			$customer = Customer::findOne($model->customer_id);
			
			$html = $this->renderAjax('_sendmailform', ['model'=>$model, 'current_file'=>$newfile, 'customer'=>$customer, 'type'=>1]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();			
		//}
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
    		.header_pdf th {background: none;color: #333;font-size:32px;}
    		.header_pdf tr{border:none;}
    		#sr_addresses tr{border:1px solid white;}
    		.header_pdf td{font-size:20px;}
    		.header_pdf_right{float: right;}
    		#shipping_methods tr {border:2px solid silver;}
    		#products tr {border:2px solid silver;}
    		.header_pdf tr{float:left;}
    		#shipping_methods td, #shipping_methods th, #products th {text-align:center;}
    		#products td {padding-right: 8px;}
    		table tr.no-border-row {border-bottom: none;} 
    		.pair-row {background: #BBB;}   		
    		.align_right {text-align:right;}
    		.align_left {text-align:left;}	
    		.border{border:1px solid silver;}
    		.no_border{border:none;}
    	";
		
		//$filename = base64_encode(uniqid().time()) . '.pdf';
	
		if(empty($model->number_generated))
		{
			if(!empty($location->storenum))
				$name .= "Store#: " . $location->storenum;
			else
				$name .= $location->storename; 
			//
			$name .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;								
		}else
			$name = $model->number_generated;
							
		$filename =  $name . '.pdf';
		
		$targetfile = self::_temp_PDF_PATH . $filename ;
		
		$footer_content = $this->renderPartial('_generate_footer');
    	
    	$pdf = Yii::$app->pdf;
    	    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1);
		
		$mpdf->WriteHTML($content, 2);
    	
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated); 
    	
    	$mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	
    	$mpdf->SetFooter("|$footer_content|");		
		
		$mpdf->Output($targetfile, 'F');
		
		return $filename;
	}
	
	private function generateLocalQOrder($id)
	{
    	$model = QOrder::findOne($id);
    	
    	$shipment = QShipment::find()->where(['orderid'=>$model->id])->one();
    	
    	$shipping_method = ShipmentMethod::findOne($shipment->shipping_deliverymethod);
    	
    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);
    	
    	$customer = Customer::findOne($model->customer_id);
    	
    	$assetCustomer = Customer::findOne(4);
    	
    	$location = Location::findOne($model->location_id);
    	
    	$assetLocation = Location::findOne($assetCustomer->defaultshippinglocation);
    	
    	$_media_customer = Medias::findOne($customer->picture_id);
    	
    	$itemsordered = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
    	
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
    		.header_pdf th {background: none;color: #333;font-size:32px;}
    		.header_pdf tr{border:none;}
    		#sr_addresses tr{border:1px solid white;}
    		.header_pdf td{font-size:20px;}
    		.header_pdf_right{float: right;}
    		#shipping_methods tr {border:2px solid silver;}
    		#products tr {border:2px solid silver;}
    		.header_pdf tr{float:left;}
    		#shipping_methods td, #shipping_methods th, #products th {text-align:center;}
    		#products td {padding-right: 8px;}
    		table tr.no-border-row {border-bottom: none;} 
    		.pair-row {background: #BBB;}   		
    		.align_right {text-align:right;}
    		.align_left {text-align:left;}	
    		.border{border:1px solid silver;}
    		.no_border{border:none;}
    	";
		
		if(empty($model->number_generated))
		{
			if(!empty($location->storenum))
				$name .= "Store#: " . $location->storenum;
			else
				$name .= $location->storename; 
			//
			$name .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;								
		}else
			$name = $model->number_generated;
		
		$filename = "$name.pdf";
		
		$targetfile = self::_temp_PDF_PATH . $filename ;
		
		$footer_content = $this->renderPartial('_generate_footer');
    	
    	$pdf = Yii::$app->pdf;
    	    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1);
		
		$mpdf->WriteHTML($content, 2);
    	
    	$mpdf->SetTitle('Generate SO#: ' . $model->number_generated);
    	
    	$mpdf->SetHeader(Ordertype::findOne($model->ordertype)->name . ' Order||SO#: ' . $model->number_generated);
    	
    	$mpdf->SetFooter("|$footer_content|");		
		
		$mpdf->Output($targetfile, 'F');
		
		return $filename;
	}	
	
	public function actionUpdatemodel($id)
	{
		$_post = Yii::$app->request->post();
		
		$model = Models::findOne($id);
		
		if ($model===null) {
			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
			echo json_encode($_retArray);
			exit();
		}
		
		$model->stripcharacters = $_post['stripcharacters'];
		$model->checkit = $_post['checkit'];
		$model->charactercount = $_post['charactercount'];
		$model->descrip = $_post['description'];
		$model->manufacturer = $_post['manufacturer'];
		$model->serialized = $_post['serialized'];
		$model->category_id = $_post['category'];
		$model->department = $_post['department'];
		$model->aei = $_post['aei'];
		$model->frupartnum = $_post['frupartnum'];
		$model->manpartnum = $_post['manpartnum'];
		if($model->save())
			$_message = "Model was successfully updated!";
		//save partnumbers...
		foreach($_post['editModelCustomerval'] as $key=>$value)
		{
			if(!empty($_post['partid'][$key]) || !empty($_post['partdesc'][$key]))
			{
				$_model = Partnumber::find()->where(['customer'=>$value, 'model'=>$id])->one();
				//
				if($_model===null)
					$_model = new Partnumber;
				//
				$_model->customer = $value;
				$_model->model = $id;
				$_model->partid = $_post['partid'][$key];
				$_model->partdescription = $_post['partdesc'][$key];
				$_model->save();
			}
		}
		//
		PHelper::updateModelsJson($model->id);
		//$_retArray = array('success' => true, 'html' => json_encode($_post));
		$_retArray = array('success' => true, 'html' => $_message);
		echo json_encode($_retArray);
		exit();		
	}
	
	public function actionSerialform()
	{
		$_post = Yii::$app->request->get();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id']) || !isset($_post['item'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			//
			$order = Order::findOne($id);
 
			/*if(!isset($_post['next']))
				$item = Item::findOne($_post['item']);
			else */
				/*$item = Item::find()->where(['>', 'id', $_post['item']])
									->andWhere(['ordernumber'=>$id])
									->one();*/
			$item = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item']])->andWhere(['not', ['status' => array_search('Picked', Item::$status)]])->one();
				
			$html = $this->renderAjax('_serialform', [
						'item'=>$item,
						'customer'=>Customer::findOne($order->customer_id),
						'order'=>$order
						]);
			//$_retArray = array('success' => true, 'html' => $html, 'itemserialized'=> Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item'], 'status' => array_search('Picked', Item::$status)])->andWhere(['not', ['serial' => null]])->count(), 'itemserializedornot'=>Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item'], 'status' => array_search('Picked', Item::$status)])->andWhere(['not', ['serial' => null]])->count() + Item::find()->where(['model'=>$_post['item'], 'status'=>array_search('In Stock', Item::$status)])->count());
			//$_retArray = array('success' => true, 'html' => $html, 'itemserialized'=> Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item'], 'status' => array_search('Picked', Item::$status)])->andWhere(['not', ['serial' => null]])->count(), 'itemserializedornot'=>Itemsordered::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item']])->one()->qty);
			$_retArray = array('success' => true, 'html' => $html, 'itemserialized'=> Item::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item']])->andWhere(['>=', 'status', array_search('Picked', Item::$status)])->count(), 'itemserializedornot'=>Itemsordered::find()->where(['ordernumber'=>$order->id, 'model'=>$_post['item']])->one()->qty);
			echo json_encode($_retArray);
			exit();
		}
	
	}
	
	public function actionPickingconfirmform()
	{
		$_post = Yii::$app->request->get();
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['item'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			
			$item = Item::findOne($_post['item']);
			//
			$order = Order::findOne($item->ordernumber);
			$model = Models::findOne($item->model);
			//
			
			$html = $this->renderAjax('_pickingconfirmform', [
						'model'=>$model,
						'order'=>$order
					]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();
		}		
	}
	
	public function actionRefurbishform()
	{
		$_post = Yii::$app->request->get();
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['order']) || !isset($_post['model'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['order'];
			$modelid = $_post['model'];
			//
			$order = Order::findOne($id);
			$model = Models::findOne($modelid);
			//
			
			$html = $this->renderAjax('_refurbishform', [
						'model'=>$model,
						'order'=>$order
						]);
			$_retArray = array('success' => true, 'html' => $html);
			echo json_encode($_retArray);
			exit();
		}
	}
	
	public function actionPicklistreadyform()
	{
		$_post = Yii::$app->request->get();
		//if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['id'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$id = $_post['id'];
			//
			$order = Order::findOne($id);
			//
			//$items = Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])->groupBy('model')->all();
			
			$delivercleaningitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['ordernumber'=>$order->id, 'orderid'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'optionid' => [2,3], 'conditionid'=>4])
										->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'preowneditems'=>1, 'conditionid'=>4]);
			
			$cleaningmodels = \yii\helpers\ArrayHelper::getColumn($delivercleaningitems->groupBy('model')->asArray()->all(), 'model');
			
			$_countcleaningitems = $delivercleaningitems->count();
			
			$delivertestingitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'orderid'=>$order->id, 'optionid' => [47,48]])
										->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'requiretestingreferb'=>1, 'conditionid'=>4])
										->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'conditionid'=>2])
										->andWhere(['not', ['model'=>$cleaningmodels]]);
										
			$testingmodels = \yii\helpers\ArrayHelper::getColumn($delivertestingitems->groupBy('model')->asArray()->all(), 'model');
			
			$_counttestingitems = $delivertestingitems->count();
			
			$merged_cleaning_testing = array_merge($cleaningmodels, $testingmodels);
			
			$delivertoshippingitems = Item::find()
											->where(['conditionid' => [1, 3, 4], 'ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
											->andWhere(['not', ['model'=>array_merge($merged_cleaning_testing)]]);
			
			$_countshippingitems = $delivertoshippingitems->count();			
			
			$_totalcount = $_countshippingitems + $_countcleaningitems + $_counttestingitems;
			
			$delivertoshippingitems = $delivertoshippingitems->groupBy('model')->all();
			
			$delivercleaningitems = $delivercleaningitems->groupBy('model')->all();
			
			$delivertestingitems = $delivertestingitems->groupBy('model', 'department')->all();
			
			$_verificationcount = count($delivertoshippingitems) + count($delivercleaningitems) + count($delivertestingitems);
			//
			$html = $this->renderPartial('_picklistreadyform', [
							'order'=>$order,
							'delivertoshippingitems' => $delivertoshippingitems,
							'_countshippingitems' => $_countshippingitems,
							'delivercleaningitems' => $delivercleaningitems,
							'_countcleaningitems' => $_countcleaningitems,
							'delivertestingitems' => $delivertestingitems,
							'_counttestingitems' => $_counttestingitems,
							'_totalcount' => $_totalcount
						]);
			$_retArray = array('success' => true, 'html' => $html, 'verificationcount' => $_verificationcount);
			echo json_encode($_retArray);
			exit();
		//}		
	}
	
	public function actionPicklistreadymodel()
	{
		$_post = Yii::$app->request->get();
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['type']) || !isset($_post['itemid'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			
			$type = $_post['type'];
			$itemid = $_post['itemid'];
			if($itemid != 0)
				$model = Item::findOne($itemid);
			$orderid = (int) $_post['orderid'];
			
			//shipping
			if($type == 1 || $type == 0)
			{
				$query = Item::find()->where(['status'=>array_search('Picked', Item::$status), 'conditionid' => [1, 3]]);
				
				if($orderid == 0 )
					$items = $query->andWhere(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->all();
				else 
					$items = $query->andWhere(['ordernumber'=>$orderid])->all();
				//
				foreach($items as $item)
				{
					$item->status = array_search('In Shipping', Item::$status);
					if($item->save())
					{
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('In Shipping', Item::$status);
						$itemlog->itemid = $item->id;
						$itemlog->locationid = $item->location;
						$itemlog->save();				
					}			
				}							
			}		
			
			//cleaning
			if($type == 2 || $type == 0) 
			{			
				//cleaning options => into inprogress
				$query = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
									->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`');
				if($orderid == 0 )
					$items = $query->where(['ordernumber'=>$model->ordernumber, 'orderid'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status), 'optionid' => [2,3], 'conditionid'=>4])
									->orWhere(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status), 'preowneditems'=>1, 'conditionid'=>4])
									->all();	
				else 
					$items = $query->where(['ordernumber'=>$orderid, 'orderid'=>$orderid, 'status'=>array_search('Picked', Item::$status), 'optionid' => [2,3], 'conditionid'=>4])
									->orWhere(['ordernumber'=>$orderid, 'status'=>array_search('Picked', Item::$status), 'preowneditems'=>1, 'conditionid'=>4])
									->all();					
				
				foreach($items as $item)
				{
					$item->status = array_search('In Progress', Item::$status);
					if($item->save())
					{
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('In Progress', Item::$status);
						$itemlog->itemid = $item->id;
						$itemlog->locationid = $item->location;
						$itemlog->save();				
					}					
				}	
			}   
			
			//testing
			if($type == 3 || $type == 0) 
			{
				$query = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
									->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`');
				if($orderid == 0 )
					$items = $query->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status), 'orderid'=>$model->ordernumber, 'optionid' => [47,48]])
										->orWhere(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status), 'requiretestingreferb'=>1, 'conditionid'=>4])
										->orWhere(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status), 'conditionid'=>2])
										->all();		
				else 
					$items = $query->where(['ordernumber'=>$orderid, 'status'=>array_search('Picked', Item::$status), 'orderid'=>$orderid, 'optionid' => [47,48]])
										->orWhere(['ordernumber'=>$orderid, 'status'=>array_search('Picked', Item::$status), 'requiretestingreferb'=>1, 'conditionid'=>4])
										->orWhere(['ordernumber'=>$orderid, 'status'=>array_search('Picked', Item::$status), 'conditionid'=>2])
										->all();					
											
				foreach($items as $item)
				{
					$item->status = array_search('In Progress', Item::$status);
					if($item->save())
					{
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('In Progress', Item::$status);
						$itemlog->itemid = $item->id;
						$itemlog->locationid = $item->location;
						$itemlog->save();				
					}					
				}					
			}
		}
		//
			$_retArray = array('success' => true);
			echo json_encode($_retArray);
			exit();
	}
	
	public function actionDeliveritems($id)
	{
		$order = Order::findOne($id);

		$delivertoshippingitems = Item::find()->where(['conditionid' => [1, 3], 'ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)]);
		
		$_countshippingitems = $delivertoshippingitems->count();
		
		$delivercleaningitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
									->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
									->where(['ordernumber'=>$order->id, 'orderid'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'optionid' => [2,3], 'conditionid'=>4])
									->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'preowneditems'=>1, 'conditionid'=>4]);
		
		$cleaningmodels = \yii\helpers\ArrayHelper::getColumn($delivercleaningitems->groupBy('model')->asArray()->all(), 'model');
		
		$_countcleaningitems = $delivercleaningitems->count();
		
		$delivertestingitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
									->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
									->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'orderid'=>$order->id, 'optionid' => [47,48]])
									->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'requiretestingreferb'=>1, 'conditionid'=>4])
									->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'conditionid'=>2])
									->andWhere(['not', ['model'=>$cleaningmodels]]);
		
		$_counttestingitems = $delivertestingitems->count();
		
		$_totalcount = $_countshippingitems + $_countcleaningitems + $_counttestingitems;
		
		$delivertoshippingitems = $delivertoshippingitems->groupBy('model')->all();
		
		$delivercleaningitems = $delivercleaningitems->groupBy('model')->all();
		
		$delivertestingitems = $delivertestingitems->groupBy('model', 'department')->all();
		
		return $this->render('_deliveritems', [
						'order'=>$order,
						'delivertoshippingitems' => $delivertoshippingitems,
						'_countshippingitems' => $_countshippingitems,
						'delivercleaningitems' => $delivercleaningitems,
						'_countcleaningitems' => $_countcleaningitems,
						'delivertestingitems' => $delivertestingitems,
						'_counttestingitems' => $_counttestingitems,
						'_totalcount' => $_totalcount
					]);
	}
	
	public function actionRefurbish($id)
	{
		$_post = Yii::$app->request->post();
		$order = $this->findModel($id);
		$modelid = $_post['modelid'];
		$model = Models::findOne($modelid);		
		if (!isset($_POST['requiretestingreferb']))
			$model->requiretestingreferb = 0;
		else
			$model->requiretestingreferb = 1;
		
		if (!isset($_POST['preowneditems']))
			$model->preowneditems = 0;
		else
			$model->preowneditems = 1;
		//var_dump($model->id);
		//var_dump($order->id);
		//exit(1);
		$model->save();
		/*$items = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$modelid,'status'=>array_search('Picked', Item::$status)])->all();
		foreach($items as $item)
		{
			$item->status = array_search('In Progress', Item::$status);
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('In Progress', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $item->location;
				$itemlog->save();				
			}
		}*/		
		return $this->redirect(Yii::$app->request->referrer);  
	}
	
	public function actionValidateserial()
	{
		$_post = Yii::$app->request->get();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['serial']) || !isset($_post['currentmodel'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			//
			$serial = $_post['serial'];	
			$model = $_post['currentmodel'];
			$customer = Customer::findOne($_post['customer']);
			
			$_model = Models::findOne($model);
			
			$stripcharacter = str_replace('*', '', $_model->stripcharacters);
			$stripcharacterlength = strlen($stripcharacter);
			if($stripcharacterlength!==0){
				$hasstartedwithstripcharacter = (bool) preg_match('/^' . $stripcharacter . '/', $serial);
				if($hasstartedwithstripcharacter)
					$serial = substr($serial, $stripcharacterlength);
			}			
			
			$charactercount = $_model->charactercount;
				
			$stripdashserial = str_replace('-', '', $serial);

			$checkit = str_replace('*', '', $_model->checkit);
			
			$hasstartedwithcheckit = (bool) preg_match('/^' . $checkit . '/', $serial);
			
			$_find_serial = Item::find()->where(['serial'=>$serial])->one();
			
			$_find_in_inventory_serial = Item::find()->where(['serial'=>$serial, 'status'=>array_search('In Stock', Item::$status)])->one();
			
			/**
			 * verify models.checkit
			 */			
			
			if(!empty($checkit) && !$hasstartedwithcheckit){
				$_message = "Serial number must be start with $checkit";
			}
			/**
			 * verify character count
			 */
			
			else if((!empty($charactercount) && $charactercount!==0) && (strlen($stripdashserial) !== $charactercount)){								
				$_message = "Serial number must have $charactercount characters without '-'";				
			}
			
			else if($_find_serial !== null && !$customer->trackincomingserials)
				$_message = "Serial Number ($serial) already exists!";
			
			else if($customer->trackincomingserials && $_find_in_inventory_serial!==null)
				$_message = "Serial Number ($serial) already exists in " . $customer->companyname . " inventory!";
			
			if(!empty($_message))
				$_retArray = array('error' => true, 'html' => $_message);
			else 
				$_retArray = array('success' => true);
			echo json_encode($_retArray);
			exit();			
		}
	}
	
	public function actionSaveserial()
	{
		$_retArray = array('success' => FALSE, 'html' => ''); 
		$_post = Yii::$app->request->post();
		
		//if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['serial'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$serial = $_post['serial'];		
			$lane = $_post['lane'];		
			$item = $_post['currentitem'];	
			$model = $_post['currentmodel'];	
			//$order = $_post['order'];
			//find order
			$_order = $this->findModel($_post['order']);			
			$_model = Models::findOne($model);
			if($_order->ordertype!=1)
				$customer = $_order->customer_id;
			else
				$customer = 4;		
			//var_dump($serial, $model, $customer);
			/**
			 * get character to be removed from beginning
			 * verify the serial contain these characters at beginning
			 * get length of characters to be removed
			 * remove these characters using substr
			 */
			/*$stripcharacter = $_model->stripcharacters;
			$stripcharacterlength = strlen($stripcharacter);
			if($stripcharacterlength!==0){
				$hasstartedwithstripcharacter = (bool) preg_match('/^' . $stripcharacter . '/', $serial);
				if($hasstartedwithstripcharacter)
					$serial = substr($serial, $stripcharacterlength);
			}*/
			$_customer = Customer::findOne($customer);
			$stripcharacter = $_model->stripcharacters;
			$stripcharacterlength = strlen($stripcharacter);
			if($stripcharacterlength!==0 && strpos($stripcharacter, '*') === false){
				$hasstartedwithstripcharacter = (bool) preg_match('/^' . $stripcharacter . '/', $serial);
				if($hasstartedwithstripcharacter)
					$serial = substr($serial, $stripcharacterlength);
			} else if(strpos($stripcharacter, '*') !== false){
				$stripcharacter = str_replace('*', '', $stripcharacter);
				$stripcharacterarray = explode($stripcharacter, $serial);
				$serial = $stripcharacterarray[1];
			}
			//only for purchase order type.
			$current_item = Item::find()->where(['ordernumber'=>$_order->id, 'model'=>$model, 'status'=>array_search('Reserved', Item::$status)])->one();
			if($current_item === null)
				$current_item=Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$model, 'customer'=>$customer])->one();
			//
			$current_item->owner_id = Yii::$app->user->id;
			$current_item->ordernumber = $_order->id;
			if($_order->ordertype==1)
				$current_item->customer = $_order->customer_id;
			$current_item->status = array_search('Picked', Item::$status); 
			$current_item->serial = $serial;
			$current_item->lane = $lane;
			$current_item->picked = date('Y-m-d H:i:s');
			if($current_item->save()){
				//track item 
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('Picked', Item::$status);
				$itemlog->itemid = $current_item->id;
				//$itemlog->locationid = $current_item->location;
				$itemlog->save();		
				$_order_qty = Itemsordered::find()->where(['ordernumber'=>$_order->id, 'model'=>$model])->one()->qty;
				$_order_qty -= Item::find()
								->where(['ordernumber'=>$_order->id, 'model'=>$model, 'status' => array_search('Requested', Item::$status)])
								->orWhere(['ordernumber'=>$_order->id, 'model'=>$model, 'status' => array_search('In Transit', Item::$status)])
								->count();
				$done = (Item::find()->where(['ordernumber'=>$_order->id, 'model'=>$model, 'status' => array_search('Picked', Item::$status)])->andWhere(['not', ['serial' => null]])->count()==$_order_qty) ? true : false;
				$_retArray = array('success' => true, 'message' => 'Serial number is Successfully saved!');	
				if($done)
					$_retArray['done'] = $done;	
			}		
			else 
				$_retArray = array('error' => true, 'message' => 'Something wrong when trying to save serial! Plese try again!');
			echo json_encode($_retArray);
			exit();
		//}
	}
	
	public function actionScanserialpicture()
	{
		$_message = "";
		$model = new UploadForm();
		$model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
		if($model->upload())
			$_message = "Uploaded Successfull!";
		else 
			$_message = $model->errors;
		$url = 'https://api.idolondemand.com/1/api/sync/ocrdocument/v1';
		$session = Yii::$app->session;
		$filePath = realpath('uploads/images/tmp/' . $session['__user_picture']);
		//
		$post = array('apikey' => '1c1a9e1d-42bb-47c6-be5f-78bc4fbdbd26',
		                    'mode' => 'document_photo',
		                    'file' =>'@'.$filePath);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_POST,1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    $result=curl_exec ($ch);
	    curl_close ($ch);
        $json = json_decode($result,true);
	    if($json && isset($json['text_block']))
	    {
	        $textblock =$json['text_block'][0];
	        //echo PHelper::stringBetween("Serial ", " ", $textblock['text']);
			$_message = PHelper::stringBetween("Serial ", " ", $textblock['text']);
	    } 	
		$_retArray = array('success' => true, 'message' => $_message);
		echo json_encode($_retArray);
		exit();		
	}
    
    public function actionAddcustomer()
    {
    	$model = new Customer();
    	
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return $this->redirect(['view', 'id' => $model->id]);
    	} else {
    		return $this->render('_createcustomer', [
    				'model' => $model,
    				]);
    	}
    }
    
    public function actionLabeltest()
    {
    	$shipment = new \RocketShipIt\Shipment('UPS');
    	$shipment->setParameter('toCompany', 'John Doe');
    	$shipment->setParameter('toPhone', '1231231234');
    	$shipment->setParameter('toAddr1', '101 W Main');
    	$shipment->setParameter('toCity', 'Bozeman');
    	$shipment->setParameter('toState', 'MT');
    	$shipment->setParameter('toCode', '59715');
    
    	$package = new \RocketShipIt\Package('UPS');
    	$package->setParameter('length', '5');
    	$package->setParameter('width', '5');
    	$package->setParameter('height', '5');
    	$package->setParameter('weight', '5');
    
    	$shipment->addPackageToShipment($package);
    
    	$response = $shipment->submitShipment();
    
    	return $this->render('_labelreturn', ['response'=>$response]);
    }    

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /*public function actionDelete($id)
    {
		if (($model = Order::findOne($id)) !== null) {
			
			$items = Item::find()->where(['ordernumber' => $id])->all();

			$itemsOrdered = Itemsordered::find()->where(['ordernumber' => $id])->all();
						
			$shipment = Shipment::find(['orderid' => $id])->one();
				
			$orderLog = Orderlog::find(['orderid' => $id])->one();
				
			$itemsHasOption = ItemHasOption::find(['orderid' => $id])->all();
			
			//delete item entries
			foreach($items as $item) {
				$item->delete();
			}
		
			//delete itemordered entries
			foreach($itemsOrdered as $itemOrdered) {
				$itemOrdered->delete();
			}
				
			//delete item options entries
			foreach($itemsHasOption as $itemHasOption)
			{
				$itemHasOption->delete();
			}
			
			//delete shipment entry
			if(!empty($shipment))
				$shipment->delete();
		
			//delete order log entry
			if(!empty($orderLog))
				$orderLog->delete();
			
			if($model->delete()){
				$_message = '<div class="alert alert-danger fade in"><strong>Success!</strong> Order has been deleted successfully!</div>';
				Yii::$app->getSession()->setFlash('success', $_message);       	
			}
		} else {
			$_message = '<div class="alert alert-warning fade in"><strong>Success!</strong> This current Order does not exist.</div>';
			Yii::$app->getSession()->setFlash('success', $_message); 			
		}
		//
        return $this->redirect(['index']);	
    }*/
	
    /**
     * Soft Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		if (($model = Order::findOne($id)) !== null) {
			$model->deleted = 1;
			if($model->save()){
				$_message = 'Order has been deleted successfully!';
				Yii::$app->getSession()->setFlash('danger', $_message);      
			} else {
				$_message = 'There is a problem in deleting Order!';
				Yii::$app->getSession()->setFlash('warning', $_message);     
			}
//			$items = Item::find()->where(['ordernumber' => $id])->all();
//
//			$itemsOrdered = Itemsordered::find()->where(['ordernumber' => $id])->all();
//						
//			$shipment = Shipment::find(['orderid' => $id])->one();
//				
//			$orderLog = Orderlog::find(['orderid' => $id])->one();
//				
//			$itemsHasOption = ItemHasOption::find(['orderid' => $id])->all();
//			
//			//delete item entries
//			foreach($items as $item) {
//				$item->delete();
//			}
//		
//			//delete itemordered entries
//			foreach($itemsOrdered as $itemOrdered) {
//				$itemOrdered->delete();
//			}
//				
//			//delete item options entries
//			foreach($itemsHasOption as $itemHasOption)
//			{
//				$itemHasOption->delete();
//			}
//			
//			//delete shipment entry
//			if(!empty($shipment))
//				$shipment->delete();
//		
//			//delete order log entry
//			if(!empty($orderLog))
//				$orderLog->delete();
//			
//			if($model->delete()){
				//$_message = '<div class="alert alert-danger fade in"><strong>Success!</strong> Order has been deleted successfully!</div>';
				//Yii::$app->getSession()->setFlash('success', $_message);       	
			//}
		} else {
			$_message = 'This current Order does not exist.';
			Yii::$app->getSession()->setFlash('warning', $_message); 			
		}
		//
        return $this->redirect(Yii::$app->request->referrer);    	
    }
        /**
        * Get deleted orders of an existing Order model.
        * @param integer $customer
        * @return mixed
        */
        public function actionGetdeleted($customer){
            $query = Order::find()->select('lv_salesorders.*')
                            ->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
                            ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                            ->andWhere(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.deleted' => 1])
							->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
							
            $dataProvider = new ActiveDataProvider([
                        'query' => $query,
                        'pagination' => ['pageSize' => 15],
                        'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
                        ]);
						
            $ordersHtml = $this->renderPartial('_deleted', [
                                'dataProvider' => $dataProvider,
                        ]);	
            
            $qQuery = QOrder::find()->select('lv_qsalesorders.*')
    			->innerJoin('lv_qitemsordered', '`lv_qitemsordered`.`ordernumber` = `lv_qsalesorders`.`id`')
                ->where(['deleted' => 1])
    			->groupBy('`lv_qitemsordered`.`ordernumber`');
            
			$dataProvider1 = new ActiveDataProvider([
    				'query' => $qQuery,
    				'pagination' => ['pageSize' => 15],
    				'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
    				]);            
            
            $qOrdersHtml = $this->renderPartial('_qdeleted', [
                                'dataProvider' => $dataProvider1,
                        ]);
                $_retArray = array('success' => true, 'orders_html' => $ordersHtml, 'qorders_html' => $qOrdersHtml,'orders_count' => (int)$query->count() , 'qorders_count' => (int)$qQuery->count());
                echo json_encode($_retArray);
                exit();	
        }
        
        /**
        * Revert deletion of an existing Order model.
        * @param integer $id
        * @return boolean
        */
        public function actionRevert($id, $customer){
            $model = Order::findOne($id);
            $model->deleted = 0;
            $model->save();
			//
            $basket_count = Order::find()
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`deleted`' => 1])
								->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`')
								->count();
			//
            $qbasket_count = QOrder::find()
								->innerJoin('lv_qitemsordered', '`lv_qitemsordered`.`ordernumber` = `lv_qsalesorders`.`id`')
								->where(['`lv_qsalesorders`.`deleted`' => 1])
								->groupBy('`lv_qitemsordered`.`ordernumber`')
								->count();
								
			$_total_count = (int) $basket_count + (int) $qbasket_count;
			//			
			echo $_total_count;
        }
		
        /**
        * Revert deletion of an existing QOrder model.
        * @param integer $id
        * @param integer $customer
        * @return integer $basket_count
        */
        public function actionQrevert($id, $customer){
            $model = QOrder::findOne($id);
            $model->deleted = 0;
			$model->save();	
			//
            $basket_count = Order::find()
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`deleted`' => 1])
								->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`')
								->count();
			//
            $qbasket_count = QOrder::find()
								->innerJoin('lv_qitemsordered', '`lv_qitemsordered`.`ordernumber` = `lv_qsalesorders`.`id`')
								->where(['`lv_qsalesorders`.`deleted`' => 1])
								->groupBy('`lv_qitemsordered`.`ordernumber`')
								->count();
								
			$_total_count = (int) $basket_count + (int) $qbasket_count;
			//			
            return $_total_count;
        }
        
        /**
        * Deletion of an existing Order model.
        * @param integer $id
        * @return mixed
        */
        public function actionRdelete($id){
            if (($model = Order::findOne($id)) !== null) {
			$model->deleted = 1;
                        if($model->save()){
                            $_message = 'Order has been deleted successfully!';
                            Yii::$app->getSession()->setFlash('danger', $_message);      
                        } else {
                            $_message = 'There is a problem in deleting Order!';
                            Yii::$app->getSession()->setFlash('danger', $_message);     
                        }
			$items = Item::find()->where(['ordernumber' => $id])->all();

			$itemsOrdered = Itemsordered::find()->where(['ordernumber' => $id])->all();
						
			$shipment = Shipment::find(['orderid' => $id])->one();
				
			$orderLog = Orderlog::find(['orderid' => $id])->one();
				
			$itemsHasOption = ItemHasOption::find(['orderid' => $id])->all();
			
			//delete item entries
			foreach($items as $item) {
				$item->delete();
			}
		
			//delete itemordered entries
			foreach($itemsOrdered as $itemOrdered) {
				$itemOrdered->delete();
			}
				
			//delete item options entries
			foreach($itemsHasOption as $itemHasOption)
			{
				$itemHasOption->delete();
			}
			
			//delete shipment entry
			if(!empty($shipment))
				$shipment->delete();
		
			//delete order log entry
			if(!empty($orderLog))
				$orderLog->delete();
			
			if($model->delete()){
				$_message = 'Order has been deleted successfully!';
				Yii::$app->getSession()->setFlash('danger', $_message);       	
			}
		} else {
			$_message = 'This current Order does not exist!';
			Yii::$app->getSession()->setFlash('danger', $_message); 			
		}
		//
            return $this->redirect(['index']);
        }
	
	public function actionQdelete($id)
	{
		if (($model = QOrder::findOne($id)) !== null) {

			$itemsOrdered = QItemsordered::find()->where(['ordernumber' => $id])->all();
						
			$quoteShipment = QShipment::find(['orderid' => $id])->one();
				
			$quoteOrderLog = QOrderlog::find(['orderid' => $id])->one();
				
			$quoteItemsHasOption = QItemHasOption::find(['orderid' => $id])->all();
		
			//delete itemordered entries
			foreach($itemsOrdered as $itemOrdered) {
				$itemOrdered->delete();
			}
				
			//delete item options entries
			foreach($quoteItemsHasOption as $quoteItemHasOption)
			{
				$quoteItemHasOption->delete();
			}
			
			//delete shipment entry
			if(!empty($quoteShipment))
				$quoteShipment->delete();
		
			//delete order log entry
			if(!empty($quoteOrderLog))
				$quoteOrderLog->delete();
			
			if($model->delete()){
				$_message = 'Quote Order has been deleted successfully!';
				Yii::$app->getSession()->setFlash('danger', $_message);       	
			}
		} else {
			$_message = 'This current Quote Order does not exist!';
			Yii::$app->getSession()->setFlash('danger', $_message); 			
		}
		//
        return $this->redirect(['index']);		
	}

        /**
         * Soft Delete the Quotes Sales Order.
         * @param type $id
         */
        public function actionQsdelete($id)
	{
            $model = QOrder::findOne($id);
            $model->deleted = 1;
            if($model->save()){
                $_message = 'Quote Order has been deleted successfully!';
                Yii::$app->getSession()->setFlash('danger', $_message);      
            } else {
                $_message = 'There is a problem in deleting Order!';
                Yii::$app->getSession()->setFlash('danger', $_message);     
            }
            return $this->redirect(['index']);
        }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findQModel($id)
    {
        if (($model = QOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
    private function generateNumber($customer, $purchasetype, $date=null)
    {
    	$return = null;
    	$generated = $this->generateUniqueStoreNum();
    	$today = date('Y-m-d');
		//		
    	if($purchasetype==1  || $purchasetype==3 || $purchasetype==4)//purchase
    	{
			$count = Order::find()->where(['customer_id'=>$customer->id])
			->andWhere(['<>', 'ordertype', 2])
			->andWhere("date(created_at)= '$today'")->count();
			
			$count += 1;
		
			$ordernum = sprintf("%02d", $count);
			 
			$return =  $customer->code . date('m') . date('d') . date('y') . $ordernum;
			$find = Order::find()->where(['number_generated'=>$return])->count();
			if($find > 0) {
				$_find_ordernum = str_replace($customer->code . date('m') . date('d') . date('y'), '', $return);
				$_find_ordernum = (int) $_find_ordernum;
				$_find_ordernum += 1;
				$_find_ordernum = sprintf("%02d", $_find_ordernum);
			}
			$return = ($find > 0) ? $customer->code . date('m') . date('d') . date('y') . $_find_ordernum : $return;
		}
    	else if($purchasetype==2){ 
    		//verify code unicity
    		$unique = $customer->code . '-' . $generated;
    		$find = Order::find()->where(['number_generated'=>$unique])->count();
    		$return = ($find) ? $this->generateNumber($customer, $purchasetype, $date) : $unique;
    	}

    	return $return;
    }
    
    public function generateUniqueStoreNum()
    {
    	$allowed_characters = array(1,2,3,4,5,6,7,8,9,0);
    	$number_of_digits = 6;
    	$number_of_allowed_character = count($allowed_characters);
    	$unique = "";
    	for($i = 1;$i <= $number_of_digits; $i++){
    		$unique .= $allowed_characters[rand(0, $number_of_allowed_character - 1)];
    	}
    	$unique = abs($unique);
    	$gen_length = strlen($unique);
    	$diff = $number_of_digits - $gen_length;
    	if($diff>0)
    	{
    		$i=1;
    		while($i<=$diff)
    		{
    			$unique .= rand(0, $number_of_allowed_character);
    			$i++;
    		}
    	}
    	return $unique;
    }
}