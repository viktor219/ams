<?php 
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
	
	
	$_templatesaccess = '{view}';
	//if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
		//$_templatesaccess = '{trackinglink} {returnlabel}';
	//$_defaultwidth = '280px';
	//if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
		$_defaultwidth = '125px';
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
</style>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,				
				'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
				'summary'=>'', 
				'emptyText'=>'No orders available',
				'columns' => [
					[
						'attribute' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'customer_id' : '',
						'label' => (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) ? 'Customer' : 'Store Number',
						'format' => 'raw',
						'value' => function($model) {
							if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE && Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)
							{
								$location = Location::findOne($model->location_id);
								if(!empty($location->storenum))
									$_output = $location->storenum;
								else if(!empty($location->storename))
									$_output = $location->storename;
								else
									$_output = Customer::findOne($model->customer_id)->companyname;								
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
						'label' => 'Order',
						'format' => 'raw',
						'contentOptions' => ['style' => 'width:120px;'],
						'attribute' => 'number_generated',
						'value' => function($model) {
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
							}else
								$output .= $model->number_generated;
							return '<div style="line-height:40px;">' . $output. '</div>';
						}
					],
					//(Yii::$app->user->identity->usertype===User::REPRESENTATIVE) ? []: '',
					[
						'label' => 'Order Type',
						'attribute' => 'ordertype',
                                                'format' => 'raw',
						'visible' => function ($model) {
							if (Yii::$app->user->identity->usertype===User::REPRESENTATIVE && Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER) {
								return true;
							} else {
								return false;
							}
						},
						'value' => function($model) {
							$_ordertype = OrderType::findOne($model->ordertype);
							if($_ordertype !== null)
								$_output = $_ordertype->name;
							else 
								$_output = '-';
							return '<div style="line-height:40px;">' . $_output . '</div>';
						}
					],
					[
						'header' => 'Qty',
						'format' => 'raw',
						'value' => function($model) { 
							$number_items = Itemsordered::find()->where(['ordernumber'=>$model->id])->sum('qty');
							//return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$model->id.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" rel="popover" style="color:#08c;">' . $number_items . '</a>';							
							$output = '<a tabindex="0" class="btn btn-sm btn-default" style="color:#08c;">' . $number_items . '</a>';	
							return '<div style="line-height:40px;">' . $output . '</div>';
						}
					],
					[
						'attribute' => 'status',
						'format' => 'raw',
						'value' => function($model) {	
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
							$highstatus = Item::find()->where(['ordernumber'=>$model->id])->orderBy('status DESC')->one()->status;
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
							$output =  '<a tabindex="0" class="btn btn-sm btn-default popup-marker btn-status" id="order-status-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexstatus?idorder='.$model->id.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="'. $completepercentage . '% '.Item::$status[$highstatus].'" rel="popover" style="color:#08c;">'. $completepercentage . '% '.Item::$status[$highstatus].'</a>';
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
										$_output = $shipby;
									return '<div style="line-height:40px;">' . $_output . '</div>';
								}
							}
							else 
							{
								if($model->created_at){
									$created_at = strtotime($model->created_at);
									return '<div style="line-height:40px;">' . date('m/d/Y', $created_at) . '</div>';
								}								
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
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=> $_templatesaccess,
						'contentOptions' => ['style' => "width:$_defaultwidth;line-height: 40px;", 'class' => 'action-buttons'],
						'controller' => 'orders',
						'buttons' => [							
                            'view' => function ($url, $model, $key) {
                                $options = [
                                    'title' => 'View Details',
                                    'class' => 'btn btn-sm btn-info',
                                ];
                                $url = \yii\helpers\Url::toRoute(['/inprogress/details', 'id'=>$model->id]);

                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                            }
						],
					],
				],
			]); ?>