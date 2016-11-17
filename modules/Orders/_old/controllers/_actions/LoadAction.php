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
    		
    		if($type=="All"){
				if(Yii::$app->user->identity->usertype != User::TYPE_CUSTOMER && Yii::$app->user->identity->usertype != User::REPRESENTATIVE) 
				{
					$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, 'lv_salesorders.deleted' => 0]);
					
					if($customerid != 0) 
						$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
						
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');
				} else {
					$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
					$query = Order::find()->select('lv_salesorders.*')
								->innerJoin('lv_itemsordered', '`lv_itemsordered`.`ordernumber` = `lv_salesorders`.`id`')
								->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_salesorders`.`location_id`')
								->where(['`lv_salesorders`.`trackingnumber`'=>null, '`lv_salesorders`.`customer_id`'=>$customers, 'lv_salesorders.deleted' => 0]);
								
					if($customerid != 0) 
						$query->andWhere(['`lv_salesorders`.`customer_id`'=>$customerid]);
					
					$query->groupBy('`lv_itemsordered`.`ordernumber`', '`lv_locations`.`id`');					
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
    								->andWhere('lv_items.status >= ' . array_search('Shipped', Item::$status));
    			
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
    		}
    		//echo $query->createCommand()->sql;
    		$dataProvider = new ActiveDataProvider([
    				'query' => $query,
    				'pagination' => ['pageSize' => 30],
    				'sort'=> ['defaultOrder' => ['shipby'=>SORT_ASC]]
    				]);
    			
    		echo $this->controller->renderPartial('_order', [
    				'dataProvider' => $dataProvider,
    				]);
    		exit();
    	/*} else {
    		 
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}*/
    }
}