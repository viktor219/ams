<?php 
use yii\helpers\Html;
use yii\helpers\Url;
	use yii\grid\GridView;
	use app\modules\Orders\models\Order;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Item;
	use app\models\Customer;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	'summary'=>'',
	'columns' => [
		[
			'attribute' => 'ordernumber',
			'label' => 'SO#',
			'format' => 'raw',
			'value' => function($model) {
				return '<div style="line-height:40px;">' . Order::findOne($model->ordernumber)->number_generated . '</div>';
			}
		],
		[
			'attribute' => 'item_id',  
			'label' => 'Items',
			'format' => 'raw',
			'value' => function($model) {
					$_model = Models::findOne($model->model);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
					$qty = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->count();												
					return '<div style="line-height:40px;">' . $qty . '-' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
			}
		],  
		[
			'attribute' => 'created_at',
			'label' => 'Received Date',
			'format' => 'raw',
			'value' => function($model) {
					return '<div style="line-height:40px;">' . date('m/d/Y', strtotime($model->created_at)) . '</div>';
			}
		],          
		[
			'label' => 'Receiving',
			'format' => 'raw',
			'value' => function($model) {
				$customer = Order::findOne($model->ordernumber)->customer_id;
				return '<div style="line-height:40px;">' . Customer::findOne($customer)->companyname . ' Stock</div>';
			}
		],
		[
			'attribute' => 'qty',
			'label' => '',
			'format' => 'raw',
			'value' => function($model) {
				$qty = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->count();
				return '<button class="btn btn-default" id="so-row-received-items-count-'. $model->model .'">' . $qty . ' </button>';
			}
		],								
		[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{receive}',
			'controller' => 'receiving',
			'buttons' => [
				'receive' => function ($url, $model, $key) {
					//$purchase = Purchase::findOne($model->purchaseordernumber);
					$options = [
						'title' => 'Receive',
						'class' => 'btn btn-info',
						'type'=>'button',
						'onClick'=>'LoadreceiveQtyModal("' . $model->ordernumber . '", 1);'
					];
					$url = "javascript:;";
				
					return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
				},
			],
		],
	],
]); ?> 