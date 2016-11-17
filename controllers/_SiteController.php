<?php

namespace app\controllers;

use Yii;
use app\modules\Orders\models\Order;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\Category;
use app\models\Shipment;
use app\models\Users;
use app\models\Inventory;
use app\models\Itemsorderedbuild;
use app\models\Manufacturer;
use app\models\Medias;
use yii\helpers\ArrayHelper;
use app\models\ItemHasOption;
use app\models\LocationClassment;
use app\models\Models;
use app\models\User;
use app\models\Location;
use app\models\LoginForm;
use app\models\ChangepasswordForm;
use app\vendor\BlakeGardner\MacAddress;
use app\vendor\DeviceTracker;
use app\models\UserLogTracking;
use app\models\UserLogLocationTracking;
use app\models\PricingImport;
use app\models\Partnumber;
use yii\web\Session;
use yii\web\Cookie;
use app\vendor\GeoPlugin;
use yii\web\NotFoundHttpException;
use app\vendor\PHelper;
use app\models\ModelsPicture;
use app\models\Itemstesting;
use app\models\Item;
use app\models\Itemlog;
use app\models\ShipmentsAwg;
use app\models\ItemsAwg;
use app\models\ModelsAwg;
use app\models\LocationsAwg;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
/*use yii\imagine;
use yii\imagine\Image;
use Imagine\Image\Box;*/
//use vendor\Pdf\Pdf;

class SiteController extends Controller
{
	public $layout = "main";
	
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
			    	'ruleConfig' => [
	    				'class' => AccessRule::className(),
	    			],
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
		    			'roles' => [
		    				User::TYPE_ADMIN,
		    				User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_CUSTOMER,
							User::REPRESENTATIVE,
							User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
		    			],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($location = null)
    {
		$locations = array();
		if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE)
		{		
			$query = Models::find()->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
									->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
									->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_models`.`id`')
									->where(['conditionid'=>4, 'requiretestingreferb'=>1, 'orderid'=>'`lv_items`.`ordernumber`']);
			
				$dataProvider = new ActiveDataProvider([
							'query' => $query,
							'pagination' => ['pageSize' => 10],
						]);
						
//			$inventories = Item::find()->select('customer')
//								->where(['status'=>array_search('In Stock', Item::$status)])
//								->orWhere(['status'=>array_search('Ready to ship', Item::$status)])
//								->groupBy('customer')
//								->orderBy('count(model) DESC')
//								->limit(6)
//								->all();
				//var_dump($query);
                                $connection = Yii::$app->getDb();
//                                $thisWeekMonday = '2015-09-10';
                                $thisWeekMonday = date('Y-m-d', strtotime('Monday this week'));
                                $sql = 'SELECT customer FROM `lv_items` where status IN ( :status_in_stock, :status_ready_ship) group by customer order by count(model) desc limit 6';
                                $command = $connection->createCommand($sql)
                                        ->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
                                        ->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status));
                                $inventories = $command->queryAll();        
                                foreach($inventories as $key => $inventory){
                                    $sql = "select ((((SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer and DATE_FORMAT(lastupdated,'%Y-%m-%d') < :last_monday) - (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer)) / (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer and DATE_FORMAT(lastupdated,'%Y-%m-%d') < :last_monday) * 100)) as percent, (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer) as total_count";
                                    $percent = $connection->createCommand($sql)
                                        ->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
                                        ->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status))
                                        ->bindValue(':last_monday', $thisWeekMonday)
                                        ->bindValue(':customer', $inventory['customer'])
                                        ->queryAll();
                                    $inventories[$key]['percent'] = $percent[0]['percent'];
                                    $inventories[$key]['count'] = $percent[0]['total_count'];
                                }
                                uasort($inventories, function ($a, $b) {
                                    return (abs($a['percent']) > abs($b['percent'])) ? -1 : 1;
                                });
                                $sql = "SELECT companyname, COUNT(*) as nb_customer_shipments
							FROM lv_shipments
							INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
							INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id						
							GROUP BY customer_id
							ORDER BY nb_customer_shipments DESC
							LIMIT 4
						";
//				echo '<pre>'; print_r($inventories); exit;
				$command = $connection->createCommand($sql);			
				$shipments = $command->queryAll();
                                			
                                $sql = 'SELECT  COUNT(*) FROM lv_shipments
                                                        INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
                                                        INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id';
                                $command = $connection->createCommand($sql);		
                                $total_shipments = $command->queryColumn();
                                
                                $recentUsersSql = 'SELECT CONCAT(firstname," " ,lastname) as customer_name,if(UNIX_TIMESTAMP(lv_users.created_at) > UNIX_TIMESTAMP(lv_users.modified_at), lv_users.created_at, lv_users.modified_at) as dateupdated, "user" as activity_type, if(UNIX_TIMESTAMP(lv_users.modified_at) > 0, "modified", "created") as type FROM `lv_users` ORDER BY 
                                CASE created_at WHEN UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP(modified_at) THEN created_at ELSE modified_at END DESC,
                                CASE modified_at WHEN UNIX_TIMESTAMP(modified_at) > UNIX_TIMESTAMP(created_at) THEN modified_at ELSE created_at END DESC LIMIT 3';
                               
                                $recentModelsSql = 'SELECT lv_manufacturers.name as project_name, lv_models.descrip as name, "model" as activity_type, if(UNIX_TIMESTAMP(lv_models.modified_at) > 0, "modified", "created") as type, CONCAT(lv_customers.firstname, " ", lv_customers.lastname) as customer_name,if(UNIX_TIMESTAMP(lv_models.created_at) > UNIX_TIMESTAMP(lv_models.modified_at), lv_models.created_at, lv_models.modified_at) as dateupdated FROM `lv_models` join lv_partnumbers on lv_partnumbers.model = lv_models.id join lv_customers on lv_customers.id = lv_partnumbers.customer JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id 
                                ORDER BY 
                                CASE lv_models.created_at WHEN UNIX_TIMESTAMP(lv_models.created_at) > UNIX_TIMESTAMP(lv_models.modified_at) THEN lv_models.created_at ELSE lv_models.modified_at END DESC,
                                CASE lv_models.modified_at WHEN UNIX_TIMESTAMP(lv_models.modified_at) > UNIX_TIMESTAMP(lv_models.created_at) THEN lv_models.modified_at ELSE lv_models.created_at END DESC limit 3';
                                
                                $recentShipmentsSql = 'SELECT companyname as name, CONCAT(firstname," " ,lastname) as customer_name, lv_shipments.dateshipped as dateupdated, "shipment" as activity_type, "created" as type
							FROM lv_shipments
							INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
							INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id						
							GROUP BY customer_id
							ORDER BY `lv_shipments`.`dateshipped` DESC
							LIMIT 3';
                                
				$recentsItemsLogSql = 'SELECT lv_manufacturers.name as project_name, lv_models.descrip as name, CONCAT(lv_users.firstname, " ", lv_users.lastname) as customer_name,"itemlog" as activity_type, lv_itemslog.created_at as dateupdated, "updated" as type FROM `lv_itemslog` join lv_items on lv_items.id = lv_itemslog.itemid join lv_models on lv_items.model = lv_models.id JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id join lv_users on lv_users.id = lv_itemslog.userid order by lv_itemslog.created_at desc limit 3';
                                
                                $recentUsers = $connection->createCommand($recentUsersSql)->queryAll();
                                $recentModels = $connection->createCommand($recentModelsSql)->queryAll();
                                $recentShipments = $connection->createCommand($recentShipmentsSql)->queryAll();
                                $recentsItemsLog = $connection->createCommand($recentsItemsLogSql)->queryAll();
                                $recentActivities = array_merge($recentsItemsLog, $recentUsers, $recentModels, $recentShipments);
                                uasort($recentActivities, function ($a, $b) {
                                    return (strtotime($a['dateupdated']) > strtotime($b['dateupdated'])) ? -1 : 1;
                                });
//                                echo '<pre>'; print_r($recentActivities); exit;
				/*$shipments = Shipment::find()->select('lv_customers.*')->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_shipments`.`orderid`')
									->innerJoin('lv_customers', '`lv_customers`.`id` = `lv_salesorders`.`customer_id`')
									->limit(4);*/
				
			$_render = $this->render('/site/index', ['dataProvider' => $dataProvider, 'inventories' => $inventories, 'shipments' => $shipments, 'locations'=>$locations, 'total_shipments' => $total_shipments, 'recentActivities' => $recentActivities]);		
		}
		else {
			$customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
			
			$customer = Customer::findOne($customer);
			
			$locations = Location::find()->innerJoin('lv_items', '`lv_items`.`location` = `lv_locations`.`id`')
										->where(['`lv_items`.`customer`'=>$customer->id])
										->groupBy('`lv_items`.`customer`, `lv_items`.`location`')
										->all();
										
			$models = Models::find()->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
									->where(['`lv_items`.`customer`'=>$customer->id])
									->groupBy('`lv_items`.`model`')
									->all();
						
			$categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
								->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
								->where(['customer'=>$customer->id])
								->groupBy('`lv_models`.`category_id`')
								->orderBy('categoryname')
								->all();
			
			$_inventorylocations = LocationClassment::find()->select('parent_id')->where(['customer_id'=>$customer->id])->andWhere(['not', ['parent_id'=>null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();
								
			if(!empty($location))
				$location = Location::findOne($location);
								
			//$items_customer = Item::find()->where(['customer'=>$customer->id])->groupBy('location')->all();
			
			$_render = $this->render('/site/_representativeoverview', ['locations'=>$locations, 'customer'=>$customer, 'categories' => $categories, 'models'=>$models, '_location'=>$location, '_inventorylocations'=>$_inventorylocations]);
		}
		
		return $_render;
    }

    public function time_ago($date) {
        if (empty($date)) {
            return "No date provided";
        }
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
        $now = time();
        $unix_date = strtotime($date);
    // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }
    // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            $tense = "ago";
        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if ($difference != 1) {
            $periods[$j].= "s";
        }
        return "$difference $periods[$j] {$tense}";
    }
	
    public function actionSerialsearch(){
        $_post = Yii::$app->request->get();
        $searchModel = new \app\models\SerialSearch();
		$dataProvider = $searchModel->search(['SerialSearch'=>['serial'=>$_post['query']]]);
		 $customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
                $customer = Customer::findOne($customer);        
         $html =  $this->renderPartial('_searchserial', [
			'dataProvider' => $dataProvider,
                        'customer' => $customer,
        ]);	
        $_retArray = array('success' => true, 'html' => $html, 'count' => $dataProvider->getTotalCount());
    			
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //return view
        return $_retArray;
        exit();         
    }
	
	public function actionLoadmodels()
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
			$model = Models::findOne($id);
			
			//
			$html = $this->renderAjax('@app/views/layouts/_loaded_models_upload', [
										'model' => $model
									]); 
	 
			$_retArray = array('success' => true, 'html' => $html);
				
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();		
		//}
	}
	
	/*public function actionUpstest()
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

		echo '<img src="data:image/gif;base64,'.$response['pkgs'][0]['label_img'].'" />';
		
		//print_r($response);		
	}*/
  
	public function actionLoadinstockpage()
	{
		$_post = Yii::$app->request->get();
		
		$category_id = $_post['categoryid'];
		//
		$customer_id = $_post['customerid'];
		//
		$category = Category::findOne($category_id);
		
		$customer = Customer::findOne($customer_id) ;
		
		$sql = "SELECT 
						SUM(lv_items.status='". array_search('In Stock', Item::$status)."') as instock_qty,
						SUM(lv_items.status='". array_search('In Progress', Item::$status)."') as inprogress_qty,
						SUM(lv_items.status='". array_search('Shipped', Item::$status)."') as shipped_qty,
						SUM(lv_items.status='". array_search('In Stock', Item::$status)."' OR lv_items.status='".array_search('In Progress', Item::$status)."' OR lv_items.status='".array_search('Shipped', Item::$status)."') as total,
						lv_models.id,
						lv_manufacturers.name,
						lv_models.descrip,
						lv_models.aei,
						lv_departements.name as department
						FROM lv_models 
						INNER JOIN lv_items 
						ON lv_models.id=lv_items.model
						INNER JOIN lv_manufacturers
						ON lv_models.manufacturer=lv_manufacturers.id
						INNER JOIN lv_departements
						ON lv_models.department=lv_departements.id
						WHERE lv_items.customer=". $customer->id ." AND lv_models.category_id=".$category->id." GROUP BY lv_items.model";
		//
		$dataProvider = new SqlDataProvider([
					'sql' => $sql,
					'pagination' => ['pageSize' => 5],
				]);	
		//
    	$html = $this->renderAjax('@app/modules/Customers/views/default/_loadstockoverview', [
									'dataProvider' => $dataProvider,
									'category' => $category,
									'customer' => $customer
    							]); 
 
    	$_retArray = array('success' => true, 'html' => $html);
    		
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();								
	}
	
    public function actionLoadawaitingdeliverylab()
    {    	
    	$_awaiting_delivery_items = Itemstesting::find()
    			->innerJoin('lv_items', '`lv_items`.`id` = `lv_itemstesting`.`itemid`')
		    	->where(['status'=>array_search('Requested for Service', Item::$status)])
		    	->limit(10)
		    	->all();    	
    	
    	$_delivered_items = Item::find()->where(['status'=>array_search('Used for Service', Item::$status)])->orderBy('lastupdated DESC')->limit(5)->all(); 
    	
    	//var_dump($_delivered_items);
    	
    	$html = $this->renderAjax('_loadawaitingdeliverylab', [
    								'_awaiting_delivery_items'=>$_awaiting_delivery_items,
    								'_delivered_items'=>$_delivered_items
    							]);
    	
    	$_retArray = array('success' => true, 'html' => $html);
    		
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();
    }
    
    public function actionTurnawaitingstatustoservice()
    {
    	$_post = Yii::$app->request->get();
    	
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['itemid'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$success = false;
    		
    		$itemid = $_post['itemid'];
			
    		$item = Item::findOne($itemid);
    		
    		if($item !== null)
    		{
    			$item->status = array_search('Used for Service', Item::$status);
    			//var_dump($item->status);
    			if($item->save())
    			{
    				$success = true;
    				//track item
    				$itemlog = new Itemlog;
    				$itemlog->userid = Yii::$app->user->id;
    				$itemlog->status = array_search('Used for Service', Item::$status);
    				$itemlog->itemid = $item->id;
    				$itemlog->save();
    			}
    		} else {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    		}
    		
    		if ($success)
    			$html = "success!";
    			
    		$_retArray = array('success' => true, 'html' => $html);
    			
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();    		
    	//}
    }
	
	public function actionPreloadcustomermodels()
	{
    	foreach (Customer::find()->all() as $cust) {    	
		//$cust = Customer::findOne(178);
	    	$sql = "SELECT
							lv_models.id, 
							CONCAT(lv_manufacturers.name, ' ',
							lv_models.descrip) as name,
							pn.partid,
							lv_models.aei,
							lv_models.frupartnum,
							lv_models.manpartnum
							FROM lv_models
							LEFT JOIN lv_manufacturers
							ON lv_models.manufacturer=lv_manufacturers.id
							LEFT JOIN lv_departements
							ON lv_models.department=lv_departements.id LEFT JOIN lv_partnumbers pn ON pn.model=lv_models.id AND pn.customer='". $cust->id ."' WHERE descrip <> '' GROUP BY lv_models.id";
	    	
	    	$models = Yii::$app->db->createCommand($sql)->queryAll();
	    	
	    	$output = array();
	    	
	    	$suggestions = array();
	    	
	    	foreach($models as $model)
	    	{
	    		$part_id = "";
	    		if(isset($model['partid'])) {
	    			$part_id = $model['partid'];
	    		}
	    		$part_id = ($part_id!="") ? ' (' . $part_id . ')' : '';
				$model_aei = (!empty($model['aei'])) ? '(' . $model['aei'] . ') ' : '';
	    		$name = $model['name'];
	    		$suggestions['id']=$model['id'];
	    		$suggestions['name']=$model_aei . trim($name) . $part_id;
	    		$output[] = $suggestions;
	    	}
	    	//assemblies...
	    	$assemblies = Models::find()->where('descrip <> ""')->andWhere('manufacturer IS NULL')->andWhere('assembly=1')->all();
	    	foreach($assemblies as $assembly)
	    	{
	    		$name = $assembly->descrip;
	    		$suggestions['id']=$assembly->id;
	    		$suggestions['name']=trim($name);
	    		$output[] = $suggestions;
	    	}
	    	//store data in json file
	    	$name = $cust->id . '_models';
	    	$_storepath = "public/autocomplete/json/receiving/$name.json";
	    	$fp = fopen($_storepath, 'w');
	    	fwrite($fp, json_encode($output));
	    	fclose($fp);
    	}	
	}
	
	public function actionLoadmediamodels()
	{
		$models = Models::find()->all();
		
		foreach($models as $model)
		{
			$media = Medias::findOne($model->image_id);
			if($media !== null)
			{ 
				$picture = new ModelsPicture;
				$picture->_key = md5(uniqid().time());
				$picture->modelid = $model->id;
				$picture->mediaid = $media->id;
				$picture->save();
			}
		}
	} 
	
	/*public function actionLoadlocationclassments()
	{
// INSERT INTO `lv_locations_classments`(`parent_id`, `location_id`) SELECT 15, `lv_locations`.id
// FROM `lv_locations`
// WHERE `customer_id` =178
// AND `storenum` LIKE 'FW%'		
		$_locations = Location::find()->leftJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where('`lv_locations_classments`.`location_id` IS NULL')->all();
		//var_dump(ArrayHelper::getColumn($_locations, 'id'));
		foreach($_locations as $_location)
		{
			$_location_classment = new LocationClassment;
			$_location_classment->parent_id = 1;
			$_location_classment->location_id = $_location->id;
			$_location_classment->save();
		}
	}*/ 

    public function actionLogin()
    {
		date_default_timezone_set('US/Eastern');
		
    	$this->layout = 'login';
    	
       if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
       }
	   
	   $model = new LoginForm();
	   
		//
		if(Yii::$app->request->cookies->has('__hasagreement'))
		{
			$model->termsAgreement = true;
		}
		else 
		{
			$model->termsAgreement = false;
		}

        if ($model->load(Yii::$app->request->post())) {
			//verify if agreement cookie has been used.
			
			if(!empty($_POST['LoginForm']['termsAgreement']))
			{
				$cookie = new Cookie([
					'name' => '__hasagreement',
					'value' => $model->termsAgreement,
					'expire' => time()+60*60*24*365,
				]);
				Yii::$app->getResponse()->getCookies()->add($cookie);
			}
			else
			{
				$cookies = Yii::$app->response->cookies;
				//if(Yii::$app->request->cookies->has('__hasagreement'))
					unset($cookies['__hasagreement']);
				$model->termsAgreement = false;
			}
			//log user 
			if (YII_ENV_DEV)
				$location = $this->Iploc('68.115.162.38');
			else 
				$location = $this->Iploc(Yii::$app->getRequest()->getUserIP());
			//track user
			$user_tracking_location = new UserLogLocationTracking;
			$user_tracking_location->continent_code = $location['geoplugin_continentCode'];
			$user_tracking_location->contry_code = $location['geoplugin_countryCode'];
			$user_tracking_location->country_name = $location['geoplugin_countryName'];
			$user_tracking_location->region = $location['geoplugin_regionCode'];
			$user_tracking_location->region_name = $location['geoplugin_regionName'];
			$user_tracking_location->city = $location['geoplugin_city'];
			$user_tracking_location->latitude = $location['geoplugin_latitude'];
			$user_tracking_location->longitude = $location['geoplugin_longitude'];
			$user_tracking_location->area_code = $location[ 'geoplugin_areaCode'];
			$user_tracking_location->dma_code = $location['geoplugin_dmaCode'];
			$user_tracking_location->currency_code = $location[ 'geoplugin_currencyCode'];
			$user_tracking_location->currency_symbol = $location['geoplugin_currencySymbol'];
			$user_tracking_location->save();
			//
			$user_tracking = new UserLogTracking;
			$user_tracking->location_id = $user_tracking_location->id;
			$user_tracking->mac_address = MacAddress::getCurrentMacAddress('eth0');
			$user_tracking->ip_address = $_SERVER['REMOTE_ADDR'];
			$user_tracking->real_ip_address = Yii::$app->getRequest()->getUserIP();
			$user_tracking->browser = $_SERVER['HTTP_USER_AGENT'];
			$user_tracking->using_proxy = (Users::isProxy()===true) ? 1 : 0;
			$user_tracking->device_type = (Yii::$app->mobileDetect->isMobile()) ? 'mobile' : 'desktop';
			$user_tracking->save();
			//
			if($model->login())
			{			
				$user_tracking->userid = Yii::$app->user->id;
				//update login status to success
				$user_tracking->status = 1;
				$user_tracking->save();
				//set current ipaddress
				$session = new Session;
				$session->open();
				$session['current_ip_address'] = $user_tracking->ip_address;
				//
				$user = Users::findOne(Yii::$app->user->id);
				//set last login
				$user->last_login = date('Y-m-d H:i:s');
				$user->save();
				if($user->password_changed==0)
					return $this->redirect(['/site/changepassword']);
				else
					return $this->goBack();
			}
        }
		//
        return $this->render('login', [
            'model' => $model,
        ]);
    }
	
	public function actionChangepassword()
	{
		$this->layout = 'login';
		
		$userid = Yii::$app->user->id;
		
		$user = Users::findOne($userid);
		
		if($userid===null)
			throw new NotFoundHttpException('You have tried to access this page without logged!');
		
		if($user->password_changed==0){
		
			$model = new ChangepasswordForm;
			
			if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				$user->hash_password = md5($model->password);
				$user->password_changed = 1;
				$user->save();
				return $this->goBack();
			}
			//
			return $this->render('changepassword', [
				'model' => $model,
			]);
		}
		else 
		{
			return $this->redirect(['/site/index']);
		}
	}
	
	public function actionPreloadshipments()
	{
		ini_set('max_execution_time', 120);
		ini_set('memory_limit', '512M');
		foreach(LocationsAwg::find()->all() as $mlocation)
		{
			$location = new Location;
			$location->customer_id = 178;
			$location->storename = $mlocation->storename;
			$location->storenum = $mlocation->storenum;
			$location->address = $mlocation->address;
			$location->address2 = $mlocation->address2;
			$location->city = $mlocation->city;
			$location->state = $mlocation->state;
			$location->zipcode = $mlocation->zipcode;
			$location->phone = $mlocation->phone;
			$location->email = $mlocation->email;
			$location->save();
		}
		//var_dump(ModelsAwg::find()->where(['id' =>[5233, 5234, 5235, 5237, 5238, 5239, 5240, 5241, 5242]])->all());exit(1);
		/*foreach(ModelsAwg::find()->where(['id' =>[5233, 5234, 5235, 5237, 5238, 5239, 5240, 5241, 5242]])->all() as $modelAwg)
		{		
			$_model = Models::findOne($modelAwg['id']);
			if(isset($_model))
				$_model->delete();
				//
				$_media = new Medias;
				$_media->filename = $modelAwg->image_path;
				$_media->path = 'models/';
				$_media->type = 1;
				$_media->save();				
				//
				$model = new Models;
				$model->id = $modelAwg->id;
				$model->palletqtylimit = $modelAwg->palletqtylimit;
				$model->stripcharacters = $modelAwg->stripcharacters;
				$model->checkit = $modelAwg->checkit;
				$model->charactercount = 0;
				$model->descrip = $modelAwg->descrip;
				$model->image_id = $_media->id;
				$model->aei = $modelAwg->aei;
				$model->frupartnum = $modelAwg->frupartnum;
				$model->manpartnum = $modelAwg->manpartnum;
				$model->category_id = $modelAwg->category;
				$model->department = $modelAwg->department;
				$model->serialized = $modelAwg->serialized;
				$model->storespecific = $modelAwg->storespecific;
				$model->quote = $modelAwg->quote;
				$model->save();
//echo $modelAwg->id;
				//

		}
		
		foreach(ItemsAwg::find()->all() as $ItemsAwg)
		{	
			$item = new Item;
			
			if($ItemsAwg->status == 1)
				$item->status = 4;
			else if($ItemsAwg->status == 2)
				$item->status = 7;
			else if($ItemsAwg->status == '2.5')
				$item->status = 11;
			else if($ItemsAwg->status == 3)
				$item->status = 12;
			
			$item->serial = $ItemsAwg->serial;
			$item->model = $ItemsAwg->model;
			$item->customer = 178;
			$item->location = $ItemsAwg->location;
			$item->picked = $ItemsAwg->picked;
			$item->ordernumber = $ItemsAwg->shipmentnumber;
			$item->received = $ItemsAwg->received;
			$item->receiving_pallet = $ItemsAwg->receiving_pallet;
			$item->shipped = $ItemsAwg->shipped;
			$item->trackingnumber = $ItemsAwg->trackingnumber;
			$item->trackinglink = $ItemsAwg->trackinglink;
			$item->returned = $ItemsAwg->returned;
			$item->lastupdated = $ItemsAwg->lastupdated;
			$item->requested = $ItemsAwg->requested;
			$item->prioritysort = $ItemsAwg->prioritysort;
			$item->requestedlocation = $ItemsAwg->requestedlocation;
			$item->shippingpallet = $ItemsAwg->shippingpallet;
			$item->terminalnum = $ItemsAwg->terminalnum;
			$item->notes = $ItemsAwg->notes;
			$item->transferred = $ItemsAwg->transferred;
			$item->save();
			//
			$itemlog = new Itemlog;
			$itemlog->itemid = $item->id;
			$itemlog->locationid = $item->location;
			if($item->status==12)
				$itemlog->shipment_id = Shipment::find()->where(['orderid'=>$item->ordernumber])->one()->id;
			$itemlog->status = $item->status;
			$itemlog->userid = 2;
			$itemlog->save();
			//
			//$_old_model = $ItemsAwg->model;
		}
		/*foreach(ShipmentsAwg::find()->all() as $ShipmentsAwg)
		{
			$shipment = new Shipment;
			$shipment->orderid = $ShipmentsAwg->id;
			$shipment->locationid = $ShipmentsAwg->store;
			$shipment->trackingnumber = $ShipmentsAwg->trackingnumber;
			$shipment->trackinglink = $ShipmentsAwg->trackinglink;
			$shipment->dateshipped = $ShipmentsAwg->dateshipped;
			$shipment->save();
			/*$order = new Order;
			$order->id = $ShipmentsAwg->id;
			$order->customer_id = $ShipmentsAwg->customer;
			$order->location_id = $ShipmentsAwg->store;
			$order->notes = $ShipmentsAwg->notes;
			$order->ordertype = 4;
			$order->type = $ShipmentsAwg->type;
			$order->returned = $ShipmentsAwg->returned;
			$order->returneddate = $ShipmentsAwg->returneddate;
			$order->trackingnumber = $ShipmentsAwg->trackingnumber;
			$order->trackinglink = $ShipmentsAwg->trackinglink;
			$order->dateshipped = $ShipmentsAwg->dateshipped;
			$order->shipby = $ShipmentsAwg->shipby;
			$order->trucknum = $ShipmentsAwg->trucknum;
			$order->sealnum = $ShipmentsAwg->sealnum;
			$order->dateonsite = $ShipmentsAwg->dateonsite;
			$order->returntracking = $ShipmentsAwg->returntracking;
			$order->created_at = $ShipmentsAwg->datecreated;
			$order->save();*/
		//}
	}
	
	/*public function actionConvertitem()
	{
		$items = Item::find()->groupBy('ordernumber', 'model')->all();

		foreach($items as $item)
		{
			//find order 
			$order = Order::findOne($item->ordernumber);
			//save itemsordered
			$itemordered = new Itemsorderedbuild;
			$itemordered->ordernumber = $item->ordernumber;
			$itemordered->package_optionid = null;
			$itemordered->customer = $item->customer;
			$itemordered->ordertype = $order->ordertype;
			$itemordered->qty = Item::find()->where(['ordernumber'=>$item->ordernumber, 'model'=>$item->model])->count();
			$itemordered->model = $item->model;
			$itemordered->status = null;
			$itemordered->notes = null;
			$itemordered->save();
		}		
	}*/

    public function actionLogout()
    {
    	$session = Yii::$app->session;
    	if(!is_dir($session['__autocomplete_json_generated_path']) && file_exists($session['__autocomplete_json_generated_path']))
    		unlink($session['__autocomplete_json_generated_path']);
    	
        Yii::$app->user->logout();

        return $this->goHome();
    }

	private function Iploc($ip)
	{
		return unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
	}
	
	function convertToPrice($str)
	{
		if(!empty($str))
			return str_replace(',', '.', str_replace(' ', '', $str));
		else 
			return null;
	}

}