<?php 
use yii\helpers\Html;
use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Item;
	use app\models\Purchase;
?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	'summary'=>'',
	'columns' => [
		[
			'attribute' => 'ordernumber',
			'label' => 'PO#',
			'format' => 'raw',
			'value' => function($model) {
				return '<div style="line-height:40px;">' . Purchase::findOne($model->purchaseordernumber)->number_generated . '</div>';
			}
		],
		[
			'attribute' => 'item_id',  
			'label' => 'Items',
			'format' => 'raw',
			'value' => function($model) {
				$_model = Models::findOne($model->model);
				$_manufacturer = Manufacturer::findOne($_model->manufacturer);
				$qty = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
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
			//'attribute' => 'salesordernumber',
			'label' => 'Receiving',
			'format' => 'raw',
			'value' => function($model) {
				$purchase = Purchase::findOne($model->purchaseordernumber);
				$_content = (empty($purchase->salesordernumber)) ? 'Asset Stock' : $purchase->salesordernumber;
				return '<div style="line-height:40px;">' . $_content . '</div>';
			}
		],
		[
			'attribute' => 'qty',
			'label' => '',
			'format' => 'raw',
			'value' => function($model) {
				$qty = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();
				return '<button class="btn btn-default" id="po-row-received-items-count-' . $model->model . '">' . $qty . ' </button>';
			}
		],								
		[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{receive}',
			'controller' => 'receiving',
                        'contentOptions' => ['class' => 'action-buttons'],
			'buttons' => [
				'receive' => function ($url, $model, $key) {
					//$purchase = Purchase::findOne($model->purchaseordernumber);
					$options = [
						'title' => 'Receive',
						'class' => 'btn btn-info',
						'type'=>'button',
						'onClick'=>'LoadreceiveQtyModal("' . $model->purchaseordernumber . '", 2);'
					];
					$url = "javascript:;";
				
					return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
				},
			],
		],
	],
]); ?> 