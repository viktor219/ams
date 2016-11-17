<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\ModelAssembly;
	use app\models\Manufacturer;
	use app\models\Department;
	use app\models\Category;
	use app\models\Inventory;
	use app\models\Partnumber;
	use app\models\Item;
	use app\models\Models;
	use app\models\Medias;
	use app\models\Customer;
	use yii\widgets\Pjax;
?>

	<?= GridView::widget([
	                'dataProvider' => $dataProvider,
	                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	                'summary'=>'',
	                'columns' => [
	                    [
	                        'attribute' => 'imagepath',  
							'label' => 'Thumbnail',
							'format' => 'raw',
							'value' => function($model) {
								$picture = Medias::findOne($model->image_id);
								if($picture!==null)
								return Html::img(Yii::getAlias('@web').'/public/images/models/'.$picture->filename, ['alt'=>'logo', 'onClick'=>'showPicture("' . Yii::getAlias('@web').'/public/images/models/'. $picture->filename . '");', 'height'=>'33px']);
							}
	                    ],
	                    [
		                    'attribute' => 'aei',
		                    'label' => 'Part Numbers',
		                    'format'=>'raw',
		                    'value' => function($model) {
		                    	if(!empty($model->aei))
			                    	return '<a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="partitem-popover_' . $model['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> '. $model['aei'] .' </a>';
		                    	else 
		                    		return "<div style='line-height: 40px;'>No Part Number</div>";
		                    },
		                    'filter'=>false,
	                    ],
	                    [
	                        'attribute' => 'modelname',  
							'label' => 'Model',
							'format' => 'raw',
	                        'value' => function($model) {
	                            return "<div style='line-height: 40px;'>" . Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip . "</div>";
	                        },						
	                    ],
	                    [
							'label' => 'Inventory',
							'format' => 'raw',
	                        'value' => function($model) use ($customerid) {
	                        	$content = "";
	                        	$sum = 0;
                                        if($model->assembly) {
                                                $number_items = ModelAssembly::find()->where(['modelid'=>$model->id])->sum('quantity');
                                                $nbr_items_in_stock = Item::find()
                                                                ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                                                                ->where(['modelid'=>$model->id, '`lv_items`.`customer`'=>$customerid])
                                                                ->andwhere(['status' => array_search('In Stock', Item::$status)])
//								->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')
                                                                ->count();
                                                $sum = ($number_items!=0) ? $nbr_items_in_stock / $number_items : 0;
                                        }
                                        else 
                                        {
                                                $sum_in_stock = Item::find()->where(['model'=>$model->id, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customerid])->count();
                                                //$sum_in_progress = Item::find()->where(['model'=>$model->id, 'status'=>array_search('Ready to ship', Item::$status), 'customer'=>$customerid])->count();
                                                //$sum = $sum_in_stock + $sum_in_progress;
                                                $sum = $sum_in_stock;
                                        }
//                                    return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-content = "" data-animation="true" data-trigger="focus" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorydata?id='.$model['id'].'" data-original-title="' . $model['name'] . ' ' . $model['descrip'] . '"> ' . $sum . ' </a>';
	                            return '<a tabindex="0" class="btn btn-default"> ' . $sum . ' </a>';
	                        },
	                    ],
	                    [
		                    'attribute' => 'assembly',
		                    'label' => 'Assembly',
		                    'format' => 'raw',
		                    'value' => function($model) {
		                    	$output = ($model->assembly) ? 'Yes' : 'No';
		                    	return "<div style='line-height: 40px;'>" . $output . "</div>";
		                    },
	                    ],	                  
	                    [
	                        'class' => 'yii\grid\ActionColumn',
	                        'template'=>'{view}{update}{delete}{reorder}',
	                        'contentOptions' => ['style' => 'width:220px;'],
	                        'controller' => 'inventory',
	                        'buttons' => [
								'reorder' => function ($url, $model, $key) {
										return Html::a('', 'javascript://', ['title' => Yii::t('app', 'ReOrder'),
	                                    'class' => 'glyphicon glyphicon-new-window btn btn-primary showreorder', 'data-toggle'=>'modal', 'data-target'=>'#reorder', 'id'=>$model->id . '||' . Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip . '||' . (Item::find()->where(['model'=>$model->id])->count()-Item::find()->where(['model'=>$model->id, 'status'=>3])->count())]);
								},
	                            'view' => function ($url, $model, $key) {
	                                $options = [
	                                    'title' => 'View',
	                                    'class' => 'btn btn-info',
	                                ];
	                                $url = \yii\helpers\Url::toRoute(['/inventory/view', 'id'=>$model->id]);
	
	                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
	                            },
	                            'update' => function ($url, $model, $key) {
	                                $options = [
	                                    'title' => 'Update',
	                                    'class' => 'btn btn-primary',
	                                ];
	                                $url = \yii\helpers\Url::toRoute(['/inventory/update', 'id'=>$model->id]);
	
	                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
	                            },
	                            'delete' => function ($url, $model, $key) {
	                                $options = [
	                                    'title' => 'Delete',
	                                    'class' => 'btn btn-warning',
	                                    'onClick'=> 'return confirm(\'are you sure to delete this inventory ?\');',                                   
	                                ];
	                                $url = \yii\helpers\Url::toRoute(['/inventory/delete', 'id'=>$model->id]);
	
	                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
	                            }
	                        ],
	                    ],
	                ],
	            ]); ?>