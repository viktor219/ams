<?php 
use yii\helpers\Url;
use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Item;
	use app\models\Models;
	use app\models\Vendor;
	use app\models\Manufacturer;	
?> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    	'summary'=>'',
        'columns' => [
            'number_generated',
			[
            	'attribute'=>'vendor_id',
				'label'=>'Vendor',
				'value' => function($model) {
					return Vendor::findOne($model->vendor_id)->vendorname;
				}
			],
			[
    			'label'=>'Qty',
				'format' => 'raw',
				'value' => function($model) {
					$number_items = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id])->count();
					return ($number_items != 0) ? '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getpurchaseindexdetails?idpurchase='.$model->id.'" role="button" data-toggle="popover" data-html="true" data-placement="left" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>' : '<span color="red">Exhausted</span>';
				}
			],
			[
				'attribute'=>'estimated_time',
				'value' => function($model) {
					if($model->estimated_time){
						return date('m/d/Y', strtotime($model->estimated_time));
					}
				}
			],
			'trackingnumber',
			[
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{receive} {viewpdf} {sendmail} {update} {delete}',
				'contentOptions' => ['style' => 'width:280px;'],
				'controller' => 'orders',
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
					'sendmail' => function ($url, $model, $key) {//1=>order, 2=>quoteorder, 3=>purchasing order
						$options = [
							'title' => 'Send Email',
							'class' => 'btn btn-primary',
							'onClick' => 'openMailer('. $model->id .', 2)'
						];
					$url = 'javascript:;';
					
					return Html::a('<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>', $url, $options);
					},
					'viewpdf' => function ($url, $model, $key) {
						$options = [
							'title' => 'View PDF',
							'class' => 'btn btn-primary',
							'target' => '_blank'
						];
						$url = \yii\helpers\Url::toRoute(['/purchasing/generate', 'id'=>$model->id]);
					
						return Html::a('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', $url, $options);
					},																					
					'update' => function ($url, $model, $key) {
						$options = [
							'title' => 'Edit',
							'class' => 'btn btn-warning',
							'type'=>'button'
						];
						//$url = Url::toRoute(['/purchasing/update', 'id'=>$model->id]);
						$url = 'javascript:;';
					
						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
					},
					'delete' => function ($url, $model, $key) {
						$options = [
							'title' => 'Delete',
							'class' => 'btn btn-danger deleteOrder',
							'data-content'=>'Delete Order',
							'type'=>'button',
							//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
						];
						$url = Url::toRoute(['/purchase/sdelete', 'id'=>$model->id]);
						//$url = 'javascript:;';
					
						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
					}
				],
			]
        ],
    ]); ?>