<?php

namespace app\modules\Location\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\LocationClassment;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\Location;
use app\models\LocationDetail;
use app\models\LocationParent;
use app\models\LocationSearch;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
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
					'index'
				],
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
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
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	    	
    	$_divisions = LocationClassment::find()->select('parent_id')->where(['customer_id'=>$customers])->andWhere(['not', ['parent_id'=>null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();
    	        
    	$_count_deleted = Location::find()->where(['customer_id'=>$customers, 'deleted'=>1])->count();
    	    	
    	return $this->render('index', ['_divisions'=>$_divisions, '_count_deleted' =>$_count_deleted]);
    }
    
    public function actionLoad()
    {
    	//if (Yii::$app->request->isAjax) {
    		$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    		
    		$query = Location::find()->where(['customer_id'=>$customers, 'deleted'=>0]);
    		
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 10],
    				]);
    		 
    		$html = $this->renderPartial('_load', [
    				'dataProvider' => $dataProvider,
    				]);
    		$_retArray = array('success' => true, 'html' => $html);
    			
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
    
    public function actionDload()
    {
    	//if (Yii::$app->request->isAjax) {
    	$data = Yii::$app->request->get();
    	
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	
    	$locations = \yii\helpers\ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id'=>$data['id']])->asArray()->all(), 'location_id');
    
    	$query = Location::find()->where(['customer_id'=>$customers, 'id'=>$locations]);
    
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination' => ['pageSize' => 10],
    			]);
    	 
    	$html = $this->renderPartial('_load', [
    			'dataProvider' => $dataProvider,
    			]);
    	$_retArray = array('success' => true, 'html' => $html);
    	 
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();
    	/*} else {
    	  
    	throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
    
    public function actionLoaddeleted()
    {
    	//if (Yii::$app->request->isAjax) {
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    
    	$query = Location::find()->where(['customer_id'=>$customers, 'deleted'=>1]);
    
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination' => ['pageSize' => 10],
    			]);
    	 
    	$html = $this->renderPartial('_load', [
    				'dataProvider' => $dataProvider,
    				'deleted' => true
    			]);
    	$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    	 
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();
    	/*} else {
    	  
    	throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
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
    	 
    	$searchModel = new LocationSearch();
    	
    	$dataProvider = $searchModel->search(['LocationSearch'=>['address'=>$query]]);
    	 
    	$html = $this->renderPartial('_load', [
    			'dataProvider' => $dataProvider,
    			]);
    	 
    	$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());

    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return view
    	return $_retArray;
    	exit();
    	/*} else {
    	  
    	throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
    
    public function actionCreate()
    {
    	$data = Yii::$app->request->post();
    	
    	$model = new Location;
    	
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	
    	$parent_locations = LocationParent::find()->where(['parent_parent_id'=>null])->orderBy('parent_name')->all();
    	
    	if (!empty($data)) {
    		foreach ($customers as $customer)
    		{
	    		$modelLocation = new Location();
	    		$modelLocation->customer_id = $customer;
	    		$modelLocation->address = $data['address'];
	    		$modelLocation->storenum = $data['storenum'];
	    		$modelLocation->storename = $data['storename'];
	    		$modelLocation->address2 = $data['address2'];
	    		$modelLocation->country = $data['country'];
	    		$modelLocation->city = $data['city'];
	    		$modelLocation->state = $data['state'];
	    		$modelLocation->zipcode = $data['zipcode'];
	    		if ($modelLocation->save()) {
	    			$_locationid = $modelLocation->id;
	    			$_message = 'Location has been successfully created!';
	    			Yii::$app->getSession()->setFlash('success', $_message);	    			
	    		} else {
	    			$errors .= json_encode($modelLocation->errors);
	    		}    		
	    		//
	    		$modelClassmentLocation = new LocationClassment;
	    		
	    		$parent_id = $data['parent_location'];
	    		
	    		if (strpos($parent_id, 'm_') !== false) {
	    			$modelClassmentLocation->parent_id = str_replace('m_', '', $parent_id);
	    		} elseif (strpos($parent_id, 'p_') !== false) {
	    			$locationid = str_replace('p_', '', $parent_id);
	    			
	    			$location = Location::findOne($locationid);
	    			
	    			$locationname = "";
	    			
	    			if(!empty($location->storenum))
	    				$locationname .= "Store#: " . $location->storenum . " - ";
	    			if(!empty($location->storename))
	    				$locationname .= $location->storename  . ' - ';
	    			//
	    			$locationname .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;	    			
	    			
	    			$parentLocation = new LocationParent;
	    			$parentLocation->parent_name = $locationname;
	    			$parentLocation->save();
	    			
	    			$modelClassmentLocation->parent_id = $parentLocation->id;
	    		}
	    		$modelClassmentLocation->location_id = $_locationid;
	    		$modelClassmentLocation->save();
    		}
    		
            return $this->redirect(['create']);
        } else {
            return $this->render('create', [
                'model' => $model,
            	'customers'	=> $customers,
            	'parent_locations' => $parent_locations
            ]);
        }
    }
    
    public function actionManage()
    {
    	$data = Yii::$app->request->post();
    	
    	if (!empty($data)) {   	
	    	$parent_location = $data['parent_location'];
	    	
	    	$success = false;
	    	
	    	if (strpos($parent_location, 'm_') !== false) {
	    		$parent_id = str_replace('m_', '', $parent_location);
	    	} elseif (strpos($parent_location, 'p_') !== false) {
	    		$locationid = str_replace('p_', '', $parent_location);
	    	
	    		$location = Location::findOne($locationid);
	    	
	    		$locationname = "";
	    	
	    		if(!empty($location->storenum))
	    			$locationname .= "Store#: " . $location->storenum . " - ";
	    		if(!empty($location->storename))
	    			$locationname .= $location->storename  . ' - ';
	    		//
	    		$locationname .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
	    	
	    		$parentLocation = new LocationParent;
	    		$parentLocation->parent_name = $locationname;
	    		$parentLocation->save();
	    	
	    		$parent_id = $parentLocation->id;
	    	}    	
	    	
	    	$locations = $_POST['locations'];
	    	
	    	foreach ($locations as $location)
	    	{
	    		$model = new LocationClassment;
	    		$model->parent_id = $parent_id;
	    		$model->location_id = $location;
	    		if($model->save())
	    			$success = true;
	    		else 
	    			$errors .= json_encode($model->errors);		
	    	}
	    	
	    	if($success)
	    	{
	    		$_message = 'Locations has been successfully added!';
	    		Yii::$app->getSession()->setFlash('success', $_message);
	    	}
	    	else {
	    		$_message = $errors;
	    		Yii::$app->getSession()->setFlash('danger', $_message);
	    	}
	    	
	    	return $this->redirect(['create']);
    	} else
            throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionCreatefolder()
    {
    	$data = Yii::$app->request->post();
    	
    	if (!empty($data)) {
    		$parent_folder = $data['e_parent_folder'];
    		
    		$folder_code = $data['e_folder_code'];
    		
    		$folder_name = $data['e_folder_name'];
    		
    		$model = new LocationParent;
    		
    		$model->parent_parent_id = $parent_folder;
    		
    		$model->parent_code = $folder_code;
    		
    		$model->parent_name = $folder_name;
    		
    		if($model->save())
    		{
    			$_message = 'Division {<strong>'. $model->parent_name .'</strong>} has been successfully created!';
    			Yii::$app->getSession()->setFlash('success', $_message);    			
    		}
    		else {
    			$_message = json_encode($model->errors);
    			Yii::$app->getSession()->setFlash('danger', $_message);
    		}
    	}
    	
    	return $this->redirect(['create']);
    }
    
    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$data = Yii::$app->request->post();

    	$model = $this->findModel($id);
    	
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	 
    	$parent_locations = LocationParent::find()->where(['parent_parent_id'=>null])->orderBy('parent_name')->all();   	 
    
    	  if (!empty($data)) {
    		$model->address = $data['address'];
    		$model->storenum = $data['storenum'];
    		$model->storename = $data['storename'];
    		$model->address2 = $data['address2'];
    		$model->country = $data['country'];
    		$model->city = $data['city'];
    		$model->state = $data['state'];
    		$model->zipcode = $data['zipcode'];
    		if ($model->save()) {
    			$_locationid = $model->id;
    			$_message = 'Location has been successfully updated!';
    			Yii::$app->getSession()->setFlash('success', $_message);	    			
    		} else {
    			$errors .= json_encode($model->errors);
    		}    		
    		//
    		$modelClassmentLocation = new LocationClassment;
    		
    		$parent_id = $data['parent_location'];
    		
    		if(!empty($parent_id))
    		{
    			$_old_parents = LocationParent::find()->where(['location_id'=>$model->id])->all();
    			foreach ($_old_parents as $_old_parent)
    			{
    				$_old_parent->delete();
    			}
    			//
	    		if (strpos($parent_id, 'm_') !== false) {
	    			$modelClassmentLocation->parent_id = str_replace('m_', '', $parent_id);
	    		} elseif (strpos($parent_id, 'p_') !== false) {
	    			$locationid = str_replace('p_', '', $parent_id);
	    				    			
	    			$locationname = "";
	    			
	    			if(!empty($model->storenum))
	    				$locationname .= "Store#: " . $model->storenum . " - ";
	    			if(!empty($model->storename))
	    				$locationname .= $model->storename  . ' - ';
	    			//
	    			$locationname .= $model->address . " " . $model->address2 . " " . $model->city . " " . $model->state . " " . $model->zipcode;	    			
	    			//
	    			$parentLocation = new LocationParent;
	    			$parentLocation->parent_name = $locationname;
	    			$parentLocation->save();
	    			
	    			$modelClassmentLocation->parent_id = $parentLocation->id;
	    		}
	    		$modelClassmentLocation->location_id = $_locationid;
	    		$modelClassmentLocation->save();
    		}    		
            return $this->redirect(['update', 'id'=>$model->id]);
        } else {
    		return $this->render('update', [
		                'model' => $model,
		            	'customers'	=> $customers,
		            	'parent_locations' => $parent_locations
    				]);
    	}
    }
    
    
    public function actionEditdetails()
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
    		$model = Location::findOne($id);
    		
    		$name = ($model->storenum) ? 'Store#: ' . $model->storenum : '';
    		
    		if(empty($name))
    			$name = $model->storename;
    		
    		$locationdetail = LocationDetail::find()->where(['locationid'=>$model->id])->one();
    			    			
    		$html = $this->renderPartial('_editlocationsettingsform', [
    				'location'=>$model,
    				'locationdetail'=>$locationdetail
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'locationName'=>$name);
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			//return list
			return $_retArray;		
    	//}    	
    }
    
    public function actionLoadlocationsettings()
    {
    	$_post = Yii::$app->request->get();
    	
    	$locationid = $_post['id'];
    	 
    	$location = Location::findOne($locationid);   

    	$locationdetail = LocationDetail::find()->where(['locationid'=>$locationid])->one();
    	
    	$html = $this->renderPartial('_loadlocationsettings', [
    			'_location'=>$location,
    			'_location_details'=>$locationdetail
    			]);
    	$_retArray = array('success' => true, 'html' => $html);
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return list
    	return $_retArray;    	
    }
    
    public function actionSavelocationdetails()
    {
    	$_post = Yii::$app->request->post();
    	
    	$locationid = $_post['locationId'];
    	
    	$location = Location::findOne($locationid);
    	
    	if($location != null)
    	{
	    	$location->connection_type = $_post['connection_type'];
	    	$location->save();
	    	
	    	$locationdetail = LocationDetail::find()->where(['locationid'=>$locationid])->one();
	    	if(empty($locationdetail))
	    		$locationdetail = new LocationDetail;
	    	$locationdetail->locationid = $location->id;
	    	$locationdetail->ipaddress = $_post['ip_address'];
	    	$locationdetail->gateway = $_post['gateway'];
	    	$locationdetail->primary_dns = $_post['primary_dns'];
	    	$locationdetail->secondary_dns = $_post['secondary_dns'];
	    	$locationdetail->wins_server = $_post['wins_server'];
	    	$locationdetail->save();
    	}
    	$_retArray = array('success' => true, 'id'=>$locationid);
    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	//return list
    	return $_retArray;    	
    }
    
    /**
     * Revert deletion of an existing Order model.
     * @param integer $id
     * @return boolean
     */
    public function actionRevert($id){
    	$model = $this->findModel($id);
    	$model->deleted = 0;
    	$model->save();
    	//
    	$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    	 
    	$_count_deleted = Location::find()->where(['customer_id'=>$customers, 'deleted'=>1])->count();
    	//
    	return $_count_deleted;
    }
    
    public function actionDelete($id)
    {
    	$model = $this->findModel($id);
    	$model->deleted = 1;
    	if($model->save()) {
    		$_message = 'Location has been deleted successfully!';
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
    	if (($model = Location::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }    
       
}
