<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\ModelAssembly;
	
?>
<?= GridView::widget([
	        'dataProvider' => $dataProvider,
    		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    		'summary'=>'',		    		
	        'columns' => [
				[
		            'attribute'=>'modelid',
					'format' => 'raw',
					'value'=> function ($model) {
						$_output = Models::findOne($model->modelid)->descrip;
						return "<div style='line-height: 40px;'>" . $_output . "</div>";
					}
				],
				[
	            	'label'=>'Quantity',
					'format' => 'raw',
					'value'=> function ($model) {
						//$number_items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->count();
						$number_items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->sum('quantity');
						$items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->all();
						$content = "";
						foreach($items as $item)
						{
							$_model = Models::findOne($item->partid);
							$_manufacturer = Manufacturer::findOne($_model->manufacturer);
							$newline = '(' . $item->quantity . ') ' . $_manufacturer->name . ' ' . $_model->descrip;		
							if($_manufacturer->name !=="" && strpos($content, $newline) === false)
								$content .= $newline . "<br/>";														
						}
						return '<a tabindex="0" class="btn btn-default" id="assembly-popover_' . $model->id . '" role="button" data-toggle="popover" rel="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . Html::encode($content) . '" style="color:#08c;">' . $number_items . '</a>';
					}
				],
				/*[
					'attribute'=>'quantity',
					'format' => 'raw',
					'value'=> function ($model) {
						$_output = $model->quantity;
						return "<button class='btn btn-default'>" . $_output . "</button>";
					}
				],	*/				
				[
					'attribute'=>'created_at',
					'format' => 'raw',
					'value'=> function ($model) {
						$_output = $model->created_at;
						return "<div style='line-height: 40px;'>" . $_output . "</div>";
					}
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'template'=>'{update} {delete}',
                                        'contentOptions' => ['class' => 'action-buttons'],
					'controller' => 'assembly',
					'buttons' => [
							'update' => function ($url, $model, $key) {
								$options = [
									'title' => 'Edit',
									'class' => 'btn btn-warning',
									'data-content'=>'Edit Order',
									'type'=>'button'
								];
								$url = \yii\helpers\Url::toRoute(['/assembly/update', 'id'=>$model->modelid]);

								return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
							},
							'delete' => function ($url, $model, $key) {
								$options = [
									'title' => 'Delete',
									'class' => 'btn btn-danger',
									'data-content'=>'Delete Order',
									'type'=>'button',
									'onClick'=> 'removeAssembly(' . $model->modelid . ')',
								];
								$url = 'javascript:;';

								return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
							}
						],								
				],
	        ],
	    ]); ?>