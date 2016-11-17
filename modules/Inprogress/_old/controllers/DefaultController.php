<?php

namespace app\modules\Inprogress\controllers;

use Yii;
use app\modules\Orders\models\Order;
use app\modules\Orders\models\OrderSearch;
use yii\web\Controller;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\Item;
use app\models\QOrder;
use app\models\User;
use app\models\Models;
use app\models\Ordertype;
use app\models\Itemlog;
use app\models\Itemstesting;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;

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
				'only' => ['index','details'],
				'rules' => [
					[
						'actions' => ['index','details'],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_BILLING,
							User::TYPE_SALES,
							User::TYPE_CUSTOMER,
							User::REPRESENTATIVE,
                            User::TYPE_SHIPPING
						],
					]
				],
			]
		];
	}	
	
    public function actionIndex($customer = 0)
    {    		
        $orders_count = Order::find()
                             ->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
                             ->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                             ->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
                             ->where(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 1, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)]);
    	
    	if($customer != 0){
            $orders_count->andWhere(['`lv_salesorders`.`customer_id`'=>$customer]);
            $quotes_order_count->andWhere(['`lv_qsalesorders`.`customer_id`'=>$customer]);
    	}
    	
        $orders_count = $orders_count->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`')->count();
    	
		if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype === User::TYPE_CUSTOMER)
			$customer = UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->one()->customerid;

			return $this->render('index', [
				'customer' => $customer,
				'basket_count' => $orders_count
			]);
    	/*$status = array_keys(Item::$inprogressstatus);
		$query = Item::find()
					->andFilterWhere(['status' => $status])
					->groupBy('ordernumber', 'model')
					->orderBy('status DESC, lastupdated DESC, shipped');
        if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype == User::TYPE_CUSTOMER){
            $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
            $my_customers = "(".implode(",", array_map('intval', $customers)).")";
            $query = Item::find()
                    ->andFilterWhere(['status' => $status])
                    ->andWhere('customer IN '. $my_customers )
                    ->groupBy('ordernumber', 'model')
                    ->orderBy('status DESC, lastupdated DESC, shipped');
        }                
				
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => ['pageSize' => 15]
        ]);*/
    }
	
	public function actionLoad()
	{
    	$_post = Yii::$app->request->get();
    	
    	/*if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['type'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}*/
    		
    		$type = ucfirst($_post['type']);
			
    		$customerid = $_post['customerid'];
    		
    		if($type=="All"){
				if(Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE) 
				{
					$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 0, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)]);
					
					if($customerid != 0) 
						$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
						
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
				} else {
					$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
					$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`customer_id`'=>$customers, 'lv_salesorders.deleted' => 0, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)]);
								
					if($customerid != 0) 
						$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
					
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');					
				}
    		}
    		else
    		{
    			$query = Order::find()->select('lv_salesorders.*')
							->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
							->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
							->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
							->where(['lv_salesorders.ordertype' => Ordertype::findOne(['name'=>$type])->id])
							->andWhere(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 0, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)]);
				if($customerid != 0) 
					$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
				
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
    		}
    		//echo $query->createCommand()->sql;
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 30],
    				'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
    				]);
    			
    		echo $this->renderPartial('_order', [
    				'dataProvider' => $dataProvider,
    				]);
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
    	
    		$type = $_post['query'];
    	
    		$searchModel = new OrderSearch();
    		$dataProvider = $searchModel->searchInProgress(['OrderSearch'=>['number_generated'=>trim($type)]]);
    		 
    		$html = $this->renderPartial('_order', [
    				'dataProvider' => $dataProvider,
    				]);
    	
    		$_retArray = array('success' => true, 'html' => $html, 'count'=>$dataProvider->getTotalCount());
    		//echo json_encode($_retArray);
    		//exit();
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/		
	}
	
	/**
	* Get deleted orders of an existing Order model.
	* @param integer $customer
	* @return mixed
	*/
	public function actionGetdeleted($customer){
		$query = Order::find()->select('lv_salesorders.*')
						->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
						->andWhere(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.deleted' => 1, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)])
						->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
						
		$dataProvider = new ActiveDataProvider([
					'query' => $query,
					'pagination' => ['pageSize' => 15],
					'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
					]);
					
		$ordersHtml = $this->renderPartial('_deleted', [
							'dataProvider' => $dataProvider,
					]);	
		
			$_retArray = array('success' => true, 'orders_html' => $ordersHtml, 'orders_count' => (int)$query->count());
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
							->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
							->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
							->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`deleted`' => 1, '`lv_items`.`status`'=>array_keys(Item::$inprogressstatus)])
							->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`')
							->count();
							
		$_total_count = (int) $basket_count;
		//			
		echo $_total_count;
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
    
    public function actionDetails($id)
    {
    	$model = $this->findModel($id);
    	
    	$status = array_keys(Item::$inprogressstatus);
		
		$query = Item::find()->where(['ordernumber' => $model->id, 'status'=>$status])->orderBy('status DESC, lastupdated DESC, shipped');
    	
    	$dataProvider = new ActiveDataProvider([
				    			'query' => $query
				    		]);    	
    	
    	return $this->render('details', [
    			'dataProvider' => $dataProvider,
    			'model' => $model,
    			'query_count' => $query->count(),
    			]);    	
    }
	
    public function actionSerialsearch(){
        $_post = Yii::$app->request->get();
		
        $searchModel = new \app\models\SerialSearch();
		
		$dataProvider = $searchModel->searchInProgress(['SerialSearch'=>['serial'=>$_post['query'], 'ordernumber'=>$_post['idorder']]]);   
		
        $html = $this->renderPartial('_detailsview', [
					'dataProvider' => $dataProvider,
				]);	
				
        $_retArray = array('success' => true, 'html' => $html, 'count' => $dataProvider->getTotalCount());
    			
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //return view
        return $_retArray;
        exit();         
    }
    
    public function actionTurntocleaning()
    {
    	$data = Yii::$app->request->get();
    	
    	$_retArray = array('success' => FALSE, 'html' => '');
    	
    	if (Yii::$app->request->isAjax) {
    		    	
	    	if (!isset($data['itemid'])) {
	    		$_retArray = array('error' => true, 'html' => 'Something is wrong! Plese try again!');
	    		echo json_encode($_retArray);
	    		exit();
	    	}
	    	
	    	//$order = Order::findOne($data['orderid']);
	    	
	    	//$model = Models::findOne($data['modelid']);
	    	
	    	//$serial = $data['serial'];	     
	    	
	    	//$_finditem = Item::find()->where(['ordernumber'=>$order->id, 'model'=>$model->id, 'serial'=>$serial, 'status'=>array_search('In Progress', Item::$status)]);    	
	    	
	    	$_finditem = Item::findOne($data['itemid']);
	    	
	    	if($_finditem !== null)
	    	{
	    		$_finditem->status = array_search('Cleaned', Item::$status);
	    		if($_finditem->save())
	    		{
	    			$html = "Item is successfully cleaned!";
	    			//track item
	    			$itemlog = new Itemlog;
	    			$itemlog->userid = Yii::$app->user->id;
	    			$itemlog->status = array_search('Cleaned', Item::$status);
	    			$itemlog->itemid = $_finditem->id;
					$itemlog->locationid = $_finditem->location;
	    			$itemlog->save();	    			
	    		}
	    	} else {
    		 
    			$_retArray = array('error' => true, 'html' => 'Something is wrong! Plese try again!');
    		}
	    	
	    	$_retArray = array('success' => true, 'html' => $html);
	    		
	    	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    	//return view
	    	return $_retArray;
	    	exit();
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
    
    public function actionEndcleaning($id)
    {
    	$order = $this->findModel($id);
    	//get all inprogress items which need cleaning
    	$inprogressitems = Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
    								   ->where(['ordernumber'=>$order->id, 'status' => array_search('In Progress', Item::$status)])
    								   ->all();
    	$success = false;
    	//
    	foreach ($inprogressitems as $inprogressitem)
    	{
    		$cleaninghasoption = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
								    		->where(['orderid'=>$order->id, 'itemid'=>$inprogressitem->model])
								    		->andWhere(['ordernumber'=>$order->id, 'model'=>$inprogressitem->model])
								    		->andWhere('optionid IN (2,3)')
								    		->all();
    		$_model = Models::findOne($inprogressitem->model);
    		
    		if(($cleaninghasoption || $_model->preowneditems) && $inprogressitem->status!=array_search('In Progress', Item::$status))
    		{
    			$inprogressitem->status = array_search('Cleaned', Item::$status);
    			if($inprogressitem->save())
    			{
    				$html = "Item is successfully cleaned!";
    				$success = true;
    				//track item
    				$itemlog = new Itemlog;
    				$itemlog->userid = Yii::$app->user->id;
    				$itemlog->status = array_search('Cleaned', Item::$status);
    				$itemlog->itemid = $inprogressitem->id;
					$itemlog->locationid = $inprogressitem->location;
    				$itemlog->save();
    			}    	
    			else 
    				$errors .= $inprogressitem->errors;
    		}
    	}
    	
    	if($success === true){
    		$_message = '<div class="alert alert-success"><strong>Success!</strong> Cleaning is completed now!</div>';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
    		$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
    		Yii::$app->getSession()->setFlash('error', $_message);
    	}
    	
    	return $this->redirect(Yii::$app->request->referrer);    	
    }
    
    public function actionTurnonship($id)
    {
    	$order = $this->findModel($id);
    	$status = array_keys(Item::$inprogressstatus);
    	//get tested items
    	$testeditems = Item::find()->where(['ordernumber'=>$order->id, 'status' => $status])->all();  
    	$success = false;
    	foreach($testeditems as $testeditem)
    	{
    		$testeditem->status = array_search('Ready to ship', Item::$status);
    		if($testeditem->save())
    		{
    			$success = true;
    			//track item
    			$itemlog = new Itemlog;
    			$itemlog->userid = Yii::$app->user->id;
    			$itemlog->status = array_search('Ready to ship', Item::$status);
    			$itemlog->itemid = $testeditem->id;
				$itemlog->locationid = $testeditem->location;
    			$itemlog->save();
    		}
    		else 
    			$errors .= $testeditem->errors;
    	}
    	if($success === true){
    		$_message = '<div class="alert alert-success"><strong>Success!</strong> Items are ready to ship now!</div>';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
    		if(!empty($errors)) {
	    		$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
	    		Yii::$app->getSession()->setFlash('error', $_message);
    		}
    	}
    	return $this->redirect(Yii::$app->request->referrer);  	
    }
    
    public function actionTurnmodelonship($id)
    {
    	$item = Item::findOne($id);
    	$success = false;
    	$item->status = array_search('Ready to ship', Item::$status);
    	if($item->save())
    	{
    		$success = true;
    		//track item
    		$itemlog = new Itemlog;
    		$itemlog->userid = Yii::$app->user->id;
    		$itemlog->status = array_search('Ready to ship', Item::$status);
    		$itemlog->itemid = $item->id;
			$itemlog->locationid = $item->location;
    		$itemlog->save();
    	} else {
    		$errors = $item->errors;
    	}    	
    	
    	if($success === true){
    		$_message = '<div class="alert alert-success"><strong>Success!</strong> Item {<b>' . $item->serial . '</b>} is ready to ship now!</div>';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
    		$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
    		Yii::$app->getSession()->setFlash('error', $_message);
    	}
    	return $this->redirect(Yii::$app->request->referrer);
    }
    
    public function actionTurntotesting()
    {
    	$id = $_POST['itemId'];
    	
    	$_finditem = Item::findOne($id);
    	
    	$success = false;
    	
    	if($_finditem !== null)
    	{
    		$_finditem->status = array_search('Requested for Service', Item::$status);
    		if($_finditem->save())
    		{
    			$success = true;
    			//
    			$itemstesting = new Itemstesting;
    			$itemstesting->itemid = $_finditem->id;
    			$itemstesting->problem = $_POST['problem'];
    			$itemstesting->resolution = $_POST['resolution'];
    			$itemstesting->partid = $_POST['modelid'];
    			if(!$itemstesting->save()) {
    				$errors = $itemstesting->errors;
    				$success = false;
    			}
    			//track item
    			$itemlog = new Itemlog;
    			$itemlog->userid = Yii::$app->user->id;
    			$itemlog->status = array_search('Requested for Service', Item::$status);
    			$itemlog->itemid = $_finditem->id;
				$itemlog->locationid = $_finditem->location;
    			$itemlog->save();
    		}
    	}

    	if($success === true){
    		$_message = '<div class="alert alert-success"><strong>Success!</strong> Success!</div>';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
    		$_message = '<div class="alert alert-danger"><strong>Failed!</strong>' . json_encode($errors) . '</div>';
    		Yii::$app->getSession()->setFlash('error', $_message);
    	}
    	
    	return $this->redirect(Yii::$app->request->referrer);
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
}
