<?php

namespace app\modules\Inventory\controllers;

use Yii;
use yii\web\Controller;
use app\models\Inventory;
use app\modules\Orders\models\Order;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use app\models\InventorySearch;
use app\models\User;
use app\models\Partnumber;
use app\models\Medias;
use app\models\Item;
use app\models\Itemlog;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Customer;
use app\models\Category;
use app\models\Location;
use app\models\LocationClassment;
use app\models\ModelsPicture;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserHasCustomer;
use app\models\Orderlog;
use app\models\Shipment;
use app\models\Itemsordered;
use app\models\SystemSetting;
use yii\helpers\ArrayHelper;
use app\vendor\PHelper;
use app\components\Common;
use app\models\CustomerSetting;

class DefaultController extends Controller
{
    const FILE_UPLOAD_PATH_MODEL = "/public/images/models/";
    const FILE_UPLOAD_PATH = "/public/images/";
	
    public function behaviors()
    {
        return [
	    	'access' => [
	    		'class' => AccessControl::className(),
			    	// We will override the default rule config with the new AccessRule class
			    	'ruleConfig' => [
	    				'class' => AccessRule::className(),
	    			],
	    		'only' => ['index','create', 'update', 'view', 'delete', 'load', 'search', 'getdeleted', 'softdelete', 'revert'],
		    	'rules' => [
		    		[
		    			'actions' => ['index','create', 'update', 'view', 'delete', 'load', 'search', 'getdeleted', 'softdelete', 'revert', 'getmodal', 'getmodeldetails', 'merge', 'transfer', 'transferloc'],
		    			'allow' => true,
		    			// Allow few users
		    			'roles' => [
		    				User::TYPE_ADMIN,
		    				User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
							User::REPRESENTATIVE,
							User::TYPE_TECHNICIAN,
							User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
		    			],
		    		]
		    	],
	    	]
        ];
    }

    /**
     * Lists all Inventory models.
     * @return mixed
     */
    public function actionIndex()
    {

        //$query = Inventory::find()->all();
        /*$dataProvider = new ActiveDataProvider([
            'query' => Inventory::find(),
            'pagination' => ['pageSize' => 10],
        ]);*/
        $mongo = new \Mongo("mongodb://localhost");
        $collection = $mongo->ams->inventorymodels;
        if(!$collection->count()){
            $sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
                IF(lv_manufacturers.name !='' and lv_models.descrip !='',CONCAT(lv_manufacturers.name,' ', lv_models.descrip),if(lv_models.descrip!='', lv_models.descrip, lv_manufacturers.name) ) as modelname,
                lv_models.assembly, 
                lv_models.aei, 
                lv_models.frupartnum, 
                lv_models.manpartnum, 
                lv_departements.name as department,
                lv_models.id,
                lv_models.category_id,
                lv_medias.filename,
                (select group_concat(DISTINCT partid) as partid from lv_partnumbers where lv_partnumbers.model = lv_models.id) as partnumber,
                IFNULL(p.nb_models, 0) AS nb_models,
                IFNULL(p.instock_qty, 0) AS instock_qty
                FROM lv_models 
                LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
                LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
                LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
                LEFT JOIN (
                        SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
                        FROM lv_items
                        WHERE status IN (". array_search('In Stock', Item::$status).")
                        GROUP BY model
                ) p ON (lv_models.id = p.model) where lv_models.deleted = 0
                ORDER BY lv_manufacturers.name, lv_models.descrip
                ";
            $connection = Yii::$app->getDb();			
            $results = $connection->createCommand($sql)->queryAll();
            $collection->drop();
            $collection->batchInsert($results);
        }
       // $searchModel = new InventorySearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $basket_count = Models::find()->where(['deleted' => 1])->count();
        
        return $this->render('index', [
            'basket_count' => $basket_count
           // 'searchModel' => $searchModel,
           // 'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * This function is used to Soft Delete inventory.
     * @param integer $id
     */
    public function actionSoftdelete($id){
        $model = Models::findOne($id);
        $model->deleted = 1;
        if($model->save()){
                $_message = 'Inventory has been deleted successfully.';
                Yii::$app->getSession()->setFlash('danger', $_message);
        } else{
                $errors = json_encode($_model->errors) . '<br/>' . json_encode($model->errors);
                $_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);        		
        }
        return $this->redirect(['/inventory/index']);
    }
    
    /**
     * This function is used to Revert deleted inventory.
     * @param integer $id
     */ 
    public function actionRevert($id){
        $model = Models::findOne($id);
        $model->deleted = 0;
        $isSuccess = $model->save();
        if($isSuccess){
                $_message = 'Inventory has been reverted successfully.';
                Yii::$app->getSession()->setFlash('success', $_message);
        } else {
                $errors = json_encode($_model->errors) . '<br/>' . json_encode($model->errors);
                $_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);        		
        }
        return $isSuccess;
    }
	
	public function actionConfirmitem()
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
			
			$item = Item::findOne($id);
			$item->confirmed = 1;
			$item->tagnum = $_post['tagnum'];	
			$item->save();
			
			$_retArray = array('success' => true, 'html' => 'Confirmed!');
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		//}
	}
	
	public function actionModifyitem()
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
			
			$item = Item::findOne($id);
			$item->confirmed = 0;
			$item->tagnum = $_post['tagnum'];
			$item->save();
			
			$_retArray = array('success' => true, 'html' => 'Not Confirmed!');
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		//}
	}
	
    public function actionLoad()
    {
    	//if (Yii::$app->request->isAjax) {
            $condition = array('deleted' => "0");
			if(Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE) {
				//$query = Inventory::find();
//				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
//						lv_models.assembly, 
//						lv_models.aei, 
//						lv_models.frupartnum, 
//						lv_models.manpartnum, 
//						lv_departements.name as department,
//						lv_models.id,
//                                                lv_models.category_id,
//						lv_medias.filename,
//						IFNULL(p.nb_models, 0) AS nb_models,
//						IFNULL(p.instock_qty, 0) AS instock_qty
//						FROM lv_models 
//						LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
//						LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
//						LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
//						LEFT JOIN (
//							SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
//							FROM lv_items
//							WHERE status IN (". array_search('In Stock', Item::$status).")
//							GROUP BY model
//						) p ON (lv_models.id = p.model) where lv_models.deleted = 0
//						ORDER BY lv_manufacturers.name, lv_models.descrip
//						";	
			}
			else {
				$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                if(!count($customers) ){
                                    $customers = array(-1);
                                }
                $sql = "SELECT DISTINCT (
							`model`
							)
							FROM `lv_items`
							WHERE `customer` =:customer";
                
                		$connection = Yii::$app->getDb();
                	
               			$command = $connection->createCommand($sql, [':customer'=>$customers]);
               			
               			$models = $command->queryAll();            
                        
                 $_modelsitems = ArrayHelper::getColumn($models, 'model');
                 
                 //var_dump($_modelsitems);
//                                $my_customers = "(".implode(",", array_map('intval', $customers)).")";
//				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
//					lv_models.assembly, 
//					lv_models.aei, 
//					lv_models.frupartnum, 
//					lv_models.manpartnum, 
//					lv_departements.name as department,
//					lv_models.id,
//                                        lv_models.category_id,
//					lv_medias.filename,
//						IFNULL(p.nb_models, 0) AS nb_models,
//						IFNULL(p.instock_qty, 0) AS instock_qty
//					FROM lv_models 
//					LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
//					LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
//					LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
//                                        INNER JOIN lv_items on lv_items.model = lv_models.id
//					LEFT JOIN (
//						SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
//						FROM lv_items
//						WHERE status = ". array_search('In Stock', Item::$status)." AND customer IN " . $my_customers . "
//						GROUP BY model
//					) p ON (lv_models.id = p.model)
//					WHERE customer IN " . $my_customers . " and lv_models.deleted = 0
//                                        GROUP BY lv_models.id
//					ORDER BY lv_manufacturers.name, lv_models.descrip
//					";
                                $condition = array('id' => array('$in' => $_modelsitems), 'deleted' => "0");
			}  		
			$models = Yii::$app->common->getInventory($condition);
//			$connection = Yii::$app->getDb();
			
//			$command = $connection->createCommand($sql);
//			$count = count($command->queryAll());
			
			/*$dataProvider = new ActiveDataProvider([
			            'query' => $query,
			            'pagination' => ['pageSize' => 10],
			        ]);*/
			$dataProvider = new \yii\data\ArrayDataProvider([
				'allModels' => $models,
				//'totalCount' => $count,
				'pagination' => ['pageSize' => 15],
			]);			
    	
    		echo $this->renderPartial('_models', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    /*	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    */	
    }
	
	public function actionWarehouseform()
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
			$model = $this->findModel($id);
			$manufacturer = Manufacturer::findOne($model->manufacturer);
			$customerid = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
			//		
			$html = $this->renderPartial('_warehouseform', [
							'model'=>$model,
							'customerid'=>$customerid
						]);

			$_retArray = array('success' => true, 'html' => $html, 'itemname'=>$manufacturer->name . ' ' . $model->descrip);
			echo json_encode($_retArray);
			exit();
		}				
	}
	
	public function actionCreatewarehouseorder()
	{
		$data = Yii::$app->request->post();
		
		$_retArray = array('success' => FALSE, 'message' => '');

		$purchasetype = 4;
		$customer = Customer::findOne(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid);
		$_instock_customer = $customer->id;
		$shipby = date("Y-m-d", strtotime("+2 weekday")); 
		$number_generated = $this->generateNumber($customer, $purchasetype, $shipby);
		$datetime = date('Y-m-d H:i:s');
		$successorder = false; 
		$model = new Order;
		$model->customer_id = $customer->id;
		$model->location_id = $data['order_location'];
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
		if($customer->defaultshippingchoice==0)//asset
		{
			$_setting = SystemSetting::find()->one();
			$_deliverymethod = $_setting->shipping_method;
		}
		else if($customer->defaultshippingchoice==1) {
			$_setting = CustomerSetting::find()->where(['customerid'=>$customer->id])->one();
			$_deliverymethod = $_setting->default_shipping_method;
		} else if($customer->defaultshippingchoice==2) {
			$_setting = CustomerSetting::find()->where(['customerid'=>$customer->id])->one();
			$_deliverymethod = $_setting->secondary_shipping_method;
		}			
		//save shipments 
		$shipment = new Shipment;
		$shipment->orderid = $model->id;
		$shipment->shipping_deliverymethod = $_deliverymethod;
		$shipment->locationid = $data['order_location'];
		$shipment->save();		
		//
		$_find_model = Models::findOne($data['modelId']);
		$_data_model = $data['modelId'];
		//
		if(!$_find_model->assembly)
		{
			$value = $data['order_qty'];
			//for($i=0;$i<$data['order_qty'];$i++)
			//{
				$in_stock = Item::find()->where(['customer'=>$_instock_customer, 'model'=>$_data_model, 'status'=>4])->count();
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
						$item->location = $data['order_location'];
						$item->received = $datetime;
						$item->lastupdated = $datetime;
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
					$_instock_items = Item::find()->where(['customer'=>$_instock_customer, 'status'=>array_search('In Stock', Item::$status), 'model'=>$_data_model])->all();
					foreach($_instock_items as $_instock_item) {
						$_instock_item->status = array_search('Reserved', Item::$status);
						$_instock_item->ordernumber = $model->id;
						$_instock_item->location = $data['order_location'];
						if($_instock_item->save()){
							//track item
							$itemlog = new Itemlog;
							$itemlog->userid = Yii::$app->user->id;
							$itemlog->status = array_search('Reserved', Item::$status);
							$itemlog->itemid = $_instock_item->id;
							$itemlog->locationid = $_instock_item->location;
							$itemlog->save();
							$successorder = true;
						}
					}		        					
				} else {
					//exit(1);
					$_instock_items = Item::find()->where(['customer'=>$_instock_customer, 'status'=>array_search('In Stock', Item::$status), 'model'=>$_data_model])->limit($value)->all();
					foreach($_instock_items as $_instock_item) {
						$_instock_item->status = array_search('Reserved', Item::$status);
						$_instock_item->ordernumber = $model->id;
						$_instock_item->location = $data['order_location'];
						if($_instock_item->save()){
							//track item
							$itemlog = new Itemlog;
							$itemlog->userid = Yii::$app->user->id;
							$itemlog->status = array_search('Reserved', Item::$status);
							$itemlog->itemid = $_instock_item->id;
							$itemlog->locationid = $_instock_item->location;
							$itemlog->save();
							$successorder = true;
						}
					}	        					
				}	
			//}
			//
			$order = new Itemsordered;
			$order->customer = $model->customer_id;
			$order->qty = $value;
			$order->price = 0.00;
			$order->model = $_data_model;
			$order->ordernumber = $model->id;
			$order->timestamp = $datetime;
			$order->ordertype = $purchasetype;
			$order->save();			
		}
		//
		if($successorder === true){
			$_message = 'Order {'. $model->number_generated .'} has been created successfully!';
			$_retArray = array('success' => true, 'html' => $_message);
		} else{
			$errors = json_encode($model->errors) . '<br/>' . json_encode($item->errors) . '<br/>' . json_encode($order->errors);
			$_retArray = array('error' => true, 'html' => $errors);        		
		}
		echo json_encode($_retArray);
		exit();		
	}
	
	public function actionLoadcustomerinventory()
	{
		$_post = Yii::$app->request->get();
		
		//if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');

			$locationid = $_post['locationid'];
			
    		$customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
		
    		$customer = Customer::findOne($customer);
    	
			$categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
							->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
							->where(['customer'=>$customer->id])
							->groupBy('`lv_models`.`category_id`, `lv_items`.`model`')
							->orderBy('categoryname')
							->all();
    										
    		$_inventorylocations = LocationClassment::find()->select('parent_id')->where(['customer_id'=>$customer->id])->andWhere(['not', ['parent_id'=>null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();

    		if(!empty($location) && $location != "")
				$_location = Location::findOne($locationid);			
			
			$html_category = $this->renderPartial('@app/views/site/_stockoverview', [
								'customer'=>$customer,
								'categories'=>$categories,
								'_location'=>$_location
							]);
						
			$html_location = $this->renderPartial('@app/views/site/_locationstockoverview', [
								'customer'=>$customer,
								'locations'=>$_inventorylocations,
								'_location'=>$_location
							]);
							
			$_retArray = array('success' => true, 'html_category' => $html_category, 'html_location' => $html_location);
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();	
		//}		
	}
	
	public function actionRaddinventory()
	{
		$data = Yii::$app->request->post();
		
		$_retArray = array('success' => FALSE, 'html' => '');
		
		$location = Location::findOne($data['rma_location']);
		
		$_reallocate_errors = array();
		
		$_reallocate_success = array();
		
		$_warnoneofthosematch = array();
		
		$_warningmerge = array();
		
		$_count_serials = 0;
		
		foreach($data['itemserial'] as $_key=>$_value)
		{
			$serials = $data['itemserial'][$_key];
			$tagnums = $data['itemtagnum'][$_key];
			$_count_serials = count($serials);
			
			foreach($serials as $key=>$serial)
			{		
				$tagnum = $tagnums[$key];
				
				if(!empty($serial) && empty($tagnum))
					$item = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['serial'=>$serial, 'customer'=>$location->customer_id, 'serialized'=>1])
										->groupBy('model')
										->one();
				else if(empty($serial) && !empty($tagnum))
					$item = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')			
										->where(['tagnum'=>$tagnum, 'customer'=>$location->customer_id, 'serialized'=>1])
										->groupBy('model')
										->one();
				else if(!empty($serial) && !empty($tagnum))
				{
					$from_serial = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['serial'=>$serial, 'customer'=>$location->customer_id, 'serialized'=>1])
										->groupBy('model')
										->one();		

					$from_tagnumber = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['tagnum'=>$tagnum, 'customer'=>$location->customer_id, 'serialized'=>1])
										->groupBy('model')
										->one();
										
					if(!empty($from_serial) && !empty($from_tagnumber) && ($from_serial->id != $from_tagnumber->id)) {
						//process to merging
						$merged_item = $from_serial;
						$merged_item->tagnum = $from_tagnumber->tagnum;
						$merged_item->customer = $location->customer_id;
						$merged_item->serial = $serial;
						$merged_item->model = $_key;		
						$merged_item->save();
						//assign one merged
						$item = $merged_item;
						$_warningmerge[] = $merged_item->serial . '-' . $merged_item->tagnum;
					} else if(!empty($from_serial) && empty($from_tagnumber)) {
						$item = $from_serial;
						$_warnoneofthosematch[] = $tagnum;
					} else if (!empty($from_tagnumber) && empty($from_serial)) {
						$item = $from_tagnumber;	
						$_warnoneofthosematch[] =  $serial;						
					}
				}
								
				$current_location = $item->location;
							
				if($location->id != $current_location)
				{
					if(empty($item))
					{
						$item = new Item;
						$item->customer = $location->customer_id;
						$item->serial = $serial;
						$item->model = $_key;
						$item->status = array_search('Complete', Item::$status);
					}
					$item->location = $location->id;
					//
					if(empty($item) || !empty($item) && empty($item->tagnum))
						$item->tagnum = $tagnum;
					if($item->save())
					{
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('Complete', Item::$status);
						$itemlog->itemid = $item->id;
						$itemlog->locationid = $location->id;
						$itemlog->save();						
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('Transferred', Item::$status);
						$itemlog->itemid = $item->id;
						$itemlog->locationid = $current_location;
						$itemlog->save();					
					}
					$_reallocate_success[] = (!empty($item->serial)) ? $item->serial : $tagnum;
				} else {
					$_reallocate_errors[] = (!empty($serial)) ? $serial : $tagnum;
				}
			}	
		}
		
		$_count_errors = count($_reallocate_errors);
		
		if($_count_serials==$_count_errors){
			$_reallocate_errors = array_unique($_reallocate_errors);
			$_retArray = array('error' => true, 'html_error' => 'Serials / Tag numbers {' . implode(', ', $_reallocate_errors) . '} already exist at this location!');
		} else if ($_count_errors==0){
			$_retArray = array('success' => true, 'html' => 'All serials / Tag numbers {' . implode(', ', $_reallocate_success) . '} have been successfully transferred at this location!');
		} else {
			$_retArray = array('success' => true);		
			if($_count_serials > 0)
				$_retArray['html'] = 'Serials / Tag numbers {' . implode(', ', $_reallocate_success) . '} have been successfully transferred at this location!';
			if($_count_errors > 0)
				$_retArray['html_error'] = 'Serials / Tag numbers {' . implode(', ', $_reallocate_errors) . '} already exist at this location!';
		}
		//
		if(!empty($_warnoneofthosematch))
			$_retArray['html_warn_error'] = 'Serials / Tag numbers {' . implode(', ', $_warnoneofthosematch) . '} don\'t exist!';
		if(!empty($_warningmerge))
			$_retArray['html_warn_merge_error'] = 'Serials / Tag numbers {' . implode(', ', $_warnoneofthosematch) . '} are merged!';
		
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();			
	} 
    
    public function actionMerge($id){
        $model = Models::findOne($id);
        $manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
        $_post = Yii::$app->request->post();
        if(isset($_post['models'])){
            $models = $_post['models'];
            foreach($models as $mod){
                Item::updateAll(['model' => $id], ['model' => $mod]);
                $_merge_model = Models::findOne($mod);
                $_merge_model->merge_id = $id;
                if($_merge_model->save()){
                    Yii::$app->getSession()->setFlash('success', 'Models have been successfully transferred.');
                } else {
                    Yii::$app->getSession()->setFlash('danger', 'There is some problem in transferring models');
                }
                $this->redirect(['/inventory/index']);
            }
        }
        return $this->render('_mergemodels', ['model' => $model, 'manufacturer' => $manufacturer]);
    }
    
    public function actionTransfer($id){
        $model = Models::findOne($id);
        $manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
        $items = Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status)])->groupBy('location')->all();
        $locations = \app\models\Location::find()->where(['customer_id' => 4])->all();
        foreach($locations as $location){
            $address = '';
            if (!empty($location->storenum))
                $address = $location->storenum;
            if (!empty($location->storename))
                $address .= $location->storename.' ';
            $address .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;;
            $locationList[$location->id] = trim($address);
        }
        $quantity = Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status)])->count();
        $html = $this->renderPartial("_transferloc", [ 'items' => $items, 'model' => $model, 'locationList' => $locationList , 'quantity' => $quantity]);
        return json_encode(array('model' => $manufacturer->name.' '. $model->descrip, 'html' => $html));
    }
    
    public function actionTransferloc(){
        $_post = Yii::$app->request->post();
        $model = Models::findOne($_post['model']);
        $customers = $_post['customer'];
        $items =  ArrayHelper::getColumn(Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status)])->asArray()->all(), "id");
        $pointer = 0;
        foreach($customers as $key => $customer){
            $quantity = $_post['quantity'][$key];
            $pointer = (!$key)? 0 : ($_post['quantity'][$key-1] + $pointer);
            $itemsIds = array_slice($items, $pointer, $quantity); 
            Item::updateAll(array('customer' => $customer, 'location' => $_post['location'][$key]), ['id' => $itemsIds]);
        }
        $manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
        $items = Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status)])->groupBy('location')->all();
        $quantity = Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status)])->count();
        $locations = \app\models\Location::find()->where(['customer_id' => 4])->all();
        $locationList = array();
        foreach($locations as $location){
            $address = '';
            if (!empty($location->storenum))
                $address = $location->storenum;
            if (!empty($location->storename))
                $address .= $location->storename.' ';
            $address .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;;
            $locationList[$location->id] = trim($address);
        }
        $html = $this->renderPartial("_transferloc", [ 'items' => $items, 'locationList' => $locationList,'model' => $model, 'quantity' => $quantity]);
        return json_encode(array('model' => $manufacturer->name.' '. $model->descrip, 'html' => $html));
    }
    
    public function actionGetdeleted()
    {

    	if (Yii::$app->request->isAjax) {
				//$query = Inventory::find();
				$sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
						lv_models.assembly, 
						lv_models.aei, 
						lv_models.frupartnum, 
						lv_models.manpartnum, 
						lv_departements.name as department,
						lv_models.id,
                                                lv_models.category_id,
						lv_medias.filename,
						IFNULL(p.nb_models, 0) AS nb_models,
						IFNULL(p.instock_qty, 0) AS instock_qty,
						IFNULL(p.inprogress_qty, 0) AS inprogress_qty,
						IFNULL(p.readytoship_qty, 0) AS readytoship_qty
						FROM lv_models 
						LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
						LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
						LEFT JOIN lv_medias ON lv_models.image_id = lv_medias.id 
						LEFT JOIN (
							SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty,
							SUM(IF(status='". array_search('In Progress', Item::$status)."',1,0)) AS inprogress_qty,
							SUM(IF(status='". array_search('Ready to ship', Item::$status)."',1,0)) AS readytoship_qty
							FROM lv_items
							WHERE status IN (". array_search('In Stock', Item::$status).", ". array_search('In Progress', Item::$status).", ". array_search('Ready to ship', Item::$status).")
							GROUP BY model
						) p ON (lv_models.id = p.model) where lv_models.deleted = 1
						ORDER BY lv_manufacturers.name, lv_models.descrip
						";	
			
			$connection = Yii::$app->getDb();
			
			$command = $connection->createCommand($sql);
		
			$count = count($command->queryAll());
			
			$dataProvider = new SqlDataProvider([
				'sql' => $sql,
				'totalCount' => $count,
				'pagination' => ['pageSize' => 15],
			]);
    	
    		$html = $this->renderPartial('_deleted', [
    				'dataProvider' => $dataProvider,
    				]);
                $_retArray = array('success' => true, 'html' => $html, 'count' => $count);
		echo json_encode($_retArray);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}    	
    }
    
    
    /**
     * Displays a single Inventory model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionGetmodal($id){
        $model = Models::findOne($id);
        $_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
        $name = $_manufacturer->name . ' ' . $model->descrip;
        $html = $this->renderPartial('_mergemodels');
        $_retArray = array('success' => true, 'html' => $html, 'model' => $name);
	echo json_encode($_retArray);
	exit();
    }
    
    public function actionSearchmodels($query, $id){
//        $order = $query.'%';
//        $search_str = '%'.$query.'%';
//        $sql = 'select lv_models.descrip as descrip, lv_manufacturers.name as name,IF(lv_manufacturers.name !="" and lv_models.descrip !="",CONCAT(lv_manufacturers.name," ", lv_models.descrip),if(lv_models.descrip!="", lv_models.descrip, lv_manufacturers.name) ) as modelname, lv_models.id from lv_models join lv_manufacturers on lv_manufacturers.id = lv_models.manufacturer where (lv_models.descrip like :search or lv_manufacturers.name like :search) and lv_models.deleted = 0 and lv_models.id != :model  order by 
//           CASE
//              WHEN modelname LIKE :order THEN 1
//              ELSE 2
//            END';
//        $datas = Yii::$app->db->createCommand($sql)
//                ->bindValue(':search', $search_str)
//                ->bindValue(':order', $order)
//                ->bindValue(':model', $id)
//                ->queryAll();
        ini_set('mongo.long_as_object', 1);
        $m = new \MongoClient;
        $db = $m->ams;
        $search_string = $query;
        $returnData = array();
        $search = trim($search_string);
//        $search = str_replace(" ",".*",$search);
//        $search = "/".$search.".*/i";
        $searchStrings = explode(" ", $search);
        if(count($searchStrings)> 1){
            $search = '';
            foreach($searchStrings as $string){
                $search .= "(?=.*".$string.")";
            }
        } else {
            $search = str_replace(" ",".*",$search);
            $search = ".*".$search.".*";
        }
        $condition = array(
            '$or' => array(
                array(
                    "modelname" => array('$regex' => $search, '$options' => 'i'), 
                ),
                array(
                    "aei" => array('$regex' => $search, '$options' => 'i'), 
                ),
                array(
                    "frupartnum" => array('$regex' => $search, '$options' => 'i'),
                ),
                array(
                    "manpartnum" => array('$regex' => $search, '$options' => 'i')
                ),
                array(
                    "partnumber" => array('$regex' => $search, '$options' => 'i')
                )
            ),                    
        );
        $cond['deleted'] = "0";
        $condition['$and'] = array($cond);
        $sort = array('modelname' => 1, 'aei' => 1, 'frupartnum' => 1, 'manpartnum' => 1, "partnumber" => 1);
        $models = Yii::$app->common->getInventory($condition, $sort);
        if($search_string!=''){
            usort($models, function($a, $b) use($search_string){
                if(strpos($a['modelname'], trim($search_string)) == ''){
                    $sort = 1;
                }
                elseif(strpos($b['modelname'], trim($search_string)) == ''){
                    $sort = 1;
                } 
                else {
                    $sort = -1;
                }
                return $sort;
            });
        }
        foreach($models as $res){
            //$returnData[] = array('id' => $res['id'], 'name' => $res['modelname']);
        	            $result = '';
        	            if($res['aei'] != ''){
        		            $result = '('.$res['aei'].') ';
        		        }
        		            $result .= $res['modelname'];
        		            if($res['manpartnum'] != ''){
        			                $result .= ' - '.$res['manpartnum'];
        			        }
        			            if(false !== ($position = stripos($res['partnumber'],$query))){
        				                $result .= ' ('.$res['partnumber'].')';
        				         }
        	$returnData[] = array('id' => $res['id'], 'name' => $result);
        }
        return json_encode($returnData);
    }
    
    public function actionGetmodeldetails($id){
        $sql = "SELECT lv_manufacturers.name, 
                lv_models.descrip,
                lv_models.assembly, 
                lv_models.aei, 
                lv_models.frupartnum, 
                lv_models.manpartnum, 
                lv_departements.name as department,
                lv_models.id,
                lv_models.reorderqty,
                lv_models.palletqtylimit,
                lv_models.stripcharacters,
                lv_models.checkit,
                lv_models.charactercount,
                lv_models.category_id,
                IFNULL(p.nb_models, 0) AS nb_models,
                IFNULL(p.instock_qty, 0) AS instock_qty
                FROM lv_models 
                LEFT JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
                LEFT JOIN lv_departements ON lv_models.department = lv_departements.id 
                LEFT JOIN (
                        SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty
                        FROM lv_items
                        WHERE status IN (". array_search('In Stock', Item::$status).")
                        GROUP BY model
                ) p ON (lv_models.id = p.model) where lv_models.id = ".$id;	
        $connection = Yii::$app->getDb();
	$model = $connection->createCommand($sql)->queryOne();
        $_manufacturer = \app\models\Manufacturer::findOne($model['manufacturer']);
        if(!empty($model['aei'])){
            $partnumbers = '<td><a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="modelitem-popover_' . $model['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> '. $model['aei'] .' </a></td>';
        } else { 
            $partnumbers = "<td><div style='line-height: 40px;'>No Part Number</div></td>";
        }
        $category = \app\models\Category::findOne($model['category_id']);
        $categoryname = "<td>".$category->categoryname."</td>";
        $name = "<td>".$_manufacturer['name'] . ' ' . $model['descrip']."</td>";
        $assembly = ($model['assembly']) ? 'Yes' : 'No';
        $assembly = "<td>".$assembly."</td>";
        $sum = 0;
        if($model['assembly'] == 1) {
            if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                if(!count($customers) ){
                    $customers = array(-1);
                }
                $number_items = \app\models\ModelAssembly::find()
                    ->innerJoin('lv_partnumbers', '`lv_partnumbers`.`id` = `lv_model_assemblies`.`partid`')
                    ->where(['modelid'=>$model->id, 'customer' => $customers])
                    ->sum('quantity');
            } else {
                $number_items = \app\models\ModelAssembly::find()->where(['modelid'=>$model['id']])->sum('quantity');
            }
                $nbr_items_in_stock = Item::find()
                    ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                    ->where(['modelid'=>$model['id']])
                    ->andwhere(['status'  => array_search('In Stock', Item::$status)]);
                    if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                        $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                        if(!count($customers) ){
                            $customers = array(-1);
                        }
                        $nbr_items_in_stock->andWhere(['customer' => $customers]);
                    }
                    $nbr_items_in_stock->count();
                $sum = ($number_items!=0) ? $nbr_items_in_stock / $number_items : 0;
        }
        else 
        {
                $sum_in_stock = $model['instock_qty'];
                $sum = $sum_in_stock;
        }
        $stock = '<td><a tabindex="0" class="btn btn-default popup-marker" id="stock-popover_' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-content = "" data-animation="true" data-trigger="focus" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorydata?id='.$model['id'].'" data-original-title="' . $model['name'] . ' ' . $model['descrip'] . '"> ' . $sum . ' </a></td>';
        $remove = "<td><a href='javascript:void(0);' class='btn btn-danger remove-transfer'><span class='glyphicon glyphicon-remove'></span></a><input type='hidden' value='".$id."' name='models[]'></td>";
        $reorder_qty = "<td>".$model['reorderqty']."</td>";
        $palletqty = "<td>".$model['palletqtylimit']."</td>";
        $stripchars = "<td>".$model['stripcharacters']."</td>";
        $checkit = "<td>".$model['checkit']."</td>";
        $charcount = "<td>".$model['charactercount']."</td>";
        $department = "<td>".$model['department']."</td>";
        return "<tr>".$partnumbers.$categoryname.$name.$stock.$reorder_qty.$palletqty.$stripchars.$checkit.$charcount.$department.$assembly.$remove."</tr>";
    }

    /**
     * Creates a new Inventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$session = Yii::$app->session;

        $model = new Models();
         
        $partnumber = array(); 
		
		$data = Yii::$app->request->post();
		
		if (!empty($data)) {	
			$successorder = false;
			$_new_media_id = 0;
			//
			$model->category_id = $data['category'];
			$model->manufacturer = $data['manufacturer'];
			$model->department = $data['department'];
			$model->descrip = $data['descrip'];
			$model->aei = $data['model_aei'];
			$model->purchasepricing = $data['purchasepricing'];
			$model->repairpricing = $data['repairpricing'];		
			$model->purchasepricingtier2 = $data['purchasepricing2'];
			$model->repairpricingtier2 = $data['repairpricing2'];
			//advanced options
			$model->frupartnum = $data['frupartnum'];
			$model->manpartnum = $data['manpartnum'];
			$model->prefered_vendor = $data['prefered_vendor'];
			$model->secondary_vendor = $data['secondary_vendor'];
			//additional options
			$model->reorderqty = $data['reorderqty'];
			$model->palletqtylimit = $data['palletqtylimit'];
			$model->stripcharacters = $data['stripcharacters']; 
			$model->checkit = $data['checkit'];
			$model->charactercount = $data['charactercount'];
			
			/*if (!isset($_POST['photo_conversion']))
				$model->photo_conversion = 0;
			else
				$model->photo_conversion = 1;*/ 
			
			if (!isset($_POST['serialized']))
				$model->serialized = 0;
			else
				$model->serialized = 1;
			
			if (!isset($_POST['preowneduseditems']))
				$model->preowneditems = 0;
			else
				$model->preowneditems = 1; 

			if (!isset($_POST['requiretesting']))
				$model->requiretestingreferb = 0;
			else
				$model->requiretestingreferb = 1; 		
			//var_dump(ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->all());exit(1);
			if($model->save()) {
				$successorder = true;
				//customer options 
				foreach($data['modelCustomer'] as $key => $value)
				{
					$partnumber = new Partnumber;
					$partnumber->customer = $data['modelCustomerval'][$key];
					$partnumber->model = $model->id;
					$partnumber->partid = $data['partid'][$key];
					$partnumber->partdescription = $data['partdesc'][$key];
					$partnumber->purchasepricing = $data['ppurchasepricing'][$key];
					$partnumber->purchasepricingtier2 = $data['ppurchasepricing2'][$key];
					$partnumber->repairpricing = $data['prepairpricing'][$key];
					$partnumber->repairpricingtier2 = $data['prepairpricing2'][$key];
					$partnumber->type = $data['parttype'][$key];
					$partnumber->save();
				}
				//save pictures modelid
				$pictures = ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->all();
				//var_dump($pictures);exit(1);
				foreach($pictures as $picture)
				{
					$picture->modelid = $model->id;
					$picture->save();
				}
				//
				
				if(!empty($pictures))
					$model->image_id = $pictures[0]->mediaid;
				else {
					$media = new Medias();
					$media->filename = "no_image.jpg";
					$media->path = self::FILE_UPLOAD_PATH_MODEL;
					$media->type = 1;
					$media->save();
					$model->image_id = $media->id;
				} 
					
				$model->save();
				//
				PHelper::generateModelsJson($model->id);
			}
			if($successorder === true){
								
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
				 
				$name = $_manufacturer->name . ' ' . $model->descrip;
								
				$_message = '<div class="alert alert-success fade in"><strong>Success!</strong> Model {<strong>'. $name .'</strong>} has been added successfully!</div>';
				
				Yii::$app->getSession()->setFlash('success', $_message);
			} else{
				$errors = json_encode($model->errors) . '<br/>' . json_encode($partnumber->errors);
				
				$_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . $errors . '</div>';
				
				Yii::$app->getSession()->setFlash('error', $_message);        		
			}
			
			return $this->redirect(Yii::$app->request->referrer);
		} else {
			$session['__new_model_key'] = md5(uniqid(true));
            return $this->render('create', [
                'model' => $model,
            	'partnumber' => $partnumber,
				'files' => array()
            ]);
        }
    }
	
	public function actionSetmodeloptions()
	{
		//if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			
			$data = Yii::$app->request->post();
			
			if (!isset($data['serialModelId'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			
			$model = Models::findOne($data['serialModelId']);
			
			$manufacturer = Manufacturer::findOne($model->manufacturer);
						
			if (!isset($data['serialized']))
				$model->serialized = 0;
			else
				$model->serialized = 1;	

			if (!isset($data['cleaning']))
				$model->preowneditems = 0;
			else
				$model->preowneditems = 1;	

			if (!isset($data['testing']))
				$model->requiretestingreferb = 0;
			else
				$model->requiretestingreferb = 1;					
		
			if ($model->save()) 
				$html = "Model has been added successfully";
			
			$_retArray = array('success' => true, 'html' => $html, 'itemname' => $manufacturer->name . ' ' . $model->descrip);
			
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();
		/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}	*/	
	}
	
	public function actionShowmodelsuploaded()
	{
		$html = $this->renderAjax('_loaded_models_upload');
		
		$_retArray = array('success' => true, 'html' => $html);
		echo json_encode($_retArray);
		exit();		
	}
	
	public function actionRemovemodeluploaded()
	{
		$session = Yii::$app->session;
		
		$_post = Yii::$app->request->get();
		
		$_find_1 = Medias::find()->innerJoin('lv_models_pictures', '`lv_models_pictures`.`mediaid` = `lv_medias`.`id`')
						->where(['like', 'filename', $_post['file']])
						->andWhere(['_key' => $session['__new_model_key']])
						->groupBy('`lv_models_pictures`.`mediaid`')
						->one();
		//var_dump($_find_1, $_post['file']);exit(1);
		if($_find_1!==null) {			
			$_find_2 = ModelsPicture::find()->where(['mediaid'=>$_find_1->id])->one();
			$_find_1->delete();
			$_find_2->delete();
		}			
	}
    
    public function actionSearch()
    {
    	$_post = Yii::$app->request->get();
    	
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['query'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$query = $_post['query']; 
    		
	    	$searchModel = new InventorySearch();
	    	$dataProvider = $searchModel->search(['InventorySearch'=>['descrip'=>$query]]);
	    
	    	$html = $this->renderPartial('_smodels', [
	    			'dataProvider' => $dataProvider,
	    			]);
	    	
	    	$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
	    	echo json_encode($_retArray);
	    	exit();
    	/*} else { 
    	
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }

    /**
     * Updates an existing Inventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	/*foreach (\app\models\Customer::find()->all() as $customer) 
    	{
    		PHelper::generateNonSOCustomerJson($customer->id);
    	}*/
    	
        $model = $this->findModel($id);
		
		$session = Yii::$app->session;
        
        $partnumbers = Partnumber::find()->where(['model'=>$model->id])->all();
        
        $media = Medias::findOne($model->image_id);
        
       // var_dump($partnumber);

    		$data = Yii::$app->request->post();
		
		if (!empty($data)) {	
			$successorder = false;
			
			$model->category_id = $data['category'];
			$model->manufacturer = $data['manufacturer'];
			$model->department = $data['department'];
			$model->descrip = $data['descrip'];
			$model->aei = $data['model_aei'];
			$model->purchasepricing = $data['purchasepricing'];
			$model->repairpricing = $data['repairpricing'];
			$model->purchasepricingtier2 = $data['purchasepricing2'];
			$model->repairpricingtier2 = $data['repairpricing2'];
			//advanced options
			$model->frupartnum = $data['frupartnum'];
			$model->manpartnum = $data['manpartnum'];
			$model->prefered_vendor = $data['prefered_vendor'];
			$model->secondary_vendor = $data['secondary_vendor'];
			//additional options
			$model->reorderqty = $data['reorderqty'];
			$model->palletqtylimit = $data['palletqtylimit'];
			$model->stripcharacters = $data['stripcharacters'];
			$model->checkit = $data['checkit'];
			$model->charactercount = $data['charactercount'];

			if (!isset($_POST['serialized']))
				$model->serialized = 0;
			else
				$model->serialized = 1;
				
			if (!isset($_POST['preowneduseditems']))
				$model->preowneditems = 0;
			else
				$model->preowneditems = 1;
			
			if (!isset($_POST['requiretesting']))
				$model->requiretestingreferb = 0;
			else
				$model->requiretestingreferb = 1;
				
			if($model->save())
			{
				$successorder = true;	

				foreach ($partnumbers as $partnumber)
				{
					$partnumber->delete();
				}				
				
				foreach($data['modelCustomer'] as $key => $value)
				{
					//$partnumber  = Partnumber::find()->where(['customer'=>$data['modelCustomerval'][$key], 'model'=>$model->id])->one();
					
					//if(empty($partnumber))
					$partnumber = new Partnumber;
					//var_dump($data['modelCustomerval'][$key], $model->id, $partnumber);exit(1);
					$partnumber->customer = $data['modelCustomerval'][$key];
					$partnumber->model = $model->id;
					$partnumber->partid = $data['partid'][$key];
					$partnumber->partdescription = $data['partdesc'][$key];
					$partnumber->purchasepricing = $data['ppurchasepricing'][$key];
					$partnumber->purchasepricingtier2 = $data['ppurchasepricing2'][$key];
					$partnumber->repairpricing = $data['prepairpricing'][$key];
					$partnumber->repairpricingtier2 = $data['prepairpricing2'][$key];
					$partnumber->type = $data['parttype'][$key];
					$partnumber->save();
				}		
				
				//save pictures modelid
				$pictures = ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->andWhere(['modelid'=>null])->all();
				
				foreach($pictures as $picture)
				{
					$picture->modelid = $model->id;
					$picture->save();
				}				
				
				if(!empty($pictures))
					$model->image_id = $pictures[0]->mediaid;
				$model->save();
				//
				PHelper::updateModelsJson($model->id);
			}
			if($successorder === true){
				
				$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
					
				$name = $_manufacturer->name . ' ' . $model->descrip;
								
				$_message = 'Model {<strong>'. $name .'</strong>} has been updated successfully!';
				
				Yii::$app->getSession()->setFlash('warning', $_message);
			} else{
				$errors = json_encode($model->errors) . '<br/>' . json_encode($item->errors) . '<br/>' . json_encode($order->errors);
				$_message = $errors;
				Yii::$app->getSession()->setFlash('danger', $_message);        		
			}
			
			return $this->redirect(Yii::$app->request->referrer);

        } else {
			
			$session['__new_model_key'] = ModelsPicture::find()->where(['modelid'=>$model->id])->one()->_key;
			if(empty($session['__new_model_key']))
				$session['__new_model_key'] = md5(uniqid(true));
			
            return $this->render('update', [
                'model' => $model,
            	'partnumber' => $partnumbers, 
            	'media' => $media,
				'files' => ModelsPicture::find()->where(['modelid'=>$model->id])->all()
            ]);
        }
    }

    /**
     * Deletes an existing Inventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
    	PHelper::deleteModelsJson($id);
    	
    	$model = \app\models\Models::findOne($id);
    		
    	$_manufacturer = \app\models\Manufacturer::findOne($model->manufacturer);
    	
    	$name = $_manufacturer->name . ' ' . $model->descrip;
    	
        if($this->findModel($id)->delete())
        {
        	$_message = '<div class="alert alert-danger fade in">Model {<strong>'. $name .'</strong>} has been successfully deleted !</div>';
        	
        	Yii::$app->getSession()->setFlash('error', $_message);        	
        }

        return $this->redirect(['index']);
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

    /**
     * Finds the Inventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Inventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Models::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
    /**
     * 
     * @param type $_upoload_file
     * @param type $_type
     * @return boolean|string
     */
    private function uploadMedia($_upoload_file, $_type = '') {

        /***
         * to create folders if not folders are not available
         */
        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_MODEL;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $_error = "";
        $target_dir = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_MODEL;
        $target_file = $target_dir . basename($_upoload_file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($_type == "image") {
            $check = getimagesize($_upoload_file["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                return $_error = $_error . "File is not an image.";
            }
        }

        if ($_upoload_file["size"] > 2000000) {
            return $_error = $_error . "Sorry, your file is too large.";
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return $_error = $_error . "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        if (1) {

            $temp = explode(".", $_upoload_file["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            if (move_uploaded_file($_upoload_file["tmp_name"], $target_dir . $newfilename)) {

                $_success['success'] = true;
                $_success['filename'] = $newfilename;
                return $_success;
            } else {

                return $_error = $_error . "Sorry, there was an error uploading your file.";
            }
        }
        return false;
    }
}