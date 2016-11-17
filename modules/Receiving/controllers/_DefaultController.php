<?php

namespace app\modules\Receiving\controllers;

use Yii;
use app\modules\Orders\models\Order;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Customer;
use app\models\Receive;
use app\models\Purchase;
use app\models\Models;
use app\models\ModelAssembly;
use app\models\Item;
use app\models\Itemlog;
use app\models\Itemspurchased;
use yii\helpers\ArrayHelper;
use app\vendor\PHelper;
use yii\web\Controller;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

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
				'only' => ['index','create', 'update', 'view', 'delete'],
				'rules' => [
					[
						'actions' => ['index','create', 'update', 'view', 'delete'],
						'allow' => true,
						// Allow few users
						'roles' => [
							User::TYPE_ADMIN,
							User::TYPE_CUSTOMER_ADMIN,
							User::TYPE_BILLING,
							User::TYPE_SALES,
							User::TYPE_CUSTOMER
						],
					]
				],
			]
		];
	}	
	
    public function actionIndex()
    {
		$query = Item::find()
							->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')
							->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])
							->groupBy('purchaseordernumber', 'model');
				
        $dataProvider = new ActiveDataProvider([ 
            'query' => $query,
			'pagination' => ['pageSize' => 15], 
        ]);
        
        /*$query = Item::find()->select('lv_items.*')
        				->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
        				->andWhere(['not', ['ordertype' => 1]])
						->andWhere(['status'=>array_search('In Transit', Item::$status)])
						->groupBy('ordernumber', 'model');
        
        $dataProvider_1 = new ActiveDataProvider([
        		'query' => $query,
        		'pagination' => ['pageSize' => 15],
        		'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
        		]);  */      
        
        $query = Purchase::find()->innerJoin('lv_items', '`lv_items`.`purchaseordernumber` = `lv_purchases`.`id`')
								->where(['`lv_items`.`status`'=>array_search('In Transit', Item::$status)]);
         
        $dataProvider_2 = new ActiveDataProvider([
        		'query' => $query,
        		'pagination' => ['pageSize' => 50],
        		'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
        		]);        

		$query = Item::find()
							->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
							->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])
							->andWhere(['purchaseordernumber' => null])
							->groupBy('ordernumber', '`lv_items`.`model`')
							->orderBy('`lv_salesorders`.`id` desc');
				
        $dataProvider_3 = new ActiveDataProvider([ 
            'query' => $query,
			'pagination' => ['pageSize' => 15], 
        ]);
        
        $received_so_count = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
        $received_po_count = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])->count();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
			'dataProvider2' => $dataProvider_3,
        	//'__incominginventoryProvider' => $dataProvider_1,
        	'__purchasedataProvider' => $dataProvider_2,
                'received_so_count' => $received_so_count,
                'received_po_count' => $received_po_count
        ]);
    }
	
	public function actionLoadcustomerinventory()
	{
		if (Yii::$app->request->isAjax) {
			$query = Item::find()->select('lv_items.*')
							->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')
							->andWhere(['not', ['ordertype' => 1]])
							->andWhere(['status'=>array_search('In Transit', Item::$status)])
							->groupBy('ordernumber', 'model');
			
			$dataProvider = new ActiveDataProvider([
					'query' => $query,
					'pagination' => ['pageSize' => 15],
					'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]] 
					]);     
					
			$html = $this->renderPartial('_incomingcustomerinventory', [
				'dataProvider' => $dataProvider
			]);	

			$_retArray = array('success' => true, 'html' => $html);
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();				
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
	}
	
	public function actionLoadincomingpurchase()
	{
		if (Yii::$app->request->isAjax) {
			$query = Purchase::find()->innerJoin('lv_items', '`lv_items`.`purchaseordernumber` = `lv_purchases`.`id`')
						->where(['`lv_items`.`status`'=>array_search('In Transit', Item::$status)]);
			 
			$dataProvider = new ActiveDataProvider([
					'query' => $query,
					'pagination' => ['pageSize' => 50],
					'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
					]);  

			$html = $this->renderPartial('_incoming_purchase', [
				'dataProvider' => $dataProvider
			]);	

			$_retArray = array('success' => true, 'html' => $html);
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		//return view
    		return $_retArray;
    		exit();							
		} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}			
	}
    
    public function actionSavereceiveqtyserialized()
    {
		$_post = Yii::$app->request->post();
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['serial']) && !isset($_post['order']) && !isset($_post['currentmodel']) && !isset($_post['type'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$serial = $_post['serial'];
    		$type = $_post['type'];
    		
	    	if($type==1)
	    		$order = $this->findOModel($_post['order']);
	    	else if($type==2)
	    		$order = $this->findModel($_post['order']);
	    	
	    	$model = $_post['currentmodel'];
	    	
	    	if($type==1)
	    		$row = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$order->id, 'model'=>$model])->one();
	    	else if($type==2)
	    		$row = Item::find()->where(['status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$order->id, 'model'=>$model])->one();
	    	
	    	if($row !== null) {
	    		$row->status = array_search('In Stock', Item::$status);
	    		$row->serial = $serial;
	    		if($row->save()){
	    			$success = true;
	    			//track item
	    			$itemlog = new Itemlog;
	    			$itemlog->userid = Yii::$app->user->id;
	    			$itemlog->status = array_search('In Stock', Item::$status);
	    			$itemlog->itemid = $row->id;
					$itemlog->locationid = $row->location;
	    			$itemlog->save();
	    			$_retArray = array('success' => true, 'html' => 'Serialized item is put back into stock!');
	    			if($type==1 && Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$order->id, 'model'=>$model])->count()==0)
	    				$_retArray['done'] = true;
	    			else if($type==2 && Item::find()->where(['status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$order->id, 'model'=>$model])->count()==0)
	    				$_retArray['done'] = true;
	    			echo json_encode($_retArray);
	    			exit();	    			
	    		}
	    		else
	    			$errors = $row->errors;
	    	}
    	}
    }
    
    public function actionSavereceiveqty($id, $type)
    {
		if(type==1)
			$order = $this->findOModel($id);
		else if($type==2)
    		$order = $this->findModel($id);
    	 
    	$errors;
    	
    	$success = false;
    	
    	//var_dump($_POST);
    	//exit(1);
    
    	if (isset($_POST['instockqty'])){
    		$items = $_POST['items'];
    		$qtys = $_POST['instockqty'];
    		//
    		foreach ($items as $key=>$item)
    		{
    			$qty = $qtys[$key];
				//var_dump($qty);exit(1);
				if($type==1)
    				$rows = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'ordernumber'=>$order->id, 'model'=>$item])->andWhere(['purchaseordernumber' => null])->all();
				else if($type==2)
					$rows = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$order->id, 'model'=>$item])->all();
    			$i = 0;
    			//var_dump($rows->count());
    			//exit(1);
    			foreach($rows as $row) {
    				if($i<$qty){
    					$row->status = array_search('In Stock', Item::$status);
						if($row->save()){
							$success = true;
							//track item
							$itemlog = new Itemlog;
							$itemlog->userid = Yii::$app->user->id;
							$itemlog->status = array_search('In Stock', Item::$status);
							$itemlog->itemid = $row->id;
							$itemlog->locationid = $row->location;
							$itemlog->save();						
						}
						else
							$errors = $row->errors;
					}
					$i++;
    			}
    			//set others as in transit
				if($type==1)
    				$rows = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$order->id, 'model'=>$item])->all();
				else if($type==2)
					$rows = Item::find()->where(['status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$order->id, 'model'=>$item])->all();
    			if($rows !== null){
	    			foreach($rows as $row) {
	    				$row->status = array_search('In Transit', Item::$status);
	    				if($row->save()) {
							//track item
							$itemlog = new Itemlog;
							$itemlog->userid = Yii::$app->user->id;
							$itemlog->status = array_search('In Transit', Item::$status);
							$itemlog->itemid = $row->id;
							$itemlog->locationid = $row->location;
							$itemlog->save();							
						}
	    			}
    			}
    		}
    	}
    	//
    	if($success === true){
    		if($type==1)
    			$_message = 'Sales Order has been updated successfully!';
    		else if($type==2)
    			$_message = 'Purchase Order has been updated successfully!';
    		Yii::$app->getSession()->setFlash('success', $_message);
    	} else{
    		$_message = json_encode($errors);
    		Yii::$app->getSession()->setFlash('danger', $_message);
    	}
    
    	return $this->redirect(Yii::$app->request->referrer);
    }
    
    public function actionCreate()
    {
    	$data = Yii::$app->request->post();
    	
    /*	foreach (\app\models\Customer::find()->all() as $customer)
    	{
    		PHelper::generateNonSOCustomerJson($customer->id);
    	}
    */	
    	if(!empty($data)){
    		
    		//var_dump($_POST);exit(1);
			$customerId = $data['customerId'];
			$customer = Customer::findOne($customerId);
			if(!empty($data['quantity']) && is_array($data['quantity'])) {
				foreach($data['quantity'] as $key=>$value)
				{
					$_modelid = $data['modelid'][$key];
					$_find_model_1 = Models::findOne($_modelid);
					$_conditionid = (!empty($data['itemoption'][$key])) ? $data['itemoption'][$key] : 0;
					if((!empty($_find_model_1) && !$_find_model_1->assembly))
					{
						for($i=0; $i<$value;$i++){
							$model = Item::find()->where(['customer'=>$data['customerId'], 'model'=>$data['modelid'][$key], 'status'=>array_search('Requested', Item::$status)])->one();
							if(empty($model))
								$model = new Item;
							if(isset($data['receivingserialnumber'][$_modelid]))
								$model->serial = $data['receivingserialnumber'][$_modelid][0];
							$model->conditionid = $_conditionid;
							$model->owner_id = Yii::$app->user->id;
							$model->model = $data['modelid'][$key];
							$model->customer = $data['customerId'];
							$model->location = $data['location'];
							$model->status = array_search('In Stock', Item::$status);
							$model->notes = $data['itemnotes'][$key];
							if($customer->requirepalletcount)
								$model->incomingpalletnumber = $data['palletnumber'][$key];
							if($customer->requireboxcount)
								$model->incomingboxnumber = $data['boxnumber'][$key];
							if($model->save()) {
								$success = true;
								//track item
								$itemlog = new Itemlog;
								$itemlog->userid = Yii::$app->user->id;
								$itemlog->status = array_search('In Stock', Item::$status);
								$itemlog->itemid = $model->id;
								$itemlog->locationid = $model->location;
								if($customer->requirestorenumber)
									$itemlog->incomingstorenumber = $data['storenumber'];
								$itemlog->save();
							}
							else
								$errors .= $model->errors;    					
						}
                                                $recentActivity = New \app\models\Recentactivity;
                                                $recentActivity->pk = $data['customerId'];
                                                $recentActivity->customer_id = $data['customerId'];
                                                $recentActivity->user_id = Yii::$app->user->id;
                                                $recentActivity->created_at = date('Y-m-d H:i:s');
                                                $recentActivity->type = array_search('Items Received', \app\models\Recentactivity::$type);
                                                $recentActivity->is_new = 1;
                                                $recentActivity->save();                                                
					}    	
					else 
					{
						$assembly_items = ModelAssembly::find()->where(['modelid'=>$_modelid])->all();
						foreach ($assembly_items as $assembly_item)
						{
							$model_id = $assembly_item->partid;
							$model_qty = $assembly_item->quantity;
							$_assemblyQty = $model_qty * $value;
							//
							for($i=0; $i<$_assemblyQty;$i++){
								$model = Item::find()->where(['customer'=>$data['customerId'], 'model'=>$model_id, 'status'=>array_search('Requested', Item::$status)])->one();
								if(empty($model))
									$model = new Item;
								if(isset($data['receivingserialnumber'][$_modelid]))
									$model->serial = $data['receivingserialnumber'][$_modelid][0];
								$model->conditionid = $_conditionid;
								$model->owner_id = Yii::$app->user->id;
								$model->model = $model_id;
								$model->customer = $data['customerId'];
								$model->location = $data['location'];
								$model->status = array_search('In Stock', Item::$status);
								$model->notes = $data['itemnotes'][$key];
								if($model->save()){
									$success = true;
									//track item
									$itemlog = new Itemlog;
									$itemlog->userid = Yii::$app->user->id;
									$itemlog->status = array_search('In Stock', Item::$status);
									$itemlog->itemid = $model->id;
									$itemlog->locationid = $model->location;
									$itemlog->save();
								}
								else
									$errors .= $model->errors; 
							}    					
						}
					}		
				}
			} else 
				$success = true;
    	
	    	/*$quantity = (int) $data['receivedquantity'];
	    	
	    	$success = false;
	    	
	    	$errors;
	    	
	    	if($quantity !== 0)
	    	{
	    		for($i=0; $i<$quantity;$i++){
	    			$model = new Item;
	    			if(isset($data['receivingserialnumber']))
	    				$model->serial = $data['receivingserialnumber'];
	    			$model->owner_id = Yii::$app->user->id;
	    			$model->model = $data['modelid'];
	    			$model->customer = $data['customerId'];
	    			$model->location = $data['location'];
	    			$model->status = array_search('In Stock', Item::$status);
	    			if($model->save()) {
	    				$success = true;
						//track item
						$itemlog = new Itemlog;
						$itemlog->userid = Yii::$app->user->id;
						$itemlog->status = array_search('In Stock', Item::$status);
						$itemlog->itemid = $model->id;
						$itemlog->save();					
					}
	    			else 
	    				$errors .= $model->errors;
	    		}
	    	}
	    	else 
	    		$errors .= "Quantity field contain incorrect values : {should be number and more than zero}";*/
	
	    	if($success === true){
	    		$_message = 'Inventory have been received successfully!';
	    		Yii::$app->getSession()->setFlash('success', $_message);
	    	} else if(!$success && !empty($data['quantity'])){
	    		$_message = json_encode($errors);
	    		Yii::$app->getSession()->setFlash('danger', $_message);
	    	}
	
	    	return $this->redirect(Yii::$app->request->referrer);
    	}else {
    		return $this->render('create', [
    					'model' => $model,
    				]);    		
    	}
    }
    
    public function actionReceive()
    {
    	$_post = Yii::$app->request->get();
    	
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['ordernumber'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$ordernumber = $_post['ordernumber'];
			$type = $_post['type'];
			$customer = null;
			if($type==1) {
    			$model = Order::findOne($ordernumber);
    			$items = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'ordernumber'=>$model->id])->andWhere(['purchaseordernumber' => null])->all();				
				$customer = Customer::findOne(Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'ordernumber'=>$model->id])->andWhere(['purchaseordernumber' => null])->one()->customer);
			} else if($type==2){
    			$model = Purchase::findOne($ordernumber);
    			$items = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->id])->all();
    			$customer = Customer::findOne(Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->id])->one()->customer);
			} else {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
			}
    		$items = ArrayHelper::getColumn($items, 'model');
    		$items = array_unique($items);
			//
    		$html = $this->renderAjax('_modals/___receiveqtymodal', [
    				'items' => $items,
    				'model' => $model,
					'type' => $type,
    				'customer' => $customer
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'title'=>"Save quantity to Receive (". $model->number_generated .")");
    		echo json_encode($_retArray);
    		exit();
    	}
    }
    
    public function actionOserialform()
    {
    	$_post = Yii::$app->request->get();
    	
    	if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['model']) && !isset($_post['customer']) && !isset($_post['quantity'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		
    		$customer = Customer::findOne($_post['customer']);
    		
    		$model = $_post['model'];
			
    		$row = $_post['row'];
    		
    		$quantity = $_post['quantity'];
    		
    		$session = Yii::$app->session;
    		
    		$session['totalquantity'] = $quantity;
    		
    		$session['currentquantity'] = $_post['currentquantity'];
    	
    		$html = $this->renderAjax('_oserialform', [
    				'item'=>$model,
    				'customer'=>$customer,
    				'quantity'=>$quantity,
					'row'=>$row
    				]);
    		$_retArray = array('success' => true, 'html' => $html, 'itemserialized'=>$session['currentquantity'], 'itemserializedornot'=>$quantity);
    		echo json_encode($_retArray);
    		exit();  
    	}  	
    }
    
    public function actionSaveserial()
    {
    	$_retArray = array('success' => FALSE, 'html' => '');
    	$_post = Yii::$app->request->post();
    	//$_post = array('serial'=>$serial, 'currentmodel'=>4, 'quantity'=>4, 'customerId'=>1);
    	//if (Yii::$app->request->isAjax) {
    		$_retArray = array('success' => FALSE, 'html' => '');
    		if (!isset($_post['serial']) || !isset($_post['currentmodel']) || !isset($_post['quantity']) || !isset($_post['customerId'])) {
    			$_retArray = array('success' => FALSE, 'html' => 'Something is wrong! Plese try again!');
    			echo json_encode($_retArray);
    			exit();
    		}
    		$serial = $_post['serial'];
    		$model = $_post['currentmodel'];
    		$quantity = (int) $_post['quantity'];
    		$customer = $_post['customerId'];
    		$_model = Models::findOne($model);
    		$session = Yii::$app->session;
    		$current_quantity = (int) $session['currentquantity'];
    		/**
    		 * get character to be removed from beginning
    		 * verify the serial contain these characters at beginning
    		 * get length of characters to be removed
    		 * remove these characters using substr
    		*/
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
    		
    		$current_item = new Item;
    		$current_item->owner_id = Yii::$app->user->id;
    		$current_item->customer = $customer;
    		$current_item->model = $model;
    		$current_item->status = array_search('In Stock', Item::$status);
    		$current_item->serial = $serial;
    		if($current_item->save()){
    			$current_quantity +=1;
    			$session['currentquantity'] = $current_quantity;
    			//track item
    			$itemlog = new Itemlog;
    			$itemlog->userid = Yii::$app->user->id;
    			$itemlog->status = array_search('In Stock', Item::$status);
    			$itemlog->itemid = $current_item->id;
				$itemlog->locationid = $current_item->location;
    			$itemlog->save();
				//echo $current_quantity;
				//echo $quantity;
    			$done =  ($current_quantity===$quantity) ? true : false;
    			$_retArray = array('success' => true, 'message' => 'Serial number is succefully saved!', 'current_quantity' => $current_quantity);
    			if($done)
    				$_retArray['done'] = $done;
    		}
    		else
    			$_retArray = array('error' => true, 'message' => 'Something wrong when trying to save serial! Plese try again!');
    		echo json_encode($_retArray);
    		exit();
    	//}
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

    protected function findOModel($id) {
    	if (($model = Order::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}