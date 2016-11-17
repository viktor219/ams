<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Vendor;
	use app\models\Item;
?>

	<?= GridView::widget([
        'dataProvider' => $dataProvider,
    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    	'summary'=>'',
        'columns' => [
            'number_generated',
			[
            	'attribute'=>'vendor_id',
				'format' => 'raw',
				'label'=>'Vendor',
				'value' => function($model) {
					return '<div style="line-height:40px;">' . Vendor::findOne($model->vendor_id)->vendorname . '</div>';
				}
			],
			[
    			'label'=>'Qty',
				'format' => 'raw',
				'value' => function($model) {
					$number_items = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id])->count();
					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getpurchaseindexdetails?idpurchase=' . $model->id . '" role="button" data-toggle="popover" data-html="true" data-placement="left" data-animation="true" data-trigger="focus" title="Items (' . $number_items . ')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>';
				}
			],
			[
				'attribute'=>'estimated_time',
				'format' => 'raw',
				'value' => function($model) { 
					if(!empty($model->estimated_time))
						$_content = date('m/d/Y', strtotime($model->estimated_time));
					else 
						$_content = "-";
					
					return '<div style="line-height:40px;">' . $_content . '</div>';
				}
			],
			[
				'attribute'=>'trackingnumber',
				'format' => 'raw',
				'value' => function($model) {
					if(!empty($model->trackingnumber))
						$_content = $model->trackingnumber;
					else
						$_content = "-";
			
					return '<div style="line-height:40px;">' . $_content . '</div>';
				}				
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{receive}',
				'controller' => 'orders',
                                'contentOptions' => ['class' => 'action-buttons'],
				'buttons' => [
					'receive' => function ($url, $model, $key) {
						$options = [
							'title' => 'Receive',
							'class' => 'btn btn-info',
							'type'=>'button',
							'pid'=>$model->id,
							'onClick'=>'ReceivePurchaseOrder("'.$model->id.'");'
						];
						$url = "javascript:;";
					
						return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
					},
					'update' => function ($url, $model, $key) {
						$options = [
							'title' => 'Edit',
							'class' => 'btn btn-warning',
							'type'=>'button'
						];
						$url = "javascript:;";
					
						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
					},
					'delete' => function ($url, $model, $key) {
						$options = [
							'title' => 'Delete',
							'class' => 'btn btn-danger',
							'data-content'=>'Delete Order',
							'type'=>'button',
						];
						$url = "javascript:;";
					
						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
					}
				],
			]
        ],
    ]); ?>