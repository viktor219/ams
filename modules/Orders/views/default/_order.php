<?php
	use app\modules\Orders\models\Order;
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	use yii\grid\GridView;
	use app\models\Customer;
	use app\models\Location;
	use app\models\Medias;
	use app\models\Item;
	use app\models\Itemsordered;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Department;
	use app\models\User;
  use app\models\Ordertype;
	use app\models\SalesorderMail;
	use app\models\SalesorderWs;
	use app\models\Shipment;
	use app\models\ShipmentBoxDetail;
	use app\models\LocationParent;
	use app\models\LocationClassment;
	use app\models\Orderlog;
	//
	//$_templatesaccess = '{view}';
	//$_defaultwidth = '100px';

	//if(Yii::$app->user->identity->usertype===User::TYPE_ADMIN || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN)
	//{
		$_templatesaccess = '{viewpicklist} {viewpdf} {sendmail} {update}';
		if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
			$_templatesaccess = '{trackinglink} {returnlabel} {relatedserviceview}';
		$_defaultwidth = '320px';
		if(Yii::$app->user->identity->usertype!=User::REPRESENTATIVE){
			$_templatesaccess .= '{delete}';
			$_defaultwidth = '220px';
		}
		if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
			$_defaultwidth = '135px';

	//}
?>
<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td
{
	padding: 5px;
}
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th
{
    font-size: 14px;
    text-align: center;
}
.btn-status
{
	width: 155px
}
.action-buttons
{
	text-align: right !important;
}
</style>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
				'summary'=>'',
				'emptyText'=>'No orders available',
				'columns' => [
					[
						'attribute' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'customer_id' : '',
						'label' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'Customer' : 'Destination',
						'format' => 'raw',
						'value' => function($model) {
							if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
							{
								$location = Location::findOne($model->location_id);
								if(!empty($location->storenum))
									$_output = $location->storenum;
								else if(!empty($location->storename))
									$_output = $location->storename;
								else
									$_output = Customer::findOne($model->customer_id)->companyname;
								//
								if($_output=='DIV')
								{
									$_location = LocationClassment::find()->where(['location_id'=>$model->location_id])->one();
									$location = Location::findOne($model->location_id);
									$parent = LocationParent::findOne($_location->parent_id);
									$parent_code = $parent->parent_code;
									/*if(!empty($location->parent_name))
									{
										$parent_name = explode('-', $location->parent_name);
										$parent_code = trim($parent_name[0]);
									}*/
									$_output = $parent_code . ' ' . $_output;
								}
							}
							else
							{
								$customer = Customer::findOne($model->customer_id);
								$m=$customer->picture_id;
								$picture = Medias::findOne($m);
								$link_picture = Yii::getAlias('@web').'/public/images/customers/'.$picture['filename'];
								if($picture!==null && file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$picture['filename']))
									$_output = Html::img($link_picture, ['alt'=>'logo', 'style'=>'cursor:pointer;max-width: 90px;max-height: 35px;', 'class'=>'showCustomer', 'uid'=>$model->customer_id]);
								else
									$_output = $customer->companyname;
							}
							return '<div style="line-height:40px;">' . $_output . '</div>';
						}
					],
					[
					'attribute' => 'customer_id',
					'label' => 'Destination',
					'visible' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER),
					'format' => 'raw',
					'value' => function($model) {
						$location = Location::findOne($model->location_id);
						if(!empty($location->storenum))
							$_output = $location->storenum;
						else if(!empty($location->storename))
							$_output = $location->storename;
						else
							$_output = Customer::findOne($model->customer_id)->companyname;
						//
						if($_output=='DIV')
						{
							$_location = LocationClassment::find()->where(['location_id'=>$model->location_id])->one();
							$parent = LocationParent::findOne($_location->parent_id);
							$_output = $parent->parent_code . ' ' . $_output;
						}
						return '<div style="line-height:40px;">' . $_output . '</div>';
					}
					],
					[
						'label' => 'Order',
						'format' => 'raw',
						'contentOptions' => ['style' => 'width:120px;'],
						'attribute' => 'number_generated',
						'value' => function($model) use ($type){
							$output = "";
							if(empty($model->number_generated))
							{
								$location = Location::findOne($model->location_id);
								if(!empty($location->storenum))
									$output .= "Store#: " . $location->storenum;
								//if(!empty($location->storename))
								else
									$output .= $location->storename;
								//
								//$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
							} else
								$output .= $model->number_generated;
							//
							if((Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER) && in_array($type, ["All", "Shipped", "Warehouse"]) && $model->ordertype==4)
							{
								$service_order = Order::findOne(SalesorderWs::find()->where(['warehouse_id'=>$model->id])->one()->service_id);
								$output = $service_order->number_generated;
								if(empty($output))
									$output = $model->number_generated;
							}
							return '<div style="line-height:40px;">' . $output. '</div>';
						}
					],
					[
						'header' => 'Qty',
						'format' => 'raw',
						'value' => function($model) {
							$number_items = Itemsordered::find()->where(['ordernumber'=>$model->id])->sum('qty');
							$output = '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderqtydetails?idorder='.$model->id.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" rel="popover" style="color:#08c;">' . $number_items . '</a>';
							if ((Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER) && $model->ordertype!=2)
								$output = '<a tabindex="0" class="btn btn-sm btn-default" style="color:#08c;">' . $number_items . '</a>';
							return '<div style="line-height:40px;">' . $output . '</div>';
						}
					],
					[
						'attribute' => 'status',
						'format' => 'raw',
						'value' => function($model) use ($excludeStatus){
//							$totalitems = Item::find()
//								->where(['ordernumber'=>$model->id, 'status'=>array_search('Ready to ship', Item::$status)])
//								->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('Picked', Item::$status)])
//								->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('In Progress', Item::$status)])
//								->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('In Transit', Item::$status)])
//								->count();
//							//
//
//							$completeitems = Item::find()
//								->where(['ordernumber'=>$model->id])
//								->andWhere(['status'=>array_search('Complete', Item::$status)])
//								->count();
//							//
                                                        /*$sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status IN (:ready_to_ship, :picked, :in_progress, :in_transit)) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status IN (:complete)) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status IN (:ready_to_ship, :picked, :in_progress, :in_transit)),0) completePercent';
                                                        $completeItems = Yii::$app->db->createCommand($sql)
                                                                ->bindValue(':customer', $model->id)
                                                                ->bindValue(':complete', array_search('Complete', Item::$status))
                                                                ->bindValue(':ready_to_ship', array_search('Ready to ship', Item::$status))
                                                                ->bindValue(':picked', array_search('Picked', Item::$status))
                                                                ->bindValue(':in_progress', array_search('In Progress', Item::$status))
                                                                ->bindValue(':in_transit', array_search('In Transit', Item::$status))
                                                                ->queryAll();*/
							$_itemhighstatus = Item::find()->where(['ordernumber'=>$model->id])->orderBy('status DESC');
							if(!empty($excludeStatus))
								$_itemhighstatus->andWhere(['not', ['status'=>$excludeStatus]]);
							$highstatus = $_itemhighstatus->one()->status;
							$sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
							$completeItems = Yii::$app->db->createCommand($sql)
							->bindValue(':customer', $model->id)
							->bindValue(':status', $highstatus)
							->queryAll();
                            $completepercentage = (float)$completeItems[0]['completePercent'];
//							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
							//round percentages
							if((float) $completepercentage !== floor($completepercentage))
								$completepercentage = round($completepercentage);
							$_currentstatus = Item::$status[$highstatus];
							if($highstatus==array_search('Shipped', Item::$status))
								$_currentstatus = 'Ready For Pick-Up';
							$output =  '<a tabindex="0" class="btn btn-sm btn-default popup-marker btn-status" id="order-status-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexstatus?idorder='.$model->id.'" data-content="" role="button" data-toggle="popover" data-placement="top" data-html="true" data-animation="true" data-trigger="focus" title="'. $completepercentage . '% '.$_currentstatus.'" rel="popover" style="color:#08c;">'. $completepercentage . '% '.$_currentstatus.'</a>';
							return '<div style="line-height:40px;">' . $output . '</div>';
						}
					],
					[
						'attribute' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'shipby' : 'created_at',
						'format' => 'raw',
						'label' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'Ship By' : 'Created On',
						'value' => function($model) {
							if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER)
							{
								if($model->shipby){
									$shipby = date('m/d/Y', strtotime($model->shipby));
									$today = date('m/d/Y');
									$tomorrow = date('m/d/Y', strtotime('+1 day', strtotime($today)));
									$yesterday = date('m/d/Y', strtotime('-1 day', strtotime($today)));
									if($shipby == $today)
										$_output = "<b>Today</b>";
									elseif ($shipby == $tomorrow)
										$_output = "<b>Tomorrow</b>";
									elseif ($shipby == $yesterday)
										$_output = "<b>Yesterday</b>";
									else
									{
										$_output = date("M d g:ia", strtotime($model->shipby));
									}
									return '<div style="line-height:40px;">' . $_output . '</div>';
								}
							}
							else
							{
								if($model->created_at){
									$created_at = strtotime($model->created_at);
									return '<div style="line-height:40px;">' . date("M d g:ia", $created_at) . '</div>';
								}
							}
						}
					],
					[
						'attribute' => 'created_at',
						'label' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'Created' : 'Created By',
						'format' => 'raw',
						'value' => function($model) {
							if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER)
							{
								if(!empty($model->created_at) && $model->created_at != '0000-00-00 00:00:00')
									$output = date('m/d/Y', strtotime($model->created_at));
								else
									$output = '-';
							}
							else
							{
								$_userlogid = Orderlog::find()->where(['orderid'=>$model->id])->one()->userid;
								$_userlog = User::findOne($_userlogid);
								$output = $_userlog->firstname . ' ' . $_userlog->lastname;
							}
							return '<div style="line-height:40px;">' . $output . '</div>';
						}
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'visibleButtons' => [
							'returnlabel' => function ($model, $key, $index) {
								return ($model->ordertype == 2) ? true : false;
							},
							'relatedserviceview' => function ($model, $key, $index) {
								$hasrelatedws = 1;
								if($model->ordertype == 4)
									$hasrelatedws = Order::find()->where(['id'=>$model->id])->innerJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`warehouse_id` = `lv_salesorders`.`id`')->where(['warehouse_id'=>$model->id])->count();
								return ($model->ordertype == 4 && $hasrelatedws) ? true : false;
							},
							'delete' => function ($model, $key, $index) {
								$status = ArrayHelper::getColumn(Item::find()->where(['ordernumber'=>$model->id])->distinct()->all(), 'status');
								return ((Yii::$app->user->identity->usertype==User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER) && !in_array(array_search('Reserved', Item::$status), $status) && !in_array(array_search('Requested', Item::$status), $status)) ? false : true;
							}
						],
						'template'=> $_templatesaccess,
						'contentOptions' => ['style' => "width:$_defaultwidth;line-height: 40px;", 'class' => 'action-buttons customer-action-button'],
						'controller' => 'orders',
						'buttons' => [
							'trackinglink' => function ($url, $model, $key) {
								$options = [
									'title' => 'Tracking Link',
									'class' => 'btn btn-sm btn-info',
									'target' => '_blank',
									'disabled' => true
								];
								//$hasrelatedws = 1;
								//if($model->ordertype == 4)
									//$hasrelatedws = Order::find()->where(['id'=>$model->id])->innerJoin('lv_salesorders_ws', '`lv_salesorders_ws`.`warehouse_id` = `lv_salesorders`.`id`')->where(['warehouse_id'=>$model->id])->count();
								/*if($model->ordertype == 2)
								{
									$_shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
									if(!empty($_shipment))
									{
										$box_detail = ShipmentBoxDetail::find()->where(['shipmentid'=>$_shipment->id])->one();
										if(!empty($box_detail))
										{
											unset($options['disabled']);
											$url = "https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=" . $box_detail->trackingnumber;
										}
									}
								} else if($model->ordertype == 4)
								{*/
									//if(!$hasrelatedws)
										//$options['disabled'] = true;
									$_shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
									if(!empty($_shipment) && !empty($_shipment->master_trackingnumber))
									{
										unset($options['disabled']);
										$url = "https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=" . $_shipment->master_trackingnumber;
									}
								//}
								return Html::a('<span class="glyphicon glyphicon-globe" aria-hidden="true"></span>', $url, $options);
							},
							'relatedserviceview' => function ($url, $model, $key) {
								$options = [
								'title' => 'Service Order Information',
								'class' => 'btn btn-sm btn-default',
								//'onClick' => 'OpenRelatedServiceModal('.$model->id.');',
								'id'=>'open-service-modal_' . $model->id
								];
								$url = 'javascript:;';
								return Html::a('<span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>', $url, $options);
							},
							'returnlabel' => function ($url, $model, $key) {
								$options = [
									'title' => 'Return Label',
									'class' => 'btn btn-sm btn-default',
									//'onClick' => 'OpenReturnLabelModal('.$model->id.');',
									'id' => 'open-return-label-modal_' . $model->id
								];
								$hasrelatedws = 1;
								//$url = \yii\helpers\Url::toRoute(['/orders/labeltest', 'id'=>$model->id]);
								$url = 'javascript:;';

								return Html::a('<span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span>', $url, $options);
							},
							'viewpicklist' => function ($url, $model, $key) {
								$options = [
									'title' => 'View Pick List',
									'class' => 'btn btn-sm btn-default',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/viewpicklist', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
							},
							'sendmail' => function ($url, $model, $key) {//1=>order, 2=>quoteorder, 3=>purchasing order
								$is_mail_already_sent = SalesorderMail::find()->where(['orderid'=>$model->id])->count();

								/*if($is_mail_already_sent)
									$options = [
										'title' => 'Send Email',
										'class' => 'btn btn-sm btn-success',
										'id' => 'mail-button-' . $model->id,
										'data-poload' => Yii::$app->request->baseUrl . '/ajaxrequest/getordermailstatus?idorder='.$model->id,
										'data-content' => '',
										'role' => 'button',
										'data-trigger' => 'focus',
										'data-toggle' => 'popover',
										'data-html' => 'true',
										'data-animation' => 'true',
										'rel' => 'popover',
										'title' => 'Mail Reports',
										'data-placement' => 'left',
										'onClick' => 'openMailer('. $model->id .', 1)'
									];
								else */
									$options = [
										'title' => 'Send Email',
										'class' => (!$is_mail_already_sent) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-success',
										'id' => 's-mail-button-' . $model->id,
										'onClick' => 'openMailer('. $model->id .', 1)'
									];
								$url = 'javascript:;';

								return Html::a('<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>', $url, $options);
							},
							'viewpdf' => function ($url, $model, $key) {
								$options = [
									'title' => 'View PDF',
									'class' => 'btn btn-sm btn-info',
									'target' => '_blank',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/ogenerate', 'id'=>$model->id]);

								return Html::a('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', $url, $options);
							},
							'view' => function ($url, $model, $key) {
								$options = [
									'title' => 'View',
									'class' => 'btn btn-sm btn-info',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/view', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>', $url, $options);
							},
							'update' => function ($url, $model, $key) {
								$options = [
									'title' => 'Edit',
									'class' => 'btn btn-sm btn-warning',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/update', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
							},
							'delete' => function ($url, $model, $key) {
								$options = [
									'title' => 'Delete',
									'class' => 'btn btn-sm btn-danger',
									//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
									'id' => 'soft_delete_order',
									//'data-method' => 'post'
								];
								$url = \yii\helpers\Url::toRoute(['/orders/delete', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
							}
						],
					],
				],
			]); ?>
