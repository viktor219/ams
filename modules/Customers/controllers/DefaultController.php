<?php

namespace app\modules\Customers\controllers;

use yii\helpers\ArrayHelper;
use app\models\Customer;
use app\components\AccessRule;
use yii\filters\AccessControl;
use app\models\CustomerSetting;
use app\models\Location;
use app\models\LocationClassment;
use app\models\LocationParent;
use app\models\Project;
use app\models\User;
use app\models\Item;
use app\models\Itemlog;
use app\models\Category;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Department;
use app\models\CustomerSearch;
use app\models\LocationSearch;
use app\models\Medias;
use app\models\LocationDetail;
use app\models\UserHasCustomer;
use app\vendor\PHelper;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller {

    const defaultPageSize = 10;
    const FILE_UPLOAD_PATH_CUSTOMER = "/public/images/customers/";
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
		    	'only' => [
							'index','create', 'update', 'view', 'delete', 'load', 
							'iload', 'loadmodels', 'search', 'delete', 'update', 
							'assignuser', 'addproject', 'locationcreate', 'locationupdate', 
							'showallprojects', 'locations', 'locationview', 'locationdelete', 'ownstockpage', 'statusdetails', 'serialdetails', 'softdelete', 'revert'
						],
		    	'rules' => [
			    	[
				    	'actions' => [
							'index','create', 'update', 'view', 'delete', 'load', 
							'iload', 'loadmodels', 'search', 'delete', 'update', 
							'assignuser', 'addproject', 'locationcreate', 'locationupdate', 
							'showallprojects', 'locations', 'locationview', 'locationdelete', 'ownstockpage', 'statusdetails', 'serialdetails', 'softdelete', 'revert'
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
                        'allow' => true,
                        'actions' => ['ownstockpage', 'statusdetails', 'serialdetails'],
                        'roles' => [User::REPRESENTATIVE],
                    ],					
		    	],
	    	]
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
        } else {

            $_index = 'index';
        }*/

        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $basket_count = Customer::find()->where(['deleted' => 1])->count();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'basket_count' => $basket_count
        ]);
        /* $pages = new Pagination(['totalCount' => $this->getCustomers(true), 'defaultPageSize' => self::defaultPageSize]);
          $allCustomers = $this->getCustomers();
          return $this->render('index', [
          'models' => $allCustomers,
          'pages' => $pages,
          ]); */
    }
    
    public function actionLoad()
    {
    
    	if (Yii::$app->request->isAjax) {
    		 
    		$dataProvider = new ActiveDataProvider([
    				'query' => Customer::find()->where(['deleted' => 0]),
    				'pagination' => ['pageSize' => 10],
                                'sort' => ['defaultOrder' => ['companyname' => SORT_ASC]]
    				]);
    		$html= $this->renderPartial('_load', [
    				'dataProvider' => $dataProvider,
    				]);
                echo json_encode(array('success' => true, 'html' => $html));
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionGetdeleted(){
       $query = Customer::find()->where(['deleted' => 1]);
       $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['companyname' => SORT_ASC]]
    	]);
       $html = $this->renderPartial('_deleted', [
            'dataProvider' => $dataProvider,
    	]);
       
       echo json_encode(array('success' => true, 'html' => $html, 'count' => $query->count()));
       exit();
    }
    
    public function actionSoftdelete($id){
        $customer = Customer::findOne($id);
        $custname = trim($customer->firstname.' '. $customer->lastname);
        $custname = empty($custname) ? $customer->companyname: $custname;
        $customer->deleted = 1;
        if($customer->save()){
            $_message = 'Customer {'.$custname.'} has been deleted successfully!!!';
            Yii::$app->getSession()->setFlash('success', $_message);
        } else {
            $_message = 'Failed!' . json_encode($customer->errors);
            Yii::$app->getSession()->setFlash('error', $_message);
        }
        return $this->redirect(['/customers/index']);
    }

    public function actionRevert($id){
        $customer = Customer::findOne($id);
        $customer->deleted = 0;
        echo $customer->save();
    }

    public function actionIload()
    {
    
    	if (Yii::$app->request->isAjax) {
    		
    		$query = Customer::find()->select('lv_customers.*')
    		->join('LEFT JOIN', 'lv_partnumbers', 'lv_partnumbers.customer =lv_customers.id')
    		->groupBy('customer');
    		 
    		$dataProvider = new ActiveDataProvider([
    					'query' => $query,
    					'pagination' => ['pageSize' => 10],
    				]);
    		 
    		echo $this->renderAjax('_iload', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }

    public function actionLoadmodels()
    {
    	//if (Yii::$app->request->isAjax) {
    
    		$data = Yii::$app->request->get();
			
			$customerid = $data['idcustomer'];
			    
			//if(Yii::$app->user->identity->usertype != 1) {
				$query = Models::find()->select('lv_models.*')
							->join('INNER JOIN', 'lv_items', 'lv_items.model =lv_models.id')
							->where("lv_items.customer=$customerid")
							->andWhere(['status'=>4, 'assembly'=>0])
							->groupBy('lv_items.model');
			/*} else {
				$query = Models::find()->select('lv_models.*')
							->join('INNER JOIN', 'lv_items', 'lv_items.model =lv_models.id')
							->where("lv_items.customer=$data[idcustomer]")
							->andWhere(['status'=>4, 'assembly'=>0])
							->groupBy('lv_items.model');				
			}*/
    		 
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 15],
    				]);
    		//echo $customerid;
    		echo $this->renderPartial('_models', [
    				'dataProvider' => $dataProvider,
    				'customerid' => $customerid,
    				]);
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
	
	public function actionRaddinventory()
	{
		$data = Yii::$app->request->post();
		
		foreach($data['additional_model'] as $key=>$modelid)
		{
			$model = Models::findOne($modelid);
			$quantity = $data['additional_model_qty'][$key];
			
			if($model->serialized)
			{
				
			} else {
				$_find_item_model = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Stock', Item::$status)])->one();
			}
		}
	}
	
	public function actionLoadinventorystats()
	{
		$_post = Yii::$app->request->get();
		 
		//if (Yii::$app->request->isAjax) {
		$_retArray = array('success' => FALSE, 'html' => '');
		if (!isset($_post['page'])) {
			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
			echo json_encode($_retArray);
			exit();
		}
			
		$page = $_post['page'];
		$_maxlimit = 6;
		$startLimit = $_maxlimit * ($page - 1);
		//$endLimit = $_maxlimit * $page;
			
		$totalitems = Item::find()->select('customer')
		->where(['status'=>array_search('In Stock', Item::$status)])
		->orWhere(['status'=>array_search('Ready to ship', Item::$status)])
		->groupBy('customer')
		->orderBy('count(model) DESC')
		->count();
		//echo $totalitems;
                $hasNext = true;
                if($totalitems < ($_maxlimit * $page)){
                    $hasNext = false;
                }
                $prevPage = $page - 1;
		if($startLimit < $totalitems) {
			$nextpage = $page + 1;
			//$endLimit = $endLimit - 2;
		}
		else {
			$nextpage = 2;
			//$endLimit = $_maxlimit;
			//$startLimit = $_maxlimit;
			$startLimit = 0;
		}
		$thisWeekMonday = date('Y-m-d', strtotime('Monday this week'));
		$connection = Yii::$app->getDb();
		//                                $sql = 'SELECT customer FROM `lv_items` where status IN ( :status_in_stock, :status_ready_ship) group by customer order by count(model) desc limit :start_limit, :offset';
		//                                $command = $connection->createCommand($sql)
		//                                        ->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
		//                                        ->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status))
		//                                        ->bindValue(':start_limit', $startLimit)
		//                                        ->bindValue(':offset', $_maxlimit);
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
		//                                uasort($inventories, function ($a, $b) {
		//                                    return (abs($a['percent']) > abs($b['percent'])) ? -1 : 1;
		//                                });
		$sql = 'select (((sum(t.test) - (sum(t.total))) / sum(t.test))* 100 ) as percent, abs((((sum(t.test) - (sum(t.total))) / sum(t.test))* 100 )) as absolute_percent, t.customer, sum(t.test) as count from ((SELECT count(*) as total, customer, "0" as test FROM `lv_items` WHERE status = :status_in_stock and DATE_FORMAT(lastupdated,"%Y-%m-%d") < :last_monday group by customer) UNION (SELECT "0" as total, customer, count(*) as test FROM `lv_items` WHERE status = :status_in_stock group by customer))t group by t.customer order by absolute_percent desc limit :start_limit, :offset';
		$inventories = $connection->createCommand($sql)
		->bindValue(':status_in_stock', array_search('In Stock', Item::$status))
//		->bindValue(':status_ready_ship', array_search('Ready to ship', Item::$status))
		->bindValue(':last_monday', $thisWeekMonday)
		->bindValue(':start_limit', $startLimit)
		->bindValue(':offset', $_maxlimit)
		->queryAll();
		$html = $this->renderPartial('_loadinventorystats', [
				'inventories' => $inventories,
				]);
		$_retArray = array('success' => true, 'html' => $html, 'nextpage'=>$nextpage, 'hasNext' => $hasNext, 'prevPage' => $prevPage);
		echo json_encode($_retArray);
		exit();
		//}
	}
	
	public function actionLoadshipmentsclassments()
	{
    	$_post = Yii::$app->request->get();
    	  
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');  
    		if (!isset($_post['page'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit(); 
    		}
			
			$page = $_post['page'];
			$_maxlimit = 4;
			$startLimit = $_maxlimit * ($page - 1);
			$sql = "SELECT COUNT(DISTINCT(customer_id))
						FROM lv_shipments
						INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
						INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id	
					";
					
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand($sql);
		
			$totalitems = $command->queryScalar();		
                        $hasNext = true;
                        if($totalitems < ($_maxlimit * $page)){
                            $hasNext = false;
                        }
                        $prevPage = $page - 1;                        
			if($startLimit < $totalitems)
				$nextpage = $page + 1;
			else {
				$nextpage = 2;
				$startLimit = 0;
			}	
			
			$sql = "SELECT companyname, COUNT(*) as nb_customer_shipments
						FROM lv_shipments
						INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
						INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id	
						GROUP BY customer_id
						ORDER BY nb_customer_shipments DESC
						LIMIT 4 OFFSET :offset
					";
					
			$connection = Yii::$app->getDb();
			
			$command = $connection->createCommand($sql, [':offset'=>$startLimit]);
		
			$shipments = $command->queryAll();		
                        
                        $sql = 'SELECT  COUNT(*) FROM lv_shipments
						INNER JOIN lv_salesorders ON lv_shipments.orderid = lv_salesorders.id
						INNER JOIN lv_customers ON lv_salesorders.customer_id = lv_customers.id';
                        $command = $connection->createCommand($sql);		
			$total_shipments = $command->queryColumn();
                        $html = $this->renderPartial('_loadshipmentsclassments', [
						'shipments' => $shipments,
                                                'total_shipments' => $total_shipments
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'nextpage'=>$nextpage, 'hasNext' => $hasNext, 'prevPage' => $prevPage);
    		echo json_encode($_retArray);
    		exit();			
			
		//}
	}
	
	public function actionOwnstockpage($id, $location=null)
	{		
		$customer = Customer::findOne($id);
		$location = Location::findOne($location);
	//
		$categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
							->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
							->where(['customer'=>$id])
							->groupBy('`lv_models`.`category_id`')
							->orderBy('categoryname')
							->all();
		//
        return $this->render('_stockoverview', [
			//'dataProvider' => $dataProvider,
			'customer' => $customer,
			'categories' => $categories,
			'_location' => $location
        ]);		
	}
	
	public function actionLoadrmamodelslocation()
	{
		$_post = Yii::$app->request->get();
		
    	//if (Yii::$app->request->isAjax) {
			$connection = Yii::$app->getDb();
			
    		$_retArray = array('success' => FALSE, 'html' => ''); 
			
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit(); 
    		}
			
			$locationid = $_post['id'];		
			
			$customerid = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;
			
			//var_dump(Yii::$app->user->id);
			
			$sql = "SELECT *
						FROM lv_items  
						INNER JOIN lv_models ON lv_items.model = lv_models.id
						LEFT JOIN lv_manufacturers ON lv_manufacturers.id = lv_models.manufacturer
						WHERE customer = :customer AND location = :location 
						GROUP BY model
						ORDER BY name, descrip 
						";
			
			$command = $connection->createCommand($sql, [':customer'=>$customerid, ':location'=>$locationid]);
		
			$items = $command->queryAll();	

			$count = count($items);
			 
			$_location_detail = LocationDetail::find()->where(['locationid'=>$locationid])->one();
			
			$html = $this->renderPartial('_loadrmamodelslocation', [
				'customer' => Customer::findOne($customerid),
				'modelitems' => $items,			
				'_location' => Location::findOne($locationid),
				'_location_details' => $_location_detail
			]);	
			
    		$_retArray = array('success' => true, 'html' => $html, 'count' => $count);
    		echo json_encode($_retArray);
    		exit();	
		//}
	}
	
	/*public function actionLocations($customer)
	{
		
	}*/
	
	public function actionStatusdetails($model, $customer, $status)
	{
		$customer = Customer::findOne($customer);
		
		$model = Models::findOne($model);
		
		$query = Item::find()->where(['customer'=>$customer->id, 'model'=>$model->id, 'status'=>$status]);
		
		$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['pageSize' => 15],
			]);		
		
        return $this->render('_statusdetails', [
			'dataProvider' => $dataProvider,
			'customer' => $customer,
			'model' => $model,
			'status' => $status
        ]);			
	}
	
	public function actionStatuslog($model, $customer)
	{
		$customer = Customer::findOne($customer);
		
		$model = Models::findOne($model);
		
        return $this->render('_statuslog', [
			'customer' => $customer,
			'model' => $model
        ]);		
	}
	
	public function actionSerialdetails($id)
	{
		$item = Item::findOne($id);
		
		$customer = Customer::findOne($item->customer);
		
		$model = Models::findOne($item->model);
		
		$location = Location::findOne($item->location);
		
		$manufacturer = Manufacturer::findOne($model->manufacturer);
		
		$query = Itemlog::find()->where(['itemid'=>$id]);
		
		$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['pageSize' => 15],
			]);
		
        return $this->render('_serialdetails', [
			'dataProvider' => $dataProvider,
			'item' => $item,
			'customer' => $customer,
			'model' => $model,
			'manufacturer' => $manufacturer,
			'location' => $location,
        ]);				
	}
	
	public function actionLoadeditcategoryform()
	{
    	$_post = Yii::$app->request->get();
    	  
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');  
    		if (!isset($_post['id'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit(); 
    		}
     
    		$modelid = $_post['id'];
			
			$model = Models::findOne($modelid);
			
			$_manufacturer = Manufacturer::findOne($model->manufacturer);
			
			$category = Category::findOne($model->category_id);
			
    		$html = $this->renderPartial('_loadeditcategoryform', [
    					'model' => $model,
    					'category' => $category,
						'categories' => Category::find()->orderBy('categoryname')->all(),
						'departments' => Department::find()->where('name != ""')->orderBy('name')->all()
    				]);
    
    		$_retArray = array('success' => true, 'html' => $html, 'modelname' => $_manufacturer->name . ' ' . $model->descrip . ' (<b>' . $category->categoryname . '</b>)');
    		echo json_encode($_retArray);
    		exit();			
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
	}
	
	public function actionUpdatemodeldata()
	{
		$_post = Yii::$app->request->post();
		
		//if (Yii::$app->request->isAjax) {
			$_retArray = array('success' => FALSE, 'html' => '');
			if (!isset($_post['modelId'])) {
				$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
				echo json_encode($_retArray);
				exit();
			}
			$model = Models::findOne($_post['modelId']);	
			
			$category = $_post['category'];	
			
			$department = $_post['department'];	
			
			$model->category_id = $category;
			
			$model->department = $department;
			
			if($model->save())
				$_retArray = array('success' => true, 'html' => 'Model has been successfully updated!', 'newcategoryid' => $category, 'departmentname' => strtoupper(Department::findOne($department)->name), 'categoryname' => ucfirst(strtolower(Category::findOne($category)->categoryname)));	
			
			echo json_encode($_retArray);
			exit();
		//}
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
    
    		$searchModel = new CustomerSearch();
    		$dataProvider = $searchModel->search(['CustomerSearch'=>['code'=>$query, 'companyname'=>$query, 'firstname'=>$query, 'lastname'=>$query]]);
    	  
    		$html = $this->renderPartial('_load', [
    					'dataProvider' => $dataProvider,
    				]);
    
    		$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    		echo json_encode($_retArray);
    		exit();
   		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }    

    /**
     * 
     * @param type $id
     * @return type
     * delete a customer
     */
    public function actionDelete($id) {
        if(!isset($id) || $id<=0){
            
             throw new NotFoundHttpException('The requested page does not exist.');
        }
//        $this->findModel($id)->delete();
        if($this->findModel($id)->delete()){
            $_message = 'Customer has been deleted successfully!!!';
            Yii::$app->getSession()->setFlash('success', $_message);
        }        
        $UserLocation = new Location();
        $UserLocation->deleteAll(
                'customer_id = :customer_id', array('customer_id' => $id)
        );
        return $this->redirect(['index']);
    }

    /**
     * 
     * @return type
     */
    public function actionUpdate($id) {
    	
    	$data = Yii::$app->request->post();
		
		$customer = $this->findModel($id);

        if (empty($data)) {
			$customer_settings = CustomerSetting::find()->where(['customerid'=>$customer->id])->one();
			if($customer_settings===null)
				$customer_settings = array();
			//var_dump($customer_settings);
			$_new_location_id = $customer->defaultshippinglocation;
			$locationShipping = Location::findOne($_new_location_id);
			$_new_location_id = $customer->defaultbillinglocation;
			$locationBilling = Location::findOne($_new_location_id);
			$findProjects = Customer::find()->where(['parent_id' =>null])->all();
			return $this->render('update', [
				'customer' => $customer,
				'customer_settings' => $customer_settings,
				'locationShipping' => $locationShipping,
				'locationBilling' => $locationBilling,
				'findProjects'=>$findProjects
			]);
        } else {

            /** Customer form validation save * */
            $_backTrack = false;
            $errors = '';
            $_second_location = 0;
            $_new_media_id = 0;
            if (!$_backTrack) {
            	
                if (isset($_FILES["fileToUpload"]['name']) && !empty($_FILES["fileToUpload"]['name'])) {

                    $_result = $this->uploadMedia($_FILES["fileToUpload"], 'image');
                    if (is_array($_result)) {

                        $_uploaded_file_name = $_result['filename'];
                        $media = new Medias();
                        $media->filename = $_uploaded_file_name;
                        $media->path = self::FILE_UPLOAD_PATH_CUSTOMER;
                        $media->type = 1;
                        $media->save();
                        $_new_media_id = $media->id;
                    } else {

                        $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . $_result . '</div>';
                        Yii::$app->getSession()->setFlash('error', $_message);
                        return $this->redirect('index');
                    }
                }

                $customer_settings = CustomerSetting::findOne($customer->id);
                if($customer_settings===null){
                	$customer_settings = new CustomerSetting;     
                	$customer_settings->customerid = $customer->id;
                }
                $customer_settings->default_account_number = $_POST['defaultaccountnumber'];
                $customer_settings->default_shipping_method = $_POST['defaultshippingmethod'];
                $customer_settings->secondary_account_number = $_POST['secondaryaccountnumber'];
                $customer_settings->secondary_shipping_method = $_POST['secondaryshippingmethod'];
                $customer_settings->save();                        
                //
                if ($_new_media_id > 0)
					$customer->picture_id = $_new_media_id;
                $customer->code = $_POST['customercode'];
                $customer->companyname = $_POST['companyname'];
                $customer->firstname = $_POST['contactname1'];
                $customer->lastname = $_POST['contactname2'];
                $customer->email = $_POST['email'];
                $customer->phone = $_POST['phone'];
                $customer->defaultshippingchoice = $_POST['defaultshippingchoice'];
                $customer->payment_terms_id = $_POST['payment_terms'];
                
                if (isset($_POST['parentId']))
                    $customer->parent_id = $_POST['parentId'];

                if (!isset($_POST['requireserialnumber']))
                    $customer->trackincomingserials = 0;
                else
                    $customer->trackincomingserials = 1;
				
                if (!isset($_POST['requireordernumber']))
                    $customer->requireordernumber = 0;
                else
                    $customer->requireordernumber = 1; 
				
                if (!isset($_POST['allownewcustomerorder']))
                    $customer->allownewcustomerorder = 0;
                else
                    $customer->allownewcustomerorder = 1;

                if (!isset($_POST['allowdirectshippingreq']))
                    $customer->allowdirectshippingreq = 0;
                else
                    $customer->allowdirectshippingreq = 1;

                if (!isset($_POST['allowweeklyautorderreq']))
                    $customer->allowweeklyautorderreq = 0;
                else
                    $customer->allowweeklyautorderreq = 1;	

                if (!isset($_POST['allowincomingoutshchedule']))
                    $customer->allowincomingoutshchedule = 0;
                else
                    $customer->allowincomingoutshchedule = 1;	

                if (!isset($_POST['temporaryinventorystatus']))
                    $customer->temporaryinventorystatus = 0;
                else
                    $customer->temporaryinventorystatus = 1;	

                if (!isset($_POST['requirestorenumber']))
                    $customer->requirestorenumber = 0;
                else
                    $customer->requirestorenumber = 1;	

                if (!isset($_POST['requirepalletcount']))
                    $customer->requirepalletcount = 0;
                else
                    $customer->requirepalletcount = 1;	

                if (!isset($_POST['requireboxcount']))
                    $customer->requireboxcount = 0;
                else
                    $customer->requireboxcount = 1;

                if (!isset($_POST['requirelanenumber']))
                    $customer->requirelanenumber = 0;
                else
                    $customer->requirelanenumber = 1;		

                if (!isset($_POST['requirelabelmodel']))
                    $customer->requirelabelmodel = 0;
                else
                    $customer->requirelabelmodel = 1;	

                if (!isset($_POST['requirelabelbox']))
                    $customer->requirelabelbox = 0;
                else
                    $customer->requirelabelbox = 1;	

                if (!isset($_POST['requirelabelpallet']))
                    $customer->requirelabelpallet = 0;
                else
                    $customer->requirelabelpallet = 1;	
                
                if (!isset($_POST['customerstoreinventory']))
                	$customer->customerstoreinventory = 0;
                else
                	$customer->customerstoreinventory = 1;              

                $customer->defaultreceivinglocation = $_POST['defaultreceivinglocation'];

                    if ($customer->save()) {
                    	//
                           	$modelLocation = $this->findLocationModel($customer->defaultshippinglocation, false);
                        if($modelLocation === null)
                            $modelLocation = new Location();
						/*if(empty($_POST['shippinglocation'])) {
							$modelLocation->customer_id = $customer->id;
							$modelLocation->address = $_POST['shipping_address'];
							$modelLocation->address2 = $_POST['shipping_address_2'];
							$modelLocation->country = $_POST['shipping_country'];
							$modelLocation->city = $_POST['shipping_city'];
							$modelLocation->state = $_POST['shipping_state'];
							$modelLocation->zipcode = $_POST['shipping_zip'];						
                            if ($modelLocation->save()) {
								$_first_location = $modelLocation->id;
                            } else
                                $errors .= $modelLocation->errors;
						}*/
					//
						if (isset($_POST['billing_address']) && !empty($_POST['billing_address'])) {
	                           	$modelLocation = $this->findLocationModel($customer->defaultbillinglocation, false);
	                        if(empty($modelLocation))
	                            $modelLocation = new Location();
	                        //var_dump($customer->defaultbillinglocation, $modelLocation);exit(1);
							$modelLocation->customer_id = $customer->id;
							$modelLocation->address = $_POST['billing_address'];
							$modelLocation->address2 = $_POST['billing_address_2'];
							$modelLocation->country = $_POST['billing_country'];
							$modelLocation->city = $_POST['billing_city'];
							$modelLocation->state = $_POST['billing_state'];
							$modelLocation->zipcode = $_POST['billing_zip'];
							if ($modelLocation->save()) {
								$_second_location = $modelLocation->id;
							} else {
								$errors .= $modelLocation->errors;
							}
						}					
                    } else 
                        $errors .= $customer->errors;
            }

            if (empty($errors)) {

                /**
                 * Updating customers default locations
                 */
                //$customer = $this->findModel($_new_customer_id);
				//if(empty($_POST['shippinglocation']))
					//$customer->defaultshippinglocation = $_first_location;
				//else
            		if(!empty($_POST['shippinglocation']))
						$customer->defaultshippinglocation = $_POST['shippinglocation'];
                $customer->defaultbillinglocation = $_second_location;
                $customer->save();

                $_message = '<div class="alert alert-success"><strong>Success!</strong> Customer has been updated successfully!</div>';
                Yii::$app->getSession()->setFlash('success', $_message);
            } else {
                /* if customer is created but some how any error is created then we will delete that new customer * /
                 * 
                 */
                   $customer->delete();
                if (isset($customer->defaultshippinglocation))
                    $this->findLocationModel($customer->defaultshippinglocation)->delete();
                if (isset($_second_location) && $_second_location > 0)
                    $this->findLocationModel($_second_location)->delete();

                $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);
            }
            return $this->redirect('index');
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionAssignuser() {

        if (Yii::$app->request->isAjax) {
             $user_id = Yii::$app->user->identity->id;
            /* Only when Ajax request is cretaed for loading assign user to customer form * */
            if (1) {
                if( Yii::$app->user->identity->usertype===User::TYPE_ADMIN || 
                    Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN || 
                    Yii::$app->user->identity->usertype===User::TYPE_SALES || 
                    Yii::$app->user->identity->usertype===User::TYPE_SHIPPING ||
                    Yii::$app->user->identity->usertype===User::TYPE_BILLING)
                $projects = Customer::find()->where(['owner_id' => $user_id])->all();
                else if(Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
                    $projects = Customer::find()->where(['owner_id' => $user_id])->all();
                else 
                $projects = Customer::find()->all();   
                $users = User::find()->all();
                $html = $this->renderAjax('_formassignuser', [
                    'projects' => $projects,
                    'users' => $users
                ]);
            } else {
                $html = "Something is wrong! Please try again.";
            }

            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['userId']) && !empty($_POST['userId'])) {
            /** Customer form validation save * */
            $_backTrack = false;
            $errors = '';
            $customer = new Project();
            $_new_media_id = 0;
            if (!$_backTrack) {
                $_uid = $_POST['userId'];
                $_cid = $_POST['projectId'];
                $_alls = UserHasCustomer::find()->where(['userid' => $_uid])->all();
                if(isset($_alls[0]['userid'])){
                    $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . "Because already assigned!" . '</div>';
                     Yii::$app->getSession()->setFlash('error', $_message);
                    return $this->redirect('index');
                }
                $_alls = UserHasCustomer::find()->where(['customerid'=>$_cid])->all();
                if(isset($_alls[0]['userid'])){
                    $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . "Because already assigned!" . '</div>';
                     Yii::$app->getSession()->setFlash('error', $_message);
                    return $this->redirect('index');
                }
                $mediaUser = new UserHasCustomer();
                $mediaUser->userid = $_POST['userId'];
                $mediaUser->customerid = $_POST['projectId'];
                $mediaUser->save();
                
            }

            if (!$_backTrack) {

                $_message = '<div class="alert alert-success"><strong>Success!</strong> User has been assigned successfully!</div>';
                Yii::$app->getSession()->setFlash('success', $_message);
            } else {
                $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);
            }
            return $this->redirect('index');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionAddproject() {

        if (Yii::$app->request->isAjax) {

            /* Only when Ajax request is cretaed for loading project creation form * */
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $present_id = $_GET['id'];
                $customer = array();
                $html = $this->renderAjax('addproject', [
                    'customer' => $customer,
                    'present_id' => $present_id
                ]);
            } else {
                $html = "Something is wrong! Please try again.";
            }

            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['companyname']) && !empty($_POST['companyname'])) {
            /** Customer form validation save * */
            $_backTrack = false;
            $errors = '';
            $customer = new Project();
            $_new_media_id = 0;
            if (!$_backTrack) {

                if (isset($_FILES["fileToUpload"]['name']) && !empty($_FILES["fileToUpload"]['name'])) {

                    $_result = $this->uploadMedia($_FILES["fileToUpload"], 'image');
                    if (is_array($_result)) {

                        $_uploaded_file_name = $_result['filename'];
                        $media = new Medias();
                        $media->filename = $_uploaded_file_name;
                        $media->path = self::FILE_UPLOAD_PATH_CUSTOMER;
                        $media->type = 1;
                        $media->save();
                        $_new_media_id = $media->id;
                    } else {

                        $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . $_result . '</div>';
                        Yii::$app->getSession()->setFlash('error', $_message);
                        return $this->redirect('index');
                    }
                }
                $customer->picture_id = $_new_media_id;
                $customer->parent_id = $_POST['customerId'];
                $user_id = Yii::$app->user->identity->id;
                $customer->owner_id = $user_id;
                $customer->companyname = $_POST['companyname'];



                if (!isset($_POST['requireserialnumber']))
                    $customer->trackincomingserials = 0;
                if (!isset($_POST['requireordernumber']))
                    $customer->requireordernumber = 0;

                if ($customer->save()) {

                } else {
                    $errors = $customer->errors;
                    $_backTrack = true;
                }
                
            }

            if (!$_backTrack) {

                $_message = '<div class="alert alert-success"><strong>Success!</strong> Project has been added successfully!</div>';
                Yii::$app->getSession()->setFlash('success', $_message);
            } else {
                $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);
            }
            return $this->redirect('index');
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creating customer and loading customer creation form
     */
    public function actionCreate() {
    	
    	$data = Yii::$app->request->post();
    	
    	$customer = new Customer();
    	
    	$customer_setting = new CustomerSetting();

        if (empty($data)) {
            $findProjects = Customer::find()->where(['parent_id' =>null])->all();
           return $this->render('create', [
                'customer' => $customer,
            	'customer_settings' => $customer_settings,
                'locationShipping' => array(),
                'locationBilling' => array(),
                'findProjects'=>$findProjects
            ]);
        } else {
        	//var_dump($data);exit(1);
            /** Customer form validation save * */
            $_backTrack = false;
            $user_id = Yii::$app->user->identity->id;
            $errors = '';        
            $_second_location = 0;
            $_new_media_id = 0;
            if (!$_backTrack) {
				//upload pictures
                if (isset($_FILES["fileToUpload"]['name']) && !empty($_FILES["fileToUpload"]['name'])) {
                    $_result = $this->uploadMedia($_FILES["fileToUpload"], 'image');
                    if (is_array($_result)) {
                        $_uploaded_file_name = $_result['filename'];
                        $media = new Medias();
                        $media->filename = $_uploaded_file_name;
                        $media->path = self::FILE_UPLOAD_PATH_CUSTOMER;
                        $media->type = 1;
                        $media->save();
                        $_new_media_id = $media->id;
                    } else {
                        $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . $_result . '</div>';
                        Yii::$app->getSession()->setFlash('error', $_message);
                        return $this->redirect('index');
                    }
                }
				//
                $customer->picture_id = $_new_media_id;
                $customer->code = $_POST['customercode'];
                $customer->companyname = $_POST['companyname'];
                $customer->firstname = $_POST['contactname1'];
                $customer->lastname = $_POST['contactname2'];
                $customer->email = $_POST['email'];
                $customer->phone = $_POST['phone'];
                $customer->owner_id = $user_id;
                $customer->defaultshippingchoice = $_POST['defaultshippingchoice'];
                $customer->payment_terms_id = $_POST['payment_terms'];
                

                if (isset($_POST['parentId']) && $_POST['parentId'] >0)
                    $customer->parent_id = $_POST['parentId'];
                
                if (!isset($_POST['requireserialnumber']))
                    $customer->trackincomingserials = 0;
                else
                    $customer->trackincomingserials = 1;
				
                if (!isset($_POST['requireordernumber']))
                    $customer->requireordernumber = 0;
                else
                    $customer->requireordernumber = 1; 
				
                if (!isset($_POST['allownewcustomerorder']))
                    $customer->allownewcustomerorder = 0;
                else
                    $customer->allownewcustomerorder = 1;

                if (!isset($_POST['allowdirectshippingreq']))
                    $customer->allowdirectshippingreq = 0;
                else
                    $customer->allowdirectshippingreq = 1;

                if (!isset($_POST['allowweeklyautorderreq']))
                    $customer->allowweeklyautorderreq = 0;
                else
                    $customer->allowweeklyautorderreq = 1;	

                if (!isset($_POST['allowincomingoutshchedule']))
                    $customer->allowincomingoutshchedule = 0;
                else
                    $customer->allowincomingoutshchedule = 1;	

                if (!isset($_POST['temporaryinventorystatus']))
                    $customer->temporaryinventorystatus = 0;
                else
                    $customer->temporaryinventorystatus = 1;	

                if (!isset($_POST['requirestorenumber']))
                    $customer->requirestorenumber = 0;
                else
                    $customer->requirestorenumber = 1;	

                if (!isset($_POST['requirepalletcount']))
                    $customer->requirepalletcount = 0;
                else
                    $customer->requirepalletcount = 1;	

                if (!isset($_POST['requireboxcount']))
                    $customer->requireboxcount = 0;
                else
                    $customer->requireboxcount = 1;

                if (!isset($_POST['requirelanenumber']))
                    $customer->requirelanenumber = 0;
                else
                    $customer->requirelanenumber = 1;		

                if (!isset($_POST['requirelabelmodel']))
                    $customer->requirelabelmodel = 0;
                else
                    $customer->requirelabelmodel = 1;	

                if (!isset($_POST['requirelabelbox']))
                    $customer->requirelabelbox = 0;
                else
                    $customer->requirelabelbox = 1;	

                if (!isset($_POST['requirelabelpallet']))
                    $customer->requirelabelpallet = 0;
                else
                    $customer->requirelabelpallet = 1;				

                if (!isset($_POST['customerstoreinventory']))
                	$customer->customerstoreinventory = 0;
                else
                	$customer->customerstoreinventory = 1;              

                $customer->defaultreceivinglocation = $_POST['defaultreceivinglocation'];

                if ($customer->validate()) {

                    if ($customer->save()) {

                        $_new_customer_id = $customer->id;
						//save customer setting.
                        $customer_setting->customerid = $customer->id;
                        $customer_setting->default_account_number = $_POST['defaultaccountnumber'];
                        $customer_setting->default_shipping_method = $_POST['defaultshippingmethod'];
                        $customer_setting->secondary_account_number = $_POST['secondaryaccountnumber'];
                        $customer_setting->secondary_shipping_method = $_POST['secondaryshippingmethod'];
                        $customer_setting->save();
                        //save customer shipping locations
						if(!empty($_POST['shippinglocation'])) {
							/*$modelLocation = new Location();
							$modelLocation->customer_id = $_new_customer_id;
							$modelLocation->address = $_POST['shipping_address'];
							$modelLocation->address2 = $_POST['shipping_address_2'];
							$modelLocation->country = $_POST['shipping_country'];
							$modelLocation->city = $_POST['shipping_city'];
							$modelLocation->state = $_POST['shipping_state'];
							$modelLocation->zipcode = $_POST['shipping_zip'];
							if ($modelLocation->save()) {
								$_first_location = $modelLocation->id;
							} else {

								$_backTrack = true;
							}*/
							$modelLocation = Location::findOne($_POST['shippinglocation']);
							$modelLocation->customer_id = $_new_customer_id;
							if ($modelLocation->save()) {
								$_first_location = $modelLocation->id;
							} else {
							
								$_backTrack = true;
							}							
						}
						//customer billing location saving
						if (isset($_POST['billing_address']) && !empty($_POST['billing_address'])) {
							$modelLocation = new Location();
							$modelLocation->customer_id = $_new_customer_id;
							$modelLocation->address = $_POST['billing_address'];
							$modelLocation->address2 = $_POST['billing_address_2'];
							$modelLocation->country = $_POST['billing_country'];
							$modelLocation->city = $_POST['billing_city'];
							$modelLocation->state = $_POST['billing_state'];
							$modelLocation->zipcode = $_POST['billing_zip'];
							if ($modelLocation->save()) {
								$_second_location = $modelLocation->id;
							} else {
								$_backTrack = true;
							}
						}	
						//
						PHelper::generateNonSOCustomerJson($_new_customer_id);
                    } else {
                        $errors = $customer->errors;
                    }
                } else {
                    $errors = $customer->errors;
                    $_backTrack = true;
                }
            }

            if (!$_backTrack) {

                /**
                 * Updating customers default locations
                 */
                $customer = $this->findModel($_new_customer_id);
				if(empty($_POST['shippinglocation']))
					$customer->defaultshippinglocation = $_first_location;
				else
					$customer->defaultshippinglocation = $_POST['shippinglocation'];
                $customer->defaultbillinglocation = $_second_location;
                $customer->save();

                $_message = '<div class="alert alert-success"><strong>Success!</strong> Customer has been created successfully!</div>';
                Yii::$app->getSession()->setFlash('success', $_message);
            } else {
                /* if customer is created but some how any error is created then we will delete that new customer * /
                 * 
                 */
                if (isset($_new_customer_id))
                    $this->findModel($_new_customer_id)->delete();
                if (isset($_first_location))
                    $this->findLocationModel($_first_location)->delete();
                if (isset($_second_location) && $_second_location > 0)
                    $this->findLocationModel($_second_location)->delete();

                $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                Yii::$app->getSession()->setFlash('error', $_message);
            }
            return $this->redirect('index');
        }
    }

    /**
     * 
     * @return type
     */
    public function actionLocationcreate() {

        if (Yii::$app->request->isAjax) {

            /* Only when Ajax request is cretaed for loading location creation form * */
            $location = array();
            if (isset($_GET['customer']) && $_GET['customer'] > 0) {
                $_customer_id = $_GET['customer'];
                $html = $this->renderAjax('locationcreate', [
                    'location' => $location,
                    '_customer_id' => $_customer_id
                ]);
            } else {
                $html = "Something is wrong! Please try again.";
            }
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['location_address']) && !empty($_POST['location_address'])) {

            if (isset($_POST['customerId']) && !empty($_POST['customerId'])) {

                $_customer_id = $_POST['customerId'];
                $modelLocation = new Location();
                $modelLocation->customer_id = $_customer_id;
                $modelLocation->address = $_POST['location_address'];
                $modelLocation->country = $_POST['location_country'];
                $modelLocation->city = $_POST['location_city'];
                $modelLocation->state = $_POST['location_state'];
                $modelLocation->zipcode = $_POST['location_zip'];
                $modelLocation->email = $_POST['location_email'];
                $modelLocation->phone = $_POST['location_phone'];
                $modelLocation->storenum = $_POST['storenum'];
                if ($modelLocation->validate()) {
                    $modelLocation->save();
                    $_message = '<div class="alert alert-success"><strong>Success!</strong> Location has been created successfully!</div>';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $errors = $modelLocation->errors;
                    $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                    Yii::$app->getSession()->setFlash('error', $_message);
                }
                //$customer = $this->findModel($customer->id);
                return $this->redirect('locations/?customer=' . $_customer_id);
            } else {

                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionLocationupdate() {

        if (Yii::$app->request->isAjax) {

            /* Only when Ajax request is cretaed for loading location update form * */
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $_locationr_id = $_GET['id'];
                $location = $this->findLocationModel($_locationr_id);
                $_customer_id = 0;
                $html = $this->renderAjax('locationcreate', [
                    'location' => $location,
                    '_customer_id' => $_customer_id
                ]);
            } else {

                $html = "Something is wrong! Please try again.";
            }
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else if (isset($_POST['location_address']) && !empty($_POST['location_address'])) {

            if (isset($_POST['customerId']) && !empty($_POST['customerId']) && isset($_POST['locationId']) && !empty($_POST['locationId'])) {

                $_customer_id = $_POST['customerId'];
                $_location_id = $_POST['locationId'];
                $modelLocation = $this->findLocationModel($_location_id);
                $modelLocation->customer_id = $_customer_id;
                $modelLocation->address = $_POST['location_address'];
                $modelLocation->country = $_POST['location_country'];
                $modelLocation->city = $_POST['location_city'];
                $modelLocation->state = $_POST['location_state'];
                $modelLocation->zipcode = $_POST['location_zip'];
                $modelLocation->email = $_POST['location_email'];
                $modelLocation->phone = $_POST['location_phone'];
                $modelLocation->storenum = $_POST['storenum'];
                if ($modelLocation->validate()) {
                    $modelLocation->save();
                    $_message = '<div class="alert alert-success"><strong>Success!</strong> Location has been updated successfully!</div>';
                    Yii::$app->getSession()->setFlash('success', $_message);
                } else {
                    $errors = $modelLocation->errors;
                    $_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
                    Yii::$app->getSession()->setFlash('error', $_message);
                }
                //$customer = $this->findModel($customer->id);
                return $this->redirect('locations/?customer=' . $_customer_id);
            } else {

                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     * view of a customer and only ajax request is allowed either will through exception
     */
    public function actionView() {

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
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @throws NotFoundHttpException
     */
    public function actionShowallprojects() {

        if (Yii::$app->request->isAjax) {

            $_retArray = array('success' => FALSE, 'html' => '');
            $_post = Yii::$app->request->get();
            if (!isset($_post['id'])) {
                $_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
                echo json_encode($_retArray);
                exit();
            }
            $id = $_post['id'];
            $find = Customer::find()->where(['parent_id' => $id])->all();
            //print_r($find[0]['id']);exit();
            $html = $this->renderAjax('showAllProjectsView', [
                'model' => $find
            ]);
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionMsavelocations($id)
	{
		$data = Yii::$app->request->post();
		
		$customer = Customer::findOne($id);
		$successorder = false;
		
		$_model = new LocationParent;
		$_model->parent_name = $data['parentname'];
		if($_model->save())
			$successorder = true;
		else 
			$successorder = false;
		//
		$locations = $data['locations'];
		
		foreach($locations as $location)
		{
			$model = new LocationClassment;
			$model->parent_id = $_model->id;
			$model->location_id = $location;
			if($model->save())
				$successorder = true;
			else 
				$successorder = false;
		}		
		
		if($successorder === true){
			$_message = '<div class="alert alert-success fade in"><strong>Success!</strong> Locations adding to folder has been successfully executed!</div>';
			Yii::$app->getSession()->setFlash('success', $_message);
		} else{
			$errors = json_encode($_model->errors) . '<br/>' . json_encode($model->errors);
			$_message = '<div class="alert alert-danger fade in"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
			Yii::$app->getSession()->setFlash('error', $_message);        		
		}		
		//
		return $this->redirect(Yii::$app->request->referrer);
	}

    /**
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionLocations($customer = null) {

        /**
         * Mobile or Desktop Detetcion
         */
        /*if (Yii::$app->mobileDetect->isMobile()) {

            $_index = 'locationindex_mobile';
        } else {

            $_index = 'locationindex';
        }*/
		/*if($customer !== null)
			$_render = "_replocation_form";
		else 
			$_render = */
		if($customer === null)
		{
			$searchModel = new LocationSearch();
			if (isset($_GET['customer']))
				$customer = $_GET['customer'];
			else
				throw new NotFoundHttpException('The requested page does not exist.');
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $customer);
			$customerName = $this->findModel($customer);
			return $this->render('locationindex', [
						'searchModel' => $searchModel,
						'dataProvider' => $dataProvider,
						'customerName' => $customerName->firstname." ".$customerName->lastname
			]);
		} else {
			//$location_classments = LocationClassment::find()->select('location_id')->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->where(['customer_id'=>$customer])->groupBy('location_id')->all();
			//$location_ids = ArrayHelper::getColumn($location_classments, 'location_id');
			$uncategorized_locations = "SELECT * FROM `lv_locations` WHERE `customer_id`=178 and id not in (select location_id from lv_locations_classments)";
			
			$connection = Yii::$app->getDb();
			
			$command = $connection->createCommand($uncategorized_locations);
		
			$rows = $command->queryAll();
			
			return $this->render('_replocation_form', [
				"customer" => Customer::findOne($customer),
				"uncategorized_locations" => $rows
			]);
		}
    }

    /**
     * 
     * @throws NotFoundHttpException
     */
    public function actionLocationview() {

        if (Yii::$app->request->isAjax) {

            $_retArray = array('success' => FALSE, 'html' => '');
            $_post = Yii::$app->request->get();
            if (!isset($_post['id'])) {
                $_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
                echo json_encode($_retArray);
                exit();
            }
            $id = $_post['id'];
            $find = $this->findLocationModel($id);
            $html = $this->renderAjax('locationview', [
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
     * 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionLocationdelete() {
        if (isset($_GET['customer']))
            $customer = $_GET['customer'];
        else
            throw new NotFoundHttpException('The requested page does not exist.');
        if (isset($_GET['id']))
            $location = $_GET['id'];
        else
            throw new NotFoundHttpException('The requested page does not exist.');
        $this->findLocationModel($location)->delete();
        return $this->redirect(['default/locations/?customer=' . $customer]);
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     * find customer model
     */
    protected function findModel($id) {
        //echo $id;
        //print_r(Customer::findOne($id));exit();
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
    protected function findLocationModel($id, $exHandle = true) {
        //echo $id;
        //print_r(Customer::findOne($id));exit();
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            if (!$exHandle)
                return array();
            else
                throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @return type
     */
    private function getCustomers($countOnly = false) {
        if ($countOnly) {
            $allCustomers = Yii::$app->db->createCommand("SELECT count(id) as totalRecords FROM lv_customers");
            $allCustomers = $allCustomers->queryAll();
            if (isset($allCustomers[0]['totalRecords'])) {
                return $allCustomers[0]['totalRecords'];
            }
            return 0;
        }
        $allCustomers = Yii::$app->db->createCommand("SELECT * FROM `lv_customers`");
        $allCustomers = $allCustomers->queryAll();
        return $allCustomers;
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

        
        $new_path = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_CUSTOMER;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }

        
        $_error = "";
        $target_dir = Yii::getAlias('@webroot') . self::FILE_UPLOAD_PATH_CUSTOMER;
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