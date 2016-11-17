<?php

namespace app\controllers;

use Yii;
use app\components\AccessRule;
use yii\filters\AccessControl;

use app\modules\Orders\models\Order;
use app\models\Customer;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\Location;
use app\models\Medias;
use app\models\Shipment;
use app\models\User;
use app\models\Item;
use app\models\Itemlog;

use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\web\NotFoundHttpException;


class ShipmentsController extends \yii\web\Controller
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
							'index', 'generate'				
						],
		    	'rules' => [
			    	[
				    	'actions' => [
							'index', 'generate'							
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
			    	]
		    	],
	    	]
    	];
    }
	
    public function actionIndex($customer)
    {
		$customer = Customer::findOne($customer);
		
		$query = Shipment::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_shipments`.`orderid`')
								->andFilterWhere(['customer_id'=>$customer->id]);
								
		$dataProvider = new ActiveDataProvider([
					'query' => $query,
					'pagination' => ['pageSize' => 10],
				]);
		
        return $this->render('index', [
			'dataProvider' => $dataProvider,
			'customer'=>$customer
		]);
    }
	
	public function actionGenerate($id)
	{
    	$model = $this->findModel($id);
    	    	
    	$shipping_method = ShipmentMethod::findOne($model->shipping_deliverymethod);
    	
    	$shipping_company = ShippingCompany::findOne($shipping_method->shipping_company_id);
		
		$order = Order::findOne($model->orderid);
    	
    	$customer = Customer::findOne($order->customer_id);
		
		$_media_customer = Medias::findOne($customer->picture_id);
		
		$location = Location::findOne($model->locationid);
		
		/*$logitems = Itemlog::find()->innerJoin('lv_items', '`lv_items`.`id` = `lv_itemslog`.`itemid`')
						->where(['shipment_id'=>$model->id])
						->all();*/
		$serializedshipping = "SELECT COUNT(*) as counted, lv_manufacturers.name, lv_models.descrip, lv_items.model
						FROM lv_items 
						INNER JOIN lv_models ON lv_items.model=lv_models.id 
						INNER JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id 
						WHERE ordernumber=:id AND (status=:shipped OR status=:inprogress) AND serialized='1' GROUP BY lv_models.id
						ORDER BY counted DESC, name, descrip";
						
		$unserializedshipping = "SELECT COUNT(*) as counted, lv_manufacturers.name, lv_models.descrip, lv_items.model
						FROM lv_items 
						INNER JOIN lv_models ON lv_items.model=lv_models.id 
						INNER JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id 
						WHERE ordernumber=:id AND (status=:shipped OR status=:inprogress) AND serialized='0' GROUP BY lv_models.id
						ORDER BY counted DESC, name, descrip";						
		
		$connection = Yii::$app->getDb();
		
		$command = $connection->createCommand($serializedshipping, [':id'=>$model->id, ':shipped'=>array_search('Shipped', Item::$status), ':inprogress'=>array_search('In Progress', Item::$status)]);
	
		$serializedshippingresults = $command->queryAll();		
		
		$countSerializedresults = count($serializedshippingresults);
		
		$command = $connection->createCommand($unserializedshipping, [':id'=>$model->id, ':shipped'=>array_search('Shipped', Item::$status), ':inprogress'=>array_search('In Progress', Item::$status)]);
	
		$unserializedshippingresults = $command->queryAll();		
		
		$countUnSerializedresults = count($unserializedshippingresults);
    	
    	$maxRows = 18;
    	
    	$content = $this->renderPartial('_generate', [
    			'model'=>$model, 
    			'order'=>$order, 
    			'customer'=>$customer, 
    			'location'=>$location,
    			'shipping_method'=>$shipping_method,
    			'shipping_company'=>$shipping_company,
    			'_media_customer'=>$_media_customer, 
    			'maxRows'=>$maxRows,
    			'serializedshippingresults'=>$serializedshippingresults,
    			'countSerializedresults'=>$countSerializedresults,
    			'unserializedshippingresults'=>$unserializedshippingresults,
    			'countUnSerializedresults'=>$countUnSerializedresults,
				//'logitems'=>$logitems
    			]);
    	
    	$cssContent = "
			table.page_header {width: 100%; border: none; background-color: #555; border-bottom: solid 1mm #0361A7; padding: 1mm }
			table.page_body {width: 100%; border: none; background-color: #fff; border-bottom: solid 1mm #0361A7; padding: 2mm }
			table.page_body2 {width: 100%; border: none; background-color: #fff; border-bottom: solid 1mm #0361A7; padding: 2mm }
			table.page_body3 {width: 100%; border: none; background-color: #fff; border-bottom: solid 1mm #0361A7; padding: 2mm }
			table.page_body4 {width: 100%; border: none; background-color: #fff; border-bottom: solid 1mm #0361A7; padding: 2mm }
			table.page_body5 {width: 100%; border: none; background-color: #fff; padding: 2mm }
			table.page_footer {width: 100%; border: none; background-color: #fff; padding: 2mm}
			table.widetable {width: 100%; border: none; background-color: #fff; }			
			.row-border {border-bottom: 1px solid #ccc}
			.cell-border {border-bottom: 1px solid #ccc;padding: 5px;}	
			.head-border {text-align: left;border-bottom: 1px solid #999;padding: 5px;}
			.largeheadertext {font-size: 20px;color: #fff;}
			.center {text-align: center;}
    	";
    	
    	$pdf = Yii::$app->pdf;
    	
    	$pdf->content = $content;
    	
    	$mpdf = $pdf->api; // fetches mpdf api
    	
    	$mpdf->WriteHTML($cssContent, 1, true, true);
    	
    	$mpdf->SetTitle('Shipment Manifest');
    	
    	//$mpdf->SetHeader('Shipment Manifest');
    	
    	$mpdf->SetFooter('{PAGENO}');
    	
	    // return the pdf output as per the destination setting
	    return $pdf->render(); 		
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
        if (($model = Shipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }	
}
