<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Customer;
	use app\models\Location;
	use app\models\Medias;
	use app\models\Item;
	use app\models\QItemsordered;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Department;
	use app\models\User;
	use app\models\Ordertype;
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
				'emptyText'=>'No orders available',
				'columns' => [
					[
						'attribute' => 'customer_id',
						'label' => 'Customer',
						'format' => 'raw',
						'value' => function($model) {
							$customer = Customer::findOne($model->customer_id);
							$m=$customer->picture_id;
							$picture = Medias::findOne($m);
							$link_picture = Yii::getAlias('@web').'/public/images/customers/'.$picture['filename'];
							if($picture!==null && file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$picture['filename']))
								$_output = Html::img($link_picture, ['alt'=>'logo', 'style'=>'cursor:pointer;max-width: 90px;max-height: 35px;', 'class'=>'showCustomer', 'uid'=>$model->customer_id]);
							else 
								$_output = $customer->companyname;
							return '<div style="line-height:40px;">' . $_output . '</div>';
						}
					],
					[
						'label' => 'Order Number',
						'format' => 'raw',
						'attribute' => 'number_generated',
						'value' => function($model) {
							return '<div style="line-height:40px;">' . $model->number_generated . '</div>';
						}
					],
					[
						'header' => 'Total Quantity',
						'format' => 'raw',
						'value' => function($model) {
							$items = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
							$qty = 0;
							foreach($items as $item)
							{
								$qty += $item->qty;
							}				
							$number_items = $qty;
							$item_status = array(
									'Requested',
									'In Transit',
									'Received',
									'In Stock',
									'Reserved',
									'Picked',
									'In Progress',
									'Ready to Ship',
									'Shipped',
									'Ready to Invoice',
									'Invoiced',
									'Complete'
							);	
							//
							$items = QItemsordered::find()->where(['ordernumber'=>$model->id])->all();
							$content = "";
							$qty = 0;
							foreach($items as $item)
							{
								$qty += $item->qty;
								$_model = Models::findOne($item->model);
								$manufacturer = Manufacturer::findOne($_model->manufacturer);
								$count_model = QItemsordered::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->one()->qty;
								$name = $manufacturer->name . ' ' . $_model->descrip;
								$newline = "($count_model) $name ";
								if($name!=="" && strpos($content, $newline) === false)
									$content .= $newline . "<br/>";
							}
							$output = '<a tabindex="0" class="btn btn-sm btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$model->id.'" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $qty .')" data-content="' . Html::encode($content) . '" style="color:#08c;">' . $qty . '</a>';
							return '<div style="line-height:40px;">' . $output . '</div>';
						}
					],
					[
						'attribute' => 'shipby',
						'format' => 'raw',
						'label' => 'Ship By',
						'value' => function($model) {
							if($model->shipby){
								$shipby = strtotime($model->shipby);
								return '<div style="line-height:40px;">' . date('m/d/Y', $shipby) . '</div>';
							}
						}
					],
					[
						'attribute' => 'created_at',
						'label' => 'Created',
						'format' => 'raw',
						'value' => function($model) {
							if(!empty($model->created_at) && $model->created_at != '0000-00-00 00:00:00')
								$created_at = date('m/d/Y', strtotime($model->created_at));
							else 
								$created_at = '-';
							return '<div style="line-height:40px;">' . $created_at . '</div>';
						}
					],
					(Yii::$app->user->identity->usertype===User::TYPE_ADMIN) ? 
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{revert} {delete}',
						'contentOptions' => ['style' => 'line-height:40px;width: 110px'],
						'controller' => 'orders',
						'buttons' => [
                           'revert' => function ($url, $model, $key) {
								$options = [
									'title' => 'Revert',
									'class' => 'btn btn-sm btn-info qrevertOrder',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/qrevert', 'id'=>$model->id, 'customer' => (Yii::$app->user->identity->usertype===User::REPRESENTATIVE)?Yii::$app->user->id:0]);

								return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
							},
							'delete' => function ($url, $model, $key) {
								$options = [
									'title' => 'Delete',
									'class' => 'btn btn-sm btn-danger qdeleteOrder',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/qdelete', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
							}
						],
					]:[],
				],
			]); ?>