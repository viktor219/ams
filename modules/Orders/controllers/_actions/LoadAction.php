<?php
    /**
     * Ajax Lists all Order models.
     * @return mixed
     */

namespace app\modules\Orders\controllers\_actions;

use Yii;
use yii\base\Action;
use app\modules\Orders\models\Order;
use app\models\Ordertype;
use app\models\Item;
use yii\helpers\ArrayHelper;
use app\models\UserHasCustomer;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;

class LoadAction extends Action
{
    public function run()
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
    		
    		$excludeStatus = array();
    		
    		if($type=="All"){
				if(Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE) 
				{
					/*$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 0]);*/
					$query = Order::find()->select('lv_salesorders.*')
						->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
						->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`ordertype`'=>4, 'lv_salesorders.deleted' => 0])
						//->andWhere(['<>', '`lv_items`.`status`', array_search('Shipped', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('In Transit', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('Delivered', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('Received', Item::$status)])
						->groupBy('`lv_itemsordered`.`ordernumber`, `lv_items`.`ordernumber`', '`lv_locations`.`id`');
					
					if($customerid != 0)
						$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
						
					$query2 = Order::find()->select('lv_salesorders.*')
						->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
						->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`') 
						->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
						->leftJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`service_id` = `lv_salesorders`.`id`') 
						->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`ordertype`'=>2, 'lv_salesorders.deleted' => 0])
						//->andWhere(['<>', '`lv_items`.`status`', array_search('Shipped', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('In Transit', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('Delivered', Item::$status)])
						->andWhere(['<>', '`lv_items`.`status`', array_search('Received', Item::$status)])
						->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_items`.`ordernumber`', '`lv_locations`.`id`');
					
					if($customerid != 0)
						$query2->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
					
					$query = $query->union($query2);
						
					//$excludeStatus[] = array_search('Shipped', Item::$status);
					$excludeStatus[] = array_search('In Transit', Item::$status);
					$excludeStatus[] = array_search('Delivered', Item::$status);
					$excludeStatus[] = array_search('Received', Item::$status);					
						
					//$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
				} else {
					$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
					$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->leftJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`service_id` = `lv_salesorders`.`id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`ordertype`'=>[2, 4], '`lv_salesorders`.`customer_id`'=>$customers, 'lv_salesorders.deleted' => 0])
								->andWhere(['<>', '`lv_items`.`status`', array_search('In Transit', Item::$status)])
								->andWhere(['<>', '`lv_items`.`status`', array_search('Delivered', Item::$status)])
								->andWhere(['<>', '`lv_items`.`status`', array_search('Received', Item::$status)])
								->groupBy('`lv_items`.`ordernumber`','`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`')
								->orderBy('`lv_items`.`status`');
					
					/*$query2 = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->leftJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`service_id` = `lv_salesorders`.`id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`ordertype`'=>2, '`lv_salesorders`.`customer_id`'=>$customers, 'lv_salesorders.deleted' => 0])
								//->andWhere(['<>', '`lv_items`.`status`', array_search('Shipped', Item::$status)])
								->andWhere(['<>', '`lv_items`.`status`', array_search('In Transit', Item::$status)])
								->andWhere(['<>', '`lv_items`.`status`', array_search('Delivered', Item::$status)])
								->andWhere(['<>', '`lv_items`.`status`', array_search('Received', Item::$status)])
								->groupBy('`lv_items`.`ordernumber`', '`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
								
					$query = $query->union($query2);	*/				
					
					//$excludeStatus[] = array_search('Shipped', Item::$status);
					$excludeStatus[] = array_search('In Transit', Item::$status);
					$excludeStatus[] = array_search('Delivered', Item::$status);
					$excludeStatus[] = array_search('Received', Item::$status);
				}
    		}
    		else if ($type=="Rcompleted")
    		{
    			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    			$query = Order::find()->select('lv_salesorders.*')
					    			->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
					    			->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
					    			->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
					    			->where(['`lv_salesorders`.`customer_id`'=>$customers, '`lv_salesorders`.`deleted`' => 0])
    								->andWhere('lv_items.status > ' . array_search('Shipped', Item::$status))
    								->andWhere('lv_items.status != ' . array_search('Awaiting Return', Item::$status))
									->orWhere(['`lv_salesorders`.`customer_id`'=>$customers, '`lv_salesorders`.`deleted`' => 0, '`lv_items`.`status`'=>array_search('Received', Item::$status)]);
    			
    			if($customerid != 0)
    				$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
    				
    			$query->groupBy('`lv_items`.`ordernumber`', '`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');    			
    		}
    		else if ($type=="Shipped")
    		{
    			$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
    			$query = Order::find()->select('lv_salesorders.*')
    			->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
    			->innerJoin('lv_items', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
    			->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
    			->where(['`lv_salesorders`.`customer_id`'=>$customers, '`lv_salesorders`.`deleted`' => 0])
    			->andWhere('lv_items.status = '. array_search('In Transit', Item::$status));
    			 
    			if($customerid != 0)
    				$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
    		
    			$query->groupBy('`lv_items`.`ordernumber`', '`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
    		}
    		else
    		{
    			$query = Order::find()->select('lv_salesorders.*')
							->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
							->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
							->where(['lv_salesorders.ordertype' => Ordertype::findOne(['name'=>$type])->id])
							->andWhere(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 0]);
				if($customerid != 0) 
					$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
				
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
				//
				if($type=="Service")
					$query = $query->innerJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`service_id` = `lv_salesorders`.`id`');
    		}
    		//echo $query->createCommand()->sql;
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 30],
    				'sort'=> ['defaultOrder' => (Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE)  ? ['shipby'=>SORT_ASC] : ['created_at'=>SORT_ASC]]
    				]);
    			
    		echo $this->controller->renderPartial('_order', [
    				'dataProvider' => $dataProvider,
					'type' => $type,
    				'excludeStatus' => $excludeStatus
    				]);
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
}