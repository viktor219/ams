<?php 
	use app\modules\Orders\models\Order;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\Item;
	use app\models\Customer;
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Shipment;
	use app\models\ShippingCompany;
	use app\models\ShipmentMethod;	

$_shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
$shipmethod = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);
$_company = ShippingCompany::findOne($_method->shipping_company_id);
if($shipmethod->shipping_company_id===1)
{
	$ups = new \Ups\Entity\Service;
	$ups->setCode($shipmethod->_value);	
	$__shipping_method = $ups->getName();
}
else if($shipmethod->shipping_company_id===3) //Waiting DHL issues solved
{}
else
{
	$__shipping_method = $shipmethod->_value;
}
//
$ispallet = (strpos(strtolower($__shipping_method), 'freight') !== false) ? true : false;
?>
<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	'summary'=>'', 
	'columns' => [
		[
			'attribute'=>'serial',
			'format'=>'raw',
			'value'=>function ($model) {
				return '<div style="line-height:40px;"><b>' . $model->serial . '</b></div>';
			}
		],
		[
			'attribute'=>($ispallet) ? 'Pallet' : 'Box',
			'contentOptions' => ['style' => 'width:120px;'],
			'format'=>'raw',
			'value'=>function ($model) {
                                if($ispallet && empty($model->outgoingpalletnumber)){
                                    $model->outgoingpalletnumber = 1;
                                    $model->save(false);
                                } else if(!$ispallet && empty($model->outgoingboxnumber)){
                                    $model->outgoingboxnumber = 1;
                                    $model->save(false);
                                }
				$value = ($ispallet) ? $model->outgoingpalletnumber : $model->outgoingboxnumber;
				return '<div style="line-height:40px;"><input type="text" class="form-control pallet_box" value="'.$value.'"/></div>';
			}			
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'visibleButtons' => [
				'printlabels' => function ($model, $key, $index) {
					$order = Order::findOne($model->ordernumber);
					$customer = Customer::findOne($order->customer_id);
					return ($customer->requirelabelbox) ? true : false;
				}
			],
			'template'=>'{printlabels} {ready}',
                        'header' => '',
			'contentOptions' => ['style' => 'width:180px;text-align:center;', 'class' => 'action-buttons'],
			'controller' => 'orders',
			'buttons' => [
				'printlabels' => function ($url, $model, $key) {
					$_model = Models::findOne($model->model);
					$options = [
						'title' => 'Print Labels',
						'class' => 'btn btn-info',
					];
							
					$url = ['printlabel', 'id'=>$model->id];
				 
					return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, $options);
				},
				/*'printpackinglist' => function ($url, $model, $key) {
					$_model = Models::findOne($model->model);
					
					$options = [
						'title' => 'Print Packing List',
						'class' => 'btn btn-primary',
					];
						
					$url = ['printlabel', 'id'=>$modelid];
				
					return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, $options);
				},*/
				'ready' => function ($url, $model, $key) use ($ispallet){
					$options = [
						'title' => 'Ready',
                                                'type' => ($ispallet)?"pallet":"box",
                                                'id'=>$model->id,
						'class' => ($model->status == array_search('Ready to ship', Item::$status)) ? 'btn btn-success ready_button' : 'btn btn-info set_shipready'
					];
					
					//$url = \yii\helpers\Url::toRoute(['/inprogress/turnmodelonship', 'id'=>$model->id]);
					$url = ($model->status != array_search('Ready to ship', Item::$status)) ? ['readytoship', 'id'=>$model->id] : 'javascript:;';
				
					return Html::a('<span class="glyphicon glyphicon-ok-sign"></span>', $url, $options);
				}
			],
		]
	],
]); ?>