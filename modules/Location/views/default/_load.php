<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\Item;
	use app\models\LocationClassment;
	use app\models\LocationParent;
	use app\models\UserHasCustomer;
?>

<?= GridView::widget([
        'dataProvider' => $dataProvider,
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
		'summary'=>'',
		'emptyText'=>'Locations Not found',
        'columns' => [		
			[
				'label' => 'Division ID',
				'value' => function ($model) {
					$_parent = LocationClassment::find()->where(['location_id'=>$model->id])->one();
					if($_parent === null)
						$_output = "-";
					else
						$_output = LocationParent::findOne($_parent->parent_id)->parent_code;
						
					return $_output;
				}
			],	
			[
			'label' => 'Division Name',
			'value' => function ($model) {
				$_parent = LocationClassment::find()->where(['location_id'=>$model->id])->one();
				if($_parent === null)
					$_output = "Uncategorized";
				else
					$_output = LocationParent::findOne($_parent->parent_id)->parent_name;
					
				return $_output;
			}
			],	
			[
				'label' => 'Store Number',
				'value' => function ($model) {
					$output = "-";
					if(!empty($model->storenum))
						$output = $model->storenum;
						
					return $output;
				}
			],	
			[
			'label' => 'Store Name',
			'value' => function ($model) {
				$output = "-";
				if(!empty($model->storename))
					$output = $model->storename;
			
				return $output;
			}
			],				
			[
				'label' => 'Full Address',
				'value' => function ($model) {
					//
					$output = $model->address . " " . $model->address2 . " " . $model->city . " " . $model->state . " " . $model->zipcode;	
					
					return $output;
				}				
			],
			[
			'label' => 'Email',
			'value' => function ($model) {
				$output = "-";
				if(!empty($model->email))
					$output = $model->email;
					
				return $output;
			}
			],
			[
			'label' => 'Phone',
			'value' => function ($model) {
				$output = "-";
				if(!empty($model->phone))
					$output = $location->phone;
					
				return $output;
			}
			],						
			/*[
				'label' => 'Inventory',
				'format' => 'raw',
				'value' => function ($model) {
					$customers = \yii\helpers\ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
					$number_items = Item::find()->where(['location'=>$model->id, 'customer'=>$customers])->count();
					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getlocationindexdetails?idlocation='.$model->id.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" rel="popover" style="color:#08c;">' . $number_items . '</a>';
				}
			],	*/					
			[
				'class' => 'yii\grid\ActionColumn',
				'template'=> (empty($deleted)) ? '{map} {update} {delete}' : '{reallocate} {revert}',
				'header' => '',
				'contentOptions' => ['style'=>'width:110px;', 'class' => 'action-buttons'],
				'visibleButtons' => [
					'reallocate' => function ($model, $key, $index) {
						$has_items = Item::find()->where(['location'=>$model->id])->count();
						
						//$has_items =1;
						
						return ($has_items) ? true : false;
					}				
				],
				'buttons' => [				
					'reallocate' => function ($url, $model, $key) {
						$options = [
							'title' => 'Reallocate',
							'class' => 'btn btn-sm btn-warning',
							'id' => 'reallocatebtn-' . $model->id
						];
						
						$url = 'javascript:;';
					
						return Html::a('<span class="glyphicon glyphicon-home" aria-hidden="true"></span>', $url, $options);
					},
					'revert' => function ($url, $model, $key) {
						$options = [
						'title' => 'Revert',
						'class' => 'btn btn-sm btn-info revertLocation',
						];
						$url = \yii\helpers\Url::toRoute(['/location/revert', 'id'=>$model->id]);
					
						return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
					},							
					/*'map' => function ($url, $model, $key) {
						$options = [
							'title' => 'Map',
							'class' => 'btn btn-info',
						];
						$url = 'javascript:;';

						return Html::a('<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>', $url, $options);
					},*/
					'update' => function ($url, $model, $key) {
						$options = [
							'title' => 'Edit',
							'class' => 'btn btn-sm btn-warning',
						];
						$url = \yii\helpers\Url::toRoute(['/location/update', 'id'=>$model->id]);

						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
					},
					'delete' => function ($url, $model, $key) {
						$options = [
							'title' => 'Delete',
							'class' => 'btn btn-sm btn-danger deleteLocation',
							//'id' => 'soft_delete_location'
						];
						$url = \yii\helpers\Url::toRoute(['/location/delete', 'id'=>$model->id]);
						///$url = 'javascript:;';

						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
					}
				],
			],
        ],
    ]); ?>