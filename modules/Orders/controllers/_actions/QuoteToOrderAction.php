<?php
    /**
     * Convert quote Order to normal Order.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use Yii;
use yii\base\Action;
use app\modules\Orders\models\Order;
use yii\web\NotFoundHttpException;
use app\models\QOrder;
use app\models\QItemsordered;
use app\models\ItemHasOption;
use app\models\QItemHasOption;
use app\models\Orderlog;
use app\models\QOrderlog;
use app\models\Item;
use app\models\Itemlog;
use app\models\Itemsordered;
use app\models\Models;
use app\models\Customer;
use app\models\Shipment;
use app\models\QShipment;

class QuoteToOrderAction extends Action
{
    public function run($id)
    {
    	$model = QOrder::findOne($id);
    	
    	$datetime = date('Y-m-d H:i:s');
    	
    	$_orderconvertstatus = false;
    	
    	$_itemorderedconvertstatus = false;
    	
    	$_shipmentconvertstatus = false;
    	
    	$_orderlogconvertstatus = false;
    	
    	$_itemhasoptionstatus = false;
    	
    	if(!empty($model)) {
    		//
    		$itemsOrdered = QItemsordered::find()->where(['ordernumber' => $model->id])->all();
    			
    		$customer = Customer::findOne($model->customer_id);
    	
    		$quoteShipment = QShipment::find(['orderid' => $model->id])->one();
    			
    		$quoteOrderLog = QOrderlog::find(['orderid' => $model->id])->one();
    			
    		$quoteItemsHasOption = QItemHasOption::find(['orderid' => $model->id])->all();			
			//
    		$orderGenerated = new Order;
    			
    		$orderGenerated->customer_id = $model->customer_id;
    		$orderGenerated->location_id = $model->location_id;
    		$orderGenerated->type = $model->type;
    		$orderGenerated->notes =  $model->notes;
    		$orderGenerated->ordertype =  $model->ordertype;
    		$orderGenerated->shipby =  $model->shipby;
    		///---- review
    		$orderGenerated->number_generated = $model->number_generated;
    		$orderGenerated->customer_po = $model->customer_po;
    		$orderGenerated->enduser_po =  $model->enduser_po;
    		$orderGenerated->orderfile = $model->orderfile;
    		if($orderGenerated->save()) {
    			$model = $orderGenerated;
    			$_orderconvertstatus = true;
				
    			//save shipment
    			$_shipment = new Shipment;
    			$_shipment->orderid = $model->id;
    			$_shipment->accountnumber = $quoteShipment->accountnumber;
    			$_shipment->shipping_deliverymethod = $quoteShipment->shipping_deliverymethod;
    			$_shipment->locationid = $quoteShipment->locationid;
    			if($_shipment->save())
    				$_shipmentconvertstatus = true;
    				
    			//log current order
    			$_orderlog = new Orderlog;
    			$_orderlog->orderid = $model->id;
    			$_orderlog->userid = $quoteOrderLog->userid;
    			$_orderlog->status = $quoteOrderLog->status;
    			if($_orderlog->save())
    				$_orderlogconvertstatus = true;
    		}
    			
    		//start converting...
    		foreach($itemsOrdered as $itemOrdered)
    		{
    			//save itemordered
    			$_itemOrdered = new Itemsordered;
    			$_itemOrdered->customer = $itemOrdered->customer;
    			$_itemOrdered->qty = $itemOrdered->qty;
    			$_itemOrdered->price = $itemOrdered->price;
    			$_itemOrdered->package_optionid = $itemOrdered->package_optionid;
    			$_itemOrdered->model = $itemOrdered->model;
    			$_itemOrdered->ordernumber = $model->id;
    			$_itemOrdered->timestamp = $itemOrdered->timestamp;
    			$_itemOrdered->status = $itemOrdered->status;
    			$_itemOrdered->notes = $itemOrdered->notes;
    			$_itemOrdered->ordertype = $itemOrdered->ordertype;
    			if($_itemOrdered->save()) //
    			{
    				$_itemorderedconvertstatus = true;
					
					//save options for order (cleaning, testing...)
    				foreach($quoteItemsHasOption as $quoteItemHasOption)
    				{
    					$_itemHasOption = new ItemHasOption;
    					$_itemHasOption->orderid = $model->id;
    					$_itemHasOption->itemid = $_itemOrdered->id;
    					$_itemHasOption->optionid = $quoteItemHasOption->optionid;
    					$_itemHasOption->ordertype = $quoteItemHasOption->ordertype;
    					if($_itemHasOption->save())
    						$_itemhasoptionstatus = true;
    				}
    			}
    	
    			//ITEMS CONVERSION [lv_items]
    			for($i=0;$i<$itemOrdered->qty;$i++) {
    				$_find_model = Models::findOne($itemOrdered->model);
    				$_data_model = $itemOrdered->model;
    				if(!empty($_find_model) && !$_find_model->assembly)
    					//save simple items [not assembly]
    				{
    					$in_stock = Item::find()->where(['model'=>$itemOrdered->model, 'status'=>4])->count();
    					if($model->ordertype==1 || $model->ordertype==4 || ($model->ordertype==3 && $customer->trackincomingserials == 1))//purchase, warehousing orders
    					{
    						$__qtyrequested = 0;
    						if($itemOrdered->qty>$in_stock) {
    							$__qtyrequested = $itemOrdered->qty-$in_stock;
    						}
    						if($__qtyrequested > 0) {
    	
    							for($i=0; $i<$__qtyrequested;$i++){
    								$item = new Item;
    								$item->status = 1;
    								$item->model = $itemOrdered->model;
    								$item->ordernumber = $model->id;
    								$item->customer = $model->customer_id;
    								$item->location = $model->location_id;
    								$item->received = $datetime;
    								$item->lastupdated = $datetime;
    								$item->notes = $model->notes;
    								if($item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = 1;
    									$itemlog->itemid = $item->id;
    									$itemlog->save();
    									$successorder = true;
    								}
    								else
    									$successorder = false;
    							}
    							//
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$itemOrdered->model])->all();
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    						} else {
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$itemOrdered->model])->limit($itemOrdered->qty)->all();
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    						}
    					}
    					else if($model->ordertype==3 && $customer->trackincomingserials == 0) //integration order
    					{
    						$_model = Models::findOne($itemOrdered->model);
    						if(Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->count() > $itemOrdered->qty)
    						{
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->limit($itemOrdered->qty)->all();
    							//
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    						}
    						else
    						{
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->all();
    							$qtytoinserted = $itemOrdered->qty - Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->count();
    							//
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    							//
    							for($i=0; $i<$qtytoinserted;$i++){
    								$item = new Item;
    								$item->status = array_search('Requested', Item::$status);
    								$item->model = $itemOrdered->model;
    								$item->ordernumber = $model->id;
    								$item->customer = $model->customer_id;
    								$item->location = $model->location_id;
    								$item->received = $datetime;
    								$item->lastupdated = $datetime;
    								$item->notes = $model->notes;
    								if($item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Requested', Item::$status);
    									$itemlog->itemid = $item->id;
    									$itemlog->save();
    									$successorder = true;
    								}
    								else
    									$successorder = false;
    							}
    						}
    						//}
    					}
    					else if($model->ordertype==2)
    					{
    						$_model = Models::findOne($itemOrdered->model);
    						if(Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->count() > $itemOrdered->qty)
    						{
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->limit($itemOrdered->qty)->all();
    							//
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    						}
    						else
    						{
    							$_instock_items = Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->all();
    							$qtytoinserted = $itemOrdered->qty - Item::find()->where(['status'=>array_search('In Stock', Item::$status), 'model'=>$_model->id])->count();
    							//
    							foreach($_instock_items as $_instock_item) {
    								$_instock_item->status = array_search('Reserved', Item::$status);
    								$_instock_item->ordernumber = $model->id;
    								if($_instock_item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('Reserved', Item::$status);
    									$itemlog->itemid = $_instock_item->id;
    									$itemlog->save();
    								}
    							}
    							//
    							for($i=0; $i<$qtytoinserted;$i++){
    								$item = new Item;
    								$item->status = array_search('In Transit', Item::$status);
    								$item->model = $itemOrdered->model;
    								$item->ordernumber = $model->id;
    								$item->customer = $model->customer_id;
    								$item->location = $model->location_id;
    								$item->received = $datetime;
    								$item->lastupdated = $datetime;
    								$item->notes = $model->notes;
    								if($item->save()){
    									//track item
    									$itemlog = new Itemlog;
    									$itemlog->userid = Yii::$app->user->id;
    									$itemlog->status = array_search('In Transit', Item::$status);
    									$itemlog->itemid = $item->id;
    									$itemlog->save();
    									$successorder = true;
    								}
    								else
    									$successorder = false;
    							}
    						}
    					}
    				}
    			}
    		}
    		//clear all entries converted
    		if($_orderconvertstatus) //delete quote order entry
    			QOrder::findOne($id)->delete();
    	
    		if($_itemorderedconvertstatus) { //delete itemordered entries
    			foreach($itemsOrdered as $itemOrdered) {
    				$itemOrdered->delete();
    			}
    		}
    			
    		if($_itemhasoptionstatus) { //delete item options entries
    			foreach($quoteItemsHasOption as $quoteItemHasOption)
    			{
    				$quoteItemHasOption->delete();
    			}
    		}
    			
    		if($_shipmentconvertstatus) //delete shipment entry
    			$quoteShipment->delete();
    	
    		if($_orderlogconvertstatus) //delete order log entry
    			$quoteOrderLog->delete();
			//
			$_message = 'Quote Order has been converted successfully!';
			Yii::$app->getSession()->setFlash('success', $_message);
    		//
    		return $this->controller->redirect(Yii::$app->request->referrer);
    	} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}		
    }
}