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
    use yii\helpers\ArrayHelper;
    use app\models\UserHasCustomer;

?>
<style>
	.popover{
	    max-width: 100%; /* Max Width of the popover (depending on the container!) */
	}
</style>
	<?= GridView::widget([
	                'dataProvider' => $dataProvider,
	                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
					'emptyText' => 'There are currently no items in your cart. Please add(+) an item below.',
	                'summary'=>'',
	                'columns' => [
	                    [
	                        'attribute' => 'imagepath',  
                                'contentOptions' => ['style' => 'vertical-align:middle'],
                                'label' => 'Thumbnail',
                                'format' => 'raw',
                                'value' => function($model) {
                                	$_model = Models::findOne($model->modelid);
                                	$_media = Medias::findOne($_model->image_id);
                                     if($_media['filename'])
                                         return Html::img(Yii::getAlias('@web').'/public/images/models/'. $_media['filename'], ['alt'=>'logo', 'onClick'=>'ModelsViewer(' . $model['id'] . ');', 'height'=>'33px']);
                                }
	                    ],
	                    [
		                    'attribute' => 'aei',
		                    'label' => 'Part#',
		                    'format'=>'raw',
                                    'contentOptions' => ['style' => 'vertical-align:middle'],
		                    'value' => function($model) {
		                    	$_model = Models::findOne($model->modelid);
		                    	if(!empty($_model['aei']))
			                    	return '<a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="partitem-popover_' . $_model['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $_model['id'] . '" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> '. $_model['aei'] .' </a>';
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
                                	 $_model = Models::findOne($model->modelid);
                                     $category = Category::findOne($_model['category_id']);
                                     return $category['categoryname'];
                                }
                            ],
	                    [
	                        'attribute' => 'modelname',  
                                'label' => 'Model',
                                'format' => 'raw',
                                'contentOptions' => ['style' => 'vertical-align:middle'],
	                        'value' => function($model) {
	                        	$_model = Models::findOne($model->modelid);
	                        	$_manufacturer = Manufacturer::findOne($_model->manufacturer);
	                            return $_manufacturer['name'] . " " . $_model['descrip'];
	                        },						
	                    ],   
	                    [
		                    'label' => 'Quantity', 
		                    'format' => 'raw',
		                    'contentOptions' => ['style' => 'vertical-align:middle'],
		                    'value' => function($model) {
		                    	//$_model = Models::findOne($model->modelid);
		                    	return '<input type="text" placeholder="Quantity" class="form-control modelqty" name="quantity['.$model->modelid.'][0]" value="'.$model->quantity.'" id="item-order-qty_'.$model->modelid.'"/><input type="hidden" name="models[]" value="'.$model->modelid.'"/>';
		                    }, 
	                    ],	                                   
	                    [
	                        'class' => 'yii\grid\ActionColumn',
	                        'template'=>'{delete}',
	                        'contentOptions' => ['style' => 'width: 80px;', 'class' => 'action-buttons'],
	                        'controller' => 'inventory',
	                        'buttons' => [
                                    'delete' => function ($url, $model, $key) {
                                            $options = [
                                                    'title' => 'Delete',
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'id' => 'remove-item-from-order_' . $model->modelid,                                                    
                                            ];
                                            $url = 'javascript:;';

                                            return Html::a('<span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>', $url, $options);
                                    },
	                        ],
	                    ],
	                ],
	            ]); ?>