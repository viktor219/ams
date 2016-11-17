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
    use app\models\User;
	use yii\widgets\Pjax;
    use yii\helpers\ArrayHelper;
    use yii\models\UserHasCustomer;

    $_templatesaccess = "{view}{update}{delete}{reorder}";
	if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
    
	$_templatesaccess = '{warehouseorder}';
	$_defaultwidth = 'width: 260px; min-width: 260px; vertical-align:middle';
	if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE)
		$_defaultwidth = 'width: 80px; min-width: 80px; vertical-align:middle';
        if( Yii::$app->user->identity->usertype===User::TYPE_ADMIN || 
            Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER ||
            Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN || 
            Yii::$app->user->identity->usertype===User::TYPE_SALES || 
            Yii::$app->user->identity->usertype===User::TYPE_SHIPPING ||
            Yii::$app->user->identity->usertype===User::TYPE_BILLING) {
            $_templatesaccess .= '{merge}{transfer}';
        }
?>

	<?= GridView::widget([
	                'dataProvider' => $dataProvider,
	                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	                'summary'=>'',
	                'columns' => [
	                    [
	                        'attribute' => 'imagepath',  
                                'contentOptions' => ['style' => 'vertical-align:middle'],
                                'label' => 'Thumbnail',
                                'format' => 'raw',
                                'value' => function($model) {
                                        if($model['filename'])
                                                return Html::img(Yii::getAlias('@web').'/public/images/models/'. $model['filename'], ['alt'=>'logo', 'onClick'=>'ModelsViewer(' . $model['id'] . ');', 'height'=>'33px']);
                                }
	                    ],
	                    [
		                    'attribute' => 'aei',
		                    'label' => 'Part Numbers',
		                    'format'=>'raw',
                                    'contentOptions' => ['style' => 'vertical-align:middle'],
		                    'value' => function($model) {
		                    	if(!empty($model['aei']))
			                    	return '<a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="partitem-popover_' . $model['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> '. $model['aei'] .' </a>';
		                    	else 
		                    		return "No Part Number";
		                    },
		                    'filter'=>false,
	                    ],
                            [
                                'attribute' => 'category_id',
                                'format' => 'raw',
                                'label' => 'Category',
                                'contentOptions' => ['style' => 'vertical-align:middle'],
                                'value' => function($model){
                                        $category = Category::findOne($model['category_id']);
                                        return $category['categoryname'];
                                }
                            ],
	                    [
	                        'attribute' => 'modelname',  
                                'label' => 'Model',
                                'format' => 'raw',
                                'contentOptions' => ['style' => 'vertical-align:middle'],
	                        'value' => function($model) {
	                            return $model['name'] . " " . $model['descrip'];
	                        },						
	                    ],
	                    [
                                'label' => 'Inventory',
                                'format' => 'raw',
                                'contentOptions' => ['style' => 'vertical-align:middle'],
	                        'value' => function($model) {
	                        	$content = "";
	                        	$sum = 0;
                                        if($model['assembly'] == 1) {
                                            if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                                                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                                if(!count($customers) ){
                                                    $customers = array(-1);
                                                }
                                                $number_items = ModelAssembly::find()
                                                    ->innerJoin('lv_partnumbers', '`lv_partnumbers`.`id` = `lv_model_assemblies`.`partid`')
                                                    ->where(['modelid'=>$model->id, 'customer' => $customers])
                                                    ->sum('quantity');
                                            } else {
                                                $number_items = ModelAssembly::find()->where(['modelid'=>$model->id])->sum('quantity');
                                            }
//									$items = ModelAssembly::find()->where(['modelid'=>$model->id])->all();
                                                $nbr_items_in_stock = Item::find()
                                                    ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                                                    ->where(['modelid'=>$model->id])
                                                    ->andwhere(['status'  => array_search('In Stock', Item::$status)]);
//                                                                ->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')
                                                    if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                                                        $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                                        if(!count($customers) ){
                                                            $customers = array(-1);
                                                        }
                                                        $nbr_items_in_stock->andWhere(['customer' => $customers]);
                                                    }
                                                    $nbr_items_in_stock->count();
                                                $sum = ($number_items!=0) ? $nbr_items_in_stock / $number_items : 0;						
                                        }
                                        else 
                                        {
                                                $sum_in_stock = $model['instock_qty'];
//                                                $sum_in_progress = $model['inprogress_qty'];
//                                                $sum_readytoship = $model['readytoship_qty'];
                                                $sum = $sum_in_stock;							
                                        }
	                            return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-content = "" data-animation="true" data-trigger="focus" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorydata?id='.$model['id'].'" data-original-title="' . $model['name'] . ' ' . $model['descrip'] . '"> ' . $sum . ' </a>';
	                        },
	                    ],
	                    [
		                    'attribute' => 'assembly',
		                    'label' => 'Assembly',
		                    'format' => 'raw',
                                    'contentOptions' => ['style' => 'vertical-align:middle'],
                                    'visible' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER),
		                    'value' => function($model) {
		                    	$output = ($model['assembly']) ? 'Yes' : 'No';
		                    	return $output;
		                    },
	                    ],	                  
	                    [
	                        'class' => 'yii\grid\ActionColumn',
	                        'template'=>$_templatesaccess,
	                        'contentOptions' => ['style' => $_defaultwidth],
                                'visible' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER),
	                        'controller' => 'inventory',
	                        'buttons' => [
								'reorder' => function ($url, $model, $key) {
										return Html::a('<span class="glyphicon glyphicon-new-window"></span>', 'javascript://', ['title' => Yii::t('app', 'ReOrder'),
	                                    'class' => 'btn btn-primary btn-sm showreorder', 'data-toggle'=>'modal', 'data-target'=>'#reorder', 'id'=>$model['id'] . '||' . $model['name'] . ' ' . $model['descrip'] . '||' . ($model['nb_models']-$model['instock_qty'])]);
								},
	                            'view' => function ($url, $model, $key) {
	                                $options = [
	                                    'title' => 'View',
	                                    'class' => 'btn btn-info btn-sm',
	                                ];
	                                $url = \yii\helpers\Url::toRoute(['/inventory/view', 'id'=>$model['id']]);
	
	                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
	                            },
	                            'update' => function ($url, $model, $key) {
	                                $options = [
	                                    'title' => 'Update',
	                                    'class' => 'btn btn-warning btn-sm',
	                                ];
	                                $url = \yii\helpers\Url::toRoute(['/inventory/update', 'id'=>$model['id']]);
	
	                                return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, $options);
	                            },
                                    'delete' => function ($url, $model, $key) {
                                            $options = [
                                                    'title' => 'Delete',
                                                    'class' => 'btn btn-danger btn-sm',
                                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                                                    'id' => 'soft_delete_inventory'
                                                    //'data-method' => 'post'
                                            ];
                                            $url = \yii\helpers\Url::toRoute(['/inventory/softdelete', 'id'=>$model['id']]);

                                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                                    },
                                    'merge' => function ($url, $model, $key) {
                                            $options = [
                                                    'title' => 'Merge Inventory',
                                                    'class' => 'btn btn-warning btn-sm',
                                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                                                    //'data-method' => 'post'
                                            ];
                                            $url = \yii\helpers\Url::toRoute(['/inventory/merge', 'id'=>$model['id']]);
//                                            $url = 'javascript:void(0);';
                                            return Html::a('<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>', $url, $options);
                                    },
                                    'transfer' => function ($url, $model, $key) {
                                        if($model['assembly'] == 1) {
                                            if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                                                $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                                if(!count($customers) ){
                                                    $customers = array(-1);
                                                }
                                                $number_items = ModelAssembly::find()
                                                    ->innerJoin('lv_partnumbers', '`lv_partnumbers`.`id` = `lv_model_assemblies`.`partid`')
                                                    ->where(['modelid'=>$model->id, 'customer' => $customers])
                                                    ->sum('quantity');
                                            } else {
                                                $number_items = ModelAssembly::find()->where(['modelid'=>$model->id])->sum('quantity');
                                            }
//									$items = ModelAssembly::find()->where(['modelid'=>$model->id])->all();
                                                $nbr_items_in_stock = Item::find()
                                                    ->innerJoin('lv_model_assemblies', '`lv_model_assemblies`.`partid` = `lv_items`.`model`')
                                                    ->where(['modelid'=>$model->id])
                                                    ->andwhere(['status'  => array_search('In Stock', Item::$status)]);
//                                                                ->andwhere('status IN ('.array_search('In Stock', Item::$status).', '.array_search('Ready to ship', Item::$status).')')
                                                    if(Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER){
                                                        $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');
                                                        if(!count($customers) ){
                                                            $customers = array(-1);
                                                        }
                                                        $nbr_items_in_stock->andWhere(['customer' => $customers]);
                                                    }
                                                    $nbr_items_in_stock->count();
                                                $sum = ($number_items!=0) ? $nbr_items_in_stock / $number_items : 0;						
                                        }
                                        else 
                                        {
                                                $sum_in_stock = $model['instock_qty'];
//                                                $sum_in_progress = $model['inprogress_qty'];
//                                                $sum_readytoship = $model['readytoship_qty'];
                                                $sum = $sum_in_stock;							
                                        }
                                            $options = [
                                                    'title' => 'Transfer Inventory',
                                                    'class' => 'btn btn-info btn-sm',
                                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                                                    'id' => 'transfer_inventory'
                                                    //'data-method' => 'post'
                                            ];
                                             if(!$sum){
                                                 $options['disabled'] = 'disabled';
                                             }
                                            $url = \yii\helpers\Url::toRoute(['/inventory/transfer', 'id'=>$model['id']]);
                                            return Html::a('<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>', $url, $options);
                                    },
	                        ],
	                    ],
	                ],
	            ]); ?>