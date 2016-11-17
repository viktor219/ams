<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	use app\modules\Orders\models\Order;
	use app\models\Customer;
	use app\models\Medias;
	use app\models\Item;
	use app\models\Itemsordered;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Ordertype;
	use yii\grid\GridView;
?>
<style>
	.popover{
	    max-width: 100%; /* Max Width of the popover (depending on the container!) */
	}
</style>
<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	'summary'=>'',
	'columns' => [
		[
			'attribute' => 'customer',
			'label' => Yii::t('app', 'Customer'),
			'contentOptions' => ['style' => 'width:200px;'],
			'format' => 'raw',
			'value' => function($model) {
				$customer = Customer::findOne($model->customer);
				
				$_my_media = Medias::findOne($customer->picture_id);
				 
				if(!empty($_my_media->filename)){
					$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
					if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) {
						 
						return  Html::img($target_file, ['alt'=>$customer->companyname, 'class'=>'viewCustomer', 'style'=>'cursor:pointer;max-width:90px;max-height:35px;', 'cid'=>$customer->id]);
			
					}else{
						return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
					}
					 
				}else {
					 
					return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
				}
				 
			}
		],
		[
			'attribute' => 'ordernumber',
			'label' => 'SO#',
			'format' => 'raw',
			'value' => function($model) {
				return '<div style="line-height:40px;">' . Order::findOne($model->ordernumber)->number_generated . '</div>';
			}
		],
		[
		'attribute' => 'ordernumber',
		'label' => 'PO#',
		'format' => 'raw',
		'value' => function($model) {
			$_order = Order::findOne($model->ordernumber);
			$_content = (!empty($_order->customer_po)) ? $_order->customer_po : "-";
			return '<div style="line-height:40px;">' . $_content . '</div>';
		}
		],		
		[
		'label' => 'Type',
		'format' => 'raw',
		'value' => function($model) {
			$_order = Order::findOne($model->ordernumber);
			$_type = Ordertype::findOne($_order->ordertype);
			$_content = (!empty($_type)) ? $_type->name : "-";
			return '<div style="line-height:40px;">' . $_content . '</div>';
		}
		],																																				
		[
			'attribute' => 'model',  
			'label' => 'Description',
			'format' => 'raw',
			'value' => function($model) {
					$_model = Models::findOne($model->model);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
					//$qty = Item::find()->where(['ordernumber'=>null, 'status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
					return '<div style="line-height:40px;">' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
			}
		],  
		[
			'header' => 'Qty',
			'format' => 'raw',
			'value' => function($model) {
				$highstatus = Item::find()->where(['ordernumber'=>$model->ordernumber])->orderBy('status DESC')->one()->status;
				$sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
				$completeItems = Yii::$app->db->createCommand($sql)
				->bindValue(':customer', $model->ordernumber)
				->bindValue(':status', $highstatus)
				->queryAll();
				$completepercentage = (float)$completeItems[0]['completePercent'];
				//							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
				//round percentages 
				if((float) $completepercentage !== floor($completepercentage))
					$completepercentage = round($completepercentage);
				$output =  '<a tabindex="0" class="btn btn-default popup-marker btn-status" id="order-status-popover_' . $model->ordernumber . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexstatus?idorder='.$model->ordernumber.'" data-content="" role="button" data-placement="left" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="'. $completepercentage . '% '.Item::$status[$highstatus].'" rel="popover" style="color:#08c;">'. $completepercentage . '% '.Item::$status[$highstatus].'</a>';
				return '<div style="line-height:40px;">' . $output . '</div>';
			}
		],          
		[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{receive}',
			'controller' => 'orders',
			'buttons' => [
			'receive' => function ($url, $model, $key) {
				$options = [
				'title' => 'Receive',
				'class' => 'btn btn-info',
				'type'=>'button',
				'pid'=>$model->id,
				'onClick'=>'ViewOrderDetails("'.$model->ordernumber.'");'
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
				//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
				];
				$url = "javascript:;";
					
				return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
			}
			],
		]								
	],
]); ?> 