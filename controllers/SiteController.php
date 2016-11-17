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
use app\models\Items;
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
use app\models\LocationImported;
use yii\web\Session;
use yii\web\Cookie;
use app\vendor\GeoPlugin;
use yii\web\NotFoundHttpException;
use app\vendor\PHelper;
use app\models\ModelsPicture;
use app\models\Itemstesting;
use app\models\Item;
use app\models\Itemlog;
use app\models\LocationParent;
use app\models\LocationContactImported;
use app\models\ShipmentsAwg;
use app\models\LocationDelete;
use app\models\LocationDetail;
use app\models\ItemLocation;
use app\models\LocationDetailImport;
use app\models\ItemTagnumber;
use app\models\UpdateItemLocation;
use app\models\ItemsAwg;
use app\models\ModelsAwg;
use app\models\LocationsAwg;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
/*use yii\imagine;
use yii\imagine\Image;
use Imagine\Image\Box;*/
//use vendor\Pdf\Pdf;
use app\models\ImportSerial;

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
                        'actions' => ['logout', 'index', 'sync', 'getshipments', 'getrecentactivity'],
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
    	if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER)
    	{    		    			    	
    		//			$inventories = Item::find()->select('customer')
    		//								->where(['status'=>array_search('In Stock', Item::$status)])
    		//								->orWhere(['status'=>array_search('Ready to ship', Item::$status)])
    		//								->groupBy('customer')
    		//								->orderBy('count(model) DESC')
    		//								->limit(6)
    		//								->all();
    		//var_dump($query);
//    		$connection = Yii::$app->getDb();
    		//                                $thisWeekMonday = '2015-09-10';
//    		$thisWeekMonday = date('Y-m-d', strtotime('Monday this week'));
    		//                                $sql = 'SELECT customer FROM `lv_items` where status IN ( :status_in_stock, :status_ready_ship) group by customer order by count(model) desc limit 6';
    		//                                $command = $connection->createCommand($sql)
    		//                                        ->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
    		//                                        ->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status));
    		//                                $inventories = $command->queryAll();
    		//                                foreach($inventories as $key => $inventory){
    		//                                    $sql = "select ((((SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer and DATE_FORMAT(lastupdated,'%Y-%m-%d') < :last_monday) - (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer)) / (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer and DATE_FORMAT(lastupdated,'%Y-%m-%d') < :last_monday) * 100)) as percent, (SELECT count(*) FROM `lv_items` WHERE status IN ( :status_in_stock, :status_ready_ship) and customer = :customer) as total_count";
    		//                                    $percent = $connection->createCommand($sql)
    		//                                        ->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
    		//                                        ->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status))
    		//                                        ->bindValue(':last_monday', $thisWeekMonday)
    		//                                        ->bindValue(':customer', $inventory['customer'])
    		//                                        ->queryAll();
    		//                                    $inventories[$key]['percent'] = $percent[0]['percent'];
    		//                                    $inventories[$key]['count'] = $percent[0]['total_count'];
    		//                                }
    		//
    		//                                uasort($inventories, function ($a, $b) {
    		//                                    return (abs($a['percent']) > abs($b['percent'])) ? -1 : 1;
    		//                                });
//    		$sql = 'select (((sum(t.total) - (sum(t.test))) / sum(t.total))* 100 ) as percent, abs((((sum(t.total) - (sum(t.test))) / sum(t.total))* 100 )) as absolute_percent, t.customer, sum(t.test) as count from ((SELECT count(*) as total, customer, "0" as test FROM `lv_items` WHERE status IN ( :status_in_stock , :status_ready_ship) and DATE_FORMAT(lastupdated,"%Y-%m-%d") < :last_monday group by customer) UNION (SELECT "0" as total, customer, count(*) as test FROM `lv_items` WHERE status IN ( :status_in_stock , :status_ready_ship) group by customer))t group by t.customer order by absolute_percent desc limit 6';
//                                $inventories = $connection->createCommand($sql)
//    		->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
//    		->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status))
//    				->bindValue(':last_monday', $thisWeekMonday)
//    				->queryAll();
//    				$sql = "SELECT companyname, COUNT(*) as nb_customer_shipments
//    						FROM lv_shipments
//    						INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
//    						INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id		
//                                                GROUP BY customer_id
//						ORDER BY nb_customer_shipments DESC
//						LIMIT 4";
////				echo '<pre>'; print_r($inventories); exit;
//				$command = $connection->createCommand($sql);
//				$shipments = $command->queryAll();
//                                $sql = 'SELECT  COUNT(*) FROM lv_shipments
//    				INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
//    		INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id';
//    		$command = $connection->createCommand($sql);
//    		$total_shipments = $command->queryColumn();
    	
//                                $recentUsersSql = 'SELECT CONCAT(firstname," " ,lastname) as customer_name,if(UNIX_TIMESTAMP(lv_users.created_at) > UNIX_TIMESTAMP(lv_users.modified_at), lv_users.created_at, lv_users.modified_at) as dateupdated, "user" as activity_type, if(UNIX_TIMESTAMP(lv_users.modified_at) > 0, "modified", "created") as type FROM `lv_users` ORDER BY
//    		CASE created_at WHEN UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP(modified_at) THEN created_at ELSE modified_at END DESC,
//    		CASE modified_at WHEN UNIX_TIMESTAMP(modified_at) > UNIX_TIMESTAMP(created_at) THEN modified_at ELSE created_at END DESC LIMIT 3';
//                
//                                $recentModelsSql = 'SELECT lv_manufacturers.name as project_name, lv_models.descrip as name, "model" as activity_type, if(UNIX_TIMESTAMP(lv_models.modified_at) > 0, "modified", "created") as type, CONCAT(lv_customers.firstname, " ", lv_customers.lastname) as customer_name,if(UNIX_TIMESTAMP(lv_models.created_at) > UNIX_TIMESTAMP(lv_models.modified_at), lv_models.created_at, lv_models.modified_at) as dateupdated FROM `lv_models` join lv_partnumbers on lv_partnumbers.model = lv_models.id join lv_customers on lv_customers.id = lv_partnumbers.customer JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id
//                                ORDER BY
//                                CASE lv_models.created_at WHEN UNIX_TIMESTAMP(lv_models.created_at) > UNIX_TIMESTAMP(lv_models.modified_at) THEN lv_models.created_at ELSE lv_models.modified_at END DESC,
//                                CASE lv_models.modified_at WHEN UNIX_TIMESTAMP(lv_models.modified_at) > UNIX_TIMESTAMP(lv_models.created_at) THEN lv_models.modified_at ELSE lv_models.created_at END DESC limit 3';
//    	
//                                $recentShipmentsSql = 'SELECT companyname as name, CONCAT(firstname," " ,lastname) as customer_name, lv_shipments.dateshipped as dateupdated, "shipment" as activity_type, "created" as type
//							FROM lv_shipments
//							INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
//							INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id
//							GROUP BY customer_id
//							ORDER BY `lv_shipments`.`dateshipped` DESC
//							LIMIT 3';
//    	
//				$recentsItemsLogSql = 'SELECT lv_manufacturers.name as project_name, lv_models.descrip as name, CONCAT(lv_users.firstname, " ", lv_users.lastname) as customer_name,"itemlog" as activity_type, lv_itemslog.created_at as dateupdated, "updated" as type FROM `lv_itemslog` join lv_items on lv_items.id = lv_itemslog.itemid join lv_models on lv_items.model = lv_models.id JOIN lv_manufacturers ON lv_models.manufacturer = lv_manufacturers.id join lv_users on lv_users.id = lv_itemslog.userid order by lv_itemslog.created_at desc limit 3';
//    	
//                                $recentUsers = $connection->createCommand($recentUsersSql)->queryAll();
//    	                        $recentModels = $connection->createCommand($recentModelsSql)->queryAll();
//                                $recentShipments = $connection->createCommand($recentShipmentsSql)->queryAll();
//                                $recentsItemsLog = $connection->createCommand($recentsItemsLogSql)->queryAll();
//                                $recentActivities = array_merge($recentsItemsLog, $recentUsers, $recentModels, $recentShipments);
//                                uasort($recentActivities, function ($a, $b) {
//                                    return (strtotime($a['dateupdated']) > strtotime($b['dateupdated'])) ? -1 : 1;
//                                });
    	/*$shipments = Shipment::find()->select('lv_customers.*')->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_shipments`.`orderid`')
    	->innerJoin('lv_customers', '`lv_customers`.`id` = `lv_salesorders`.`customer_id`')
    	->limit(4);*/
//        echo '<pre>'; print_r($shipmentData); exit;
    	$_render = $this->render('/site/index', ['locations'=>$locations]);
    	}
    	else {
    		$customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
		
    		$customer = Customer::findOne($customer);
    	
	    	$models = Models::find()->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
	    		->where(['`lv_items`.`customer`'=>$customer->id, 'serialized'=>1])
	    		->groupBy('`lv_items`.`model`')
	    		->all();
    	
    		if(empty($location)) 
				$location = "";
    	    										
    		$_render = $this->render('/site/_representativeoverview', ['customer'=>$customer, 'models'=>$models, 'location'=>$location]);
    	}
    	return $_render;
    }
    
    public function actionGetrecentactivity(){
        $recentActivities = \app\models\Recentactivity::find()->orderBy('created_at DESC')->limit(10)->all();
        $html  =  $this->renderPartial('_recentactivity', ['recentActivities' => $recentActivities]);
        return json_encode(array('html' => $html));
    }
    
    public function actionGetawaitingdist(){
        $query = 'SELECT lv_models.* FROM `lv_models` join lv_items on `lv_items`.`model` = `lv_models`.`id` join lv_salesorders on `lv_salesorders`.`id` = `lv_items`.`ordernumber` join lv_manufacturers on lv_manufacturers.id = lv_models.manufacturer where lv_models.department > 7 and lv_salesorders.trackingnumber is NULL and lv_items.deleted = 0 group by lv_models.id';
    		$command = Yii::$app->getDb()->createCommand($query);
    		$count = count($command->queryAll());
    		$dataProvider = new SqlDataProvider([
    				'sql' => $query,
    				'pagination' => ['pageSize' => 8],
    				'totalCount' => $count,
    				'sort' => [
                                    'attributes' => [
                                        'id' => [
                                            'asc' => ['lv_manufacturers.name' => SORT_ASC],
                                            'desc' => ['lv_manufacturers.name' => SORT_DESC],
                                            'default' => SORT_ASC,
                                        ],
                                    'department'
                                    ],
    				 ],
    			]);
        $html = $this->renderPartial('_awaitingdistribution', ['dataProvider' => $dataProvider]);
        return json_encode(array('html' => $html, 'success' => true));
    }

	public function actionRemoveduplicates()
	{
		/* $query = 'SELECT `serial` , `tagnum` , id, COUNT( id ) AS how_many
					FROM `lv_items`
					WHERE `customer` =178
		 			AND serial IS NOT NULL AND tagnum IS NOT NULL
					GROUP BY `serial` , `tagnum` , `location`
					HAVING how_many >=2
					ORDER BY how_many';*/
		 
		/* $query = 'SELECT `serial` , id, COUNT( id ) AS how_many
					FROM `lv_items`
					WHERE `customer` =178
		 			AND serial IS NOT NULL
					GROUP BY `serial`
					HAVING how_many >=2
					ORDER BY how_many';*/
		
		/*$query = 'SELECT id
					FROM `lv_items`
					WHERE `customer` =178
					AND `serial` IS NULL
					AND `tagnum` IS NULL';*/ //UNSERIALIZED ITEMS
		 
		$command = Yii::$app->getDb()->createCommand($query);
		 
		$items = ArrayHelper::getColumn($command->queryAll(), 'id');
		
		var_dump($items);
		//Item::deleteAll(['id'=>$rec]);
		foreach ($items as $item)
		{
			Item::findOne($item)->delete();
		}
	}
    
    public function actionResetpwd(){
        $this->layout = 'login';    	
       if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
       }
       $model = new LoginForm();
       $_get = Yii::$app->request->get();
       $token = $_get['token'];
       $_post = Yii::$app->request->post();
       if ($model->load($_post) && empty($token)) {
            if(empty($model->user)){
                Yii::$app->session->setFlash('error', 'Username/Email address does not exist.');
                $model->username = '';
            } else {
                $user = $model->user;
                $model = Users::findOne($user->id);
                $token = $model->id.substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 25)), 0, 25);
                $model->token = $token;
                $model->update(false);
                $body = $this->renderPartial('_resetpwd', ['model' => $model]);
                Yii::$app->mailer->compose()
                    ->setFrom([Yii::$app->params['adminEmail'] => 'Matthew Ebersole'])
                    ->setTo($model->email)
                    ->setSubject('AMS- Password Reset Request')
                    ->setHtmlBody($body)
                    ->send();
                Yii::$app->session->setFlash('success', 'Password reset instructions have been sent to your registered Email Address.');
                $this->redirect(['login']);
            }
       }
       
       if(!empty($token)){
           $model = Users::find()->where(['token' => $token])->one();
           if($model == NULL){
               Yii::$app->session->setFlash('error', 'Password Reset link is either invalid or expired.');
               return $this->redirect(['resetpwd']);
           }
           if(isset($_post['isreset'])){
               if($_post['new-pwd'] == '' || $_post['reset-pwd'] == ''){
                   Yii::$app->session->setFlash('error', 'New/Reset Password can not be left empty.');
                   return $this->redirect(['resetpwd?token='.$token]);
               } elseif($_post['new-pwd'] != $_post['reset-pwd']){
                   Yii::$app->session->setFlash('error', 'Password/Reset passwords do not match.');
                   return $this->redirect(['resetpwd?token='.$token]);
               } else {
                   $model->hash_password = md5($_post['new-pwd']);
                   $model->token = '';
                   if($model->save()){
                        Yii::$app->session->setFlash('success', 'Your Password has been changed successfully. You can now login with your new password.');
                   } else {
                       Yii::$app->session->setFlash('error', 'There is some problem in changing password. Please contact administrator.');
                   }
                   $this->redirect(['login']);
               }
           }
       }
       return $this->render('reset', [
            'model' => $model,
            'token' => $token
        ]);
    }

    public function actionGetshipments(){
        $connection = Yii::$app->getDb();
//        $lastMonday = date('Y-m-d',strtotime('last monday -7 days'));
//        $tuesDay = date('Y-m-d', strtotime('+1 day', strtotime($lastMonday)));
//        $wednesDay = date('Y-m-d', strtotime('+2 day', strtotime($lastMonday)));
//        $thursDay = date('Y-m-d', strtotime('+3 day', strtotime($lastMonday)));
//        $friDay = date('Y-m-d', strtotime('+4 day', strtotime($lastMonday)));
//        $satDay = date('Y-m-d', strtotime('+5 day', strtotime($lastMonday)));
//        $thisSunday   = date('Y-m-d',strtotime('last monday -1 days'));
        
        $seventhWeek = date('Y-m-d',strtotime('last monday -6 week'));
        $sixthWeek = date('Y-m-d',strtotime('last monday -5 week'));
        $fifthWeek = date('Y-m-d',strtotime('last monday -4 week'));
        $fourthWeek = date('Y-m-d',strtotime('last monday -3 week'));
        $thirdWeek = date('Y-m-d',strtotime('last monday -2 week'));
        $secondWeek = date('Y-m-d',strtotime('last monday -1 week'));
        $lastWeek = date('Y-m-d',strtotime('last monday'));
        
        $weekDays = array(
            array('created_at' => $seventhWeek, 'total' => 0),
            array('created_at' => $sixthWeek, 'total' => 0),
            array('created_at' => $fifthWeek, 'total' => 0),
            array('created_at' => $fourthWeek, 'total' => 0),
            array('created_at' => $thirdWeek, 'total' => 0),
            array('created_at' => $secondWeek, 'total' => 0),
            array('created_at' => $lastWeek, 'total' => 0),
        );
        $received = $shipped = array();
        foreach($weekDays as $index => $weekDay){
            $start = ($index > 0) ? $weekDays[($index -1)]['created_at'] : $seventhWeek;
            $end = $weekDay['created_at'];
            $receiveShipmentsSql = 'select count(t.customer) as total from (SELECT lv_items.customer, lv_itemslog.created_at, lv_itemslog.itemid FROM `lv_itemslog` left join lv_items on lv_items.id = lv_itemslog.itemid where lv_itemslog.status = :status_received and DATE_FORMAT(lv_itemslog.created_at,"%Y-%m-%d") > :start and DATE_FORMAT(lv_itemslog.created_at,"%Y-%m-%d") <= :end group by lv_items.customer) t';
            $receiveShipData = $connection->createCommand($receiveShipmentsSql)
                    ->bindValue(':status_received', array_search('Received', Item::$status))
                    ->bindValue(':start',$start)
                    ->bindValue(':end', $end)
                    ->queryAll();
            $received[] = array('created_at' => $end, 'total' => $receiveShipData[0]['total']);

            $shippedSql = 'select count(t.customer) as total from (SELECT lv_items.customer, lv_itemslog.created_at, lv_itemslog.itemid FROM `lv_itemslog` left join lv_items on lv_items.id = lv_itemslog.itemid where lv_itemslog.status = :status_shipped and DATE_FORMAT(lv_itemslog.created_at,"%Y-%m-%d") > :start and DATE_FORMAT(lv_itemslog.created_at,"%Y-%m-%d") <= :end group by lv_items.customer) t';
            $shippedData = $connection->createCommand($shippedSql)
                    ->bindValue(':status_shipped', array_search('Shipped', Item::$status))
                    ->bindValue(':start',$start)
                    ->bindValue(':end', $end)
                    ->queryAll();
            $shipped[] = array('created_at' => $end, 'total' => $shippedData[0]['total']);
        }
    	return json_encode(array('received' => $received, 'shipped' => $shipped), JSON_NUMERIC_CHECK);
    }
    
    public function actionCertificationreport()
    {
    	ini_set('max_execution_time', 120);
    	ini_set('memory_limit', '512M');
    	//
    	$cssContent = "
    		.kv-heading-1, th, td {font-size:18px}
    		table{border-collapse: collapse;}
    		th
		 	{
		 		background: #08c;
    			color: #FFF;
    			padding: 5px;
    			padding-left: 10px;
    			padding-right: 10px;
    			text-align:center;
		 	}	
    		td{padding:4px}
    		tr{border:none}
    		@page { 
				background: url('/testing/live/public/images/Certificate-Of-Destruction.png') no-repeat center center fixed; 
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				background-image-resize: 6;
				font-family: Arial
			}
    	";

		$count = ImportSerial::find()->orderBy('id ASC')->count();
		
		$maxperpage = 500;
		
		for($i=0; $i<$count/$maxperpage;$i++)
		{	
			$models = ImportSerial::find()->limit($maxperpage)->offset($maxperpage*$i)->orderBy('id ASC')->all();
			
			$content = $this->renderPartial('_certification_reports', [
					'models'=>$models,
					]);
			
			$pdf = \Yii::$app->pdf;
			
			$pdf->content = $content;
			
			$mpdf = $pdf->api; // fetches mpdf api
			
			$mpdf->WriteHTML($cssContent, 1, true, true);
			
			$mpdf->WriteHTML($content, 2);
			
			$mpdf->SetTitle('Certificate Of Destruction');
			
			$mpdf->SetHeader('|| Date : 09/14/2016');
			
			$mpdf->SetFooter("");
			
			if($i<($count/$maxperpage) - 1)
				$mpdf->addPage();
		}
	    // return the pdf output as per the destination setting
	    $mpdf->Output('certification_destruction.pdf', 'D');   	
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
	
	public function actionTransferloc()
	{
		$_post = Yii::$app->request->post();
		
		if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			
			$success = false;
			
			if (!isset($_post['location']) || !isset($_post['itemid'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			
			$new_location = $_post['location'];
			
			$id = $_post['itemid'];
			
			$success = false;
			
			$item = Item::findOne($id);
			
			$current_location = $item->location;
			
			$item->location = $new_location;
			
			if($item->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search('Transferred', Item::$status);
				$itemlog->itemid = $item->id;
				$itemlog->locationid = $current_location;
				$itemlog->save();				
			}
			
			$_retArray = array('success' => true, 'html' => 'Item');
				
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();				
		}
	}
	
	public function actionGetdetails()
	{
		$_post = Yii::$app->request->get();
		
		$_retArray = array('success' => FALSE, 'html' => '');
		
		if (!isset($_post['id'])) {
			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
			echo json_encode($_retArray);
			exit();
		}
		
		$connection = Yii::$app->getDb();
		
		$id = $_post['id'];		
		
		$model = Item::findOne($id);
		
		$_model = Models::findOne($model->model);
		
		$_manufacturer = Manufacturer::findOne($_model->manufacturer);
		
		$location = Location::findOne($model->location);
		
		$current_location = $location->address . ' ' . $location->city . ' ' . $location->state . ' ' .  $location->zipcode;
		
		//$customer_locations = Location::find()->where(['customer_id'=>$model->customer])->all();
		
		$sql = "SELECT id, CONCAT(IF(storenum is not null and TRIM(COALESCE(storenum, '')) <> '', CONCAT('Store#: ', storenum, ' - '), ''), IF(storename is not null and TRIM(COALESCE(storename, '')) <> '', CONCAT(storename, ' - '), ''), address, ' ', city, ' ', state, ' ', zipcode) as name FROM lv_locations WHERE customer_id=:customer";
		
		$command = $connection->createCommand($sql, [':customer'=>$model->customer]);
		
		$customer_locations = $command->queryAll();		
		
		$html = $this->renderPartial('_changeloc', [
									'model' => $model,
									'location' => $location,
									'itemid' => $id,
									'current_location' => $current_location,
									'customer_locations' => $customer_locations,
								]); 		
								
		$_retArray = array('success' => true, 'html' => $html, 'inventory_details'=> $_manufacturer->name . ' ' . $_model->descrip . ' (' . $model->serial . ')');
			
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		//return view
		return $_retArray;
		exit();				
	}
	
	public function actionInfo()
	{
	phpinfo();	
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
			$html = $this->renderPartial('@app/views/layouts/_loaded_models_upload', [
										'model' => $model
									]); 
	 
			$_retArray = array('success' => true, 'html' => $html);
				
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return view
			return $_retArray;
			exit();		
		//}
	}
	
	public function actionUpstest()
	{
		$shipment = new \RocketShipIt\Shipment('fedex');

		$shipment->setParameter('toCompany', 'John Doe');
		$shipment->setParameter('toName', 'John Doe');
		$shipment->setParameter('toPhone', '1231231234');
		$shipment->setParameter('toAddr1', '111 W Legion');
		$shipment->setParameter('toCity', 'Whitehall');
		$shipment->setParameter('toState', 'MT');
		$shipment->setParameter('toCode', '59759');
		$shipment->setParameter('packageCount', '2');
		$shipment->setParameter('sequenceNumber', '1');

		$shipment->setParameter('length', '5');
		$shipment->setParameter('width', '5');
		$shipment->setParameter('height', '5');
		$shipment->setParameter('weight','25');

		$response = $shipment->submitShipment();
		$shipmentId = $response['trk_main'];

		$label1 = $response['pkgs'][0]['label_img'];	
		
		var_dump($response['pkgs']);
	}
  
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
	
	public function actionAwaitingdeliverypick()
	{
		$_post = Yii::$app->request->get();
		
		$id = $_post['id'];
		
		$item = Item::findOne($id);
		
		$_order = Order::findOne($item->ordernumber);
				
		$serializeditems = Item::find()->where(['status'=>array_search('Requested for Service', Item::$status), 'picked'=>null])->all();
		
    	$html = $this->renderPartial('_loadpickserials', [
									'items'=>$serializeditems
    							]); 
 
    	$_retArray = array('success' => true, 'html' => $html, 'ordername'=>$_order->number_generated);
    		
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();			
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
			
			$item = Item::findOne($id);

			$order = Order::findOne($item->ordernumber); 
			
			$_model = Models::findOne($item->model);
			
			$_manufacturer = Manufacturer::findOne($_model->manufacturer); 
						
			$delivercleaningitems = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['`lv_items`.`id`'=>$id, 'status'=>array_keys(Item::$testingstatus), 'optionid' => [2,3], 'conditionid'=>4])
										->orWhere(['`lv_items`.`id`'=>$id, 'status'=>array_keys(Item::$testingstatus), 'preowneditems'=>1, 'conditionid'=>4])
										->count();
									
			$delivertestingitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
										->where(['`lv_items`.`id`'=>$id, 'status'=>array_keys(Item::$testingstatus), 'optionid' => [47,48]])
										->orWhere(['`lv_items`.`id`'=>$id, 'status'=>array_keys(Item::$testingstatus), 'requiretestingreferb'=>1, 'conditionid'=>4])
										->orWhere(['`lv_items`.`id`'=>$id, 'status'=>array_keys(Item::$testingstatus), 'conditionid'=>2])
										->count();
			
			$delivertoshippingitems = Item::find()->where(['`lv_items`.`id`'=>$id, 'conditionid' => [1, 3, 4]])->count();
			//
			$html = $this->renderPartial('_picklistreadyform', [
							'order'=>$order,
							'item'=>$item,
							'_model'=>$_model,
							'delivertoshippingitems' => $delivertoshippingitems,
							//'_countshippingitems' => $_countshippingitems,
							'delivercleaningitems' => $delivercleaningitems,
							//'_countcleaningitems' => $_countcleaningitems,
							'delivertestingitems' => $delivertestingitems,
							//'_counttestingitems' => $_counttestingitems,
							//'_totalcount' => $_totalcount
						]);
			$_retArray = array('success' => true, 'html' => $html);
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
			
			$model = Item::findOne($itemid);
			
			if($type == 1)
				$status = 'In Shipping';
			else if($type == 2)
				$status = 'In Progress';
			else if($type == 3)
				$status = 'Cleaned';
			
			$model->status = array_search($status, Item::$status);
			
			if($model->save())
			{
				//track item
				$itemlog = new Itemlog;
				$itemlog->userid = Yii::$app->user->id;
				$itemlog->status = array_search($status, Item::$status);
				$itemlog->itemid = $model->id;
				$itemlog->locationid = $model->location;
				$itemlog->save();				
			}	
		//
			$_retArray = array('success' => true);
			echo json_encode($_retArray);
			exit();			
		}
	}
	
	public function actionSaveawaitingserialized()
	{
		$_post = Yii::$app->request->post();
		
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['serial']) && !isset($_post['item'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$serial = $_post['serial'];
			
    		$id = $_post['item'];
    		
	    	$row = Item::findOne($id);
			
	    	if($row !== null) {
	    		$row->serial = $serial;
				$row->picked = date('Y-m-d H:i:s');
	    		if($row->save()){
	    			$success = true;
	    			$_retArray = array('success' => true);
	    			echo json_encode($_retArray);
	    			exit();	    			
	    		}
	    		else
	    			$errors = $row->errors;
	    	}
    	}		
	}
	
    public function actionLoadawaitingdeliverylab()
    {    	
		$count_requested_service = (int) Items::find()->where(['status'=>array_search('Requested for Service', Item::$status)])->count();
		
		$count_used_service = (int) Items::find()->where(['status'=>array_search('Used for Service', Item::$status)])->count();
		
		$max_requested_for_service = 7;

		$max_used_for_service = 3;
		
		if($count_requested_service >= $max_requested_for_service && $count_used_service >= $max_used_for_service)
		{
			$limit_requested_service = $max_requested_for_service;
			$limit_used_service = $max_used_for_service;
		} else if($count_requested_service < $max_requested_for_service && $count_used_service >= $max_used_for_service)
		{
			//echo 'hi:';
			$limit_requested_service = $max_requested_for_service-$count_requested_service;
			$limit_used_service = $max_used_for_service + $limit_requested_service;	
			//var_dump($limit_requested_service, $limit_used_service);
		}else if($count_requested_service >= $max_requested_for_service && $count_used_service < $max_used_for_service)
		{
			$limit_used_service = $max_used_for_service-$count_used_service;
			$limit_requested_service = $max_requested_for_service + $limit_used_service;			
		} else 
		{
			$limit_used_service = $max_used_for_service;
			$limit_requested_service = $max_requested_for_service;
		}
		
		$query1 = (new \yii\db\Query())
			->select("*")
			->from('lv_items')
			->where(['status'=>array_search('Requested for Service', Item::$status)])
			->limit($limit_requested_service);

		$query2 = (new \yii\db\Query())
			->select("*")
			->from('lv_items')
			->where(['status'=>array_search('Used for Service', Item::$status)])
			->limit($limit_used_service);
/* var_dump($limit_used_service);
var_dump($limit_requested_service); */
		$query1->union($query2, false);//false is UNION, true is UNION ALL
		
		$sql = $query1->createCommand()->getRawSql();
		
		$sql .= ' ORDER BY id DESC';
		
		//echo $sql;
		
		$query = Items::findBySql($sql);		
		
    	/*$_awaiting_delivery_items = Items::find()->where([])
										->limit(10)
										->all();    */	
    	
    	$_delivered_items = Item::find()->where(['status'=>array_search('Used for Service', Item::$status)])->orderBy('lastupdated DESC')->limit(5)->all(); 
		
		/*$query = Items::find()->where(['status'=>array_search('Requested for Service', Item::$status)])
							->limit(10);*/
							
    	$dataProvider = new ActiveDataProvider([
				    			'query' => $query
				    		]);  	

		//echo $dataProvider->getTotalCount();
    	
    	//var_dump($_delivered_items);
    	
    	$html = $this->renderPartial('_loadawaitingdeliverylab', [
    								'_awaiting_delivery_items'=>$_awaiting_delivery_items,
    								'_delivered_items'=>$_delivered_items,
									'dataProvider'=>$dataProvider
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
        if(Yii::$app->request->cookies->has('loginCookie') && Yii::$app->request->cookies->get('loginCookie')->value){
               $userModel = User::find()->where(['accessToken' => Yii::$app->request->cookies->get('loginCookie')->value])->one();
               $model->username = $userModel['username'];
               $model->password = $userModel['hash_password'];
               Yii::$app->user->login($userModel);
               return $this->redirect('');
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
        
        public function actionSync($model){
            if($model=='models'){
                $sql = "SELECT lv_manufacturers.name, lv_models.descrip, lv_models.image_id, 
                    IF(lv_manufacturers.name !='' and lv_models.descrip !='',CONCAT(lv_manufacturers.name,' ', lv_models.descrip),if(lv_models.descrip!='', lv_models.descrip, lv_manufacturers.name) ) as modelname,
                    lv_models.assembly, 
                    lv_models.aei, 
                    lv_models.frupartnum, 
                    lv_models.manpartnum, 
                    lv_departements.name as department,
                    lv_models.id,
                    lv_models.deleted,
                    lv_models.category_id,
                    lv_medias.filename,
                    null as partnumber,
                    null as customer,
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
                $db = \Yii::$app->common->getMongoDb();
                $collection = $db->inventorymodels;
                $collection->drop();
                $collection->batchInsert($results);
                
//                $connection = Yii::$app->getDb();
                $partNumbersSql = "select model,group_concat(DISTINCT partid) as partnumber from lv_partnumbers join lv_models on lv_models.id = lv_partnumbers.model group by model order by model";
                $partNumbers = $connection->createCommand($partNumbersSql)->queryAll();
                foreach($partNumbers as $partNumber){
                    $collection->update(array('id' => (string)$partNumber['model']), array('$set' => array('partnumber' => $partNumber['partnumber'])));
                }
                $customersSql = "select group_concat(distinct lv_items.customer) as customer, lv_models.id as model from lv_models join lv_items on lv_items.model = lv_models.id group by lv_models.id";
                $customers = $connection->createCommand($customersSql)->queryAll();
                foreach($customers as $customer){
                    $cust = explode(",", $customer['customer']);
                    $collection->update(array('id' => (string)$customer['model']), array('$set' => array('customer' => $cust)));
                }
            }
        }
	
	/*public function actionImport()
	{		

	}*/
	
	public function actionPreloadshipments()
	{
		ini_set('max_execution_time', 120);
		ini_set('memory_limit', '512M');
		/*foreach(LocationsAwg::find()->all() as $mlocation)
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
		}*/
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
        //Yii::$app->response->cookies->add('loginCookie', '');
    	Yii::$app->response->cookies->remove('loginCookie');
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