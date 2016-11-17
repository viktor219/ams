<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Models;
use app\models\Customer;

use app\models\Item;
use app\models\Itemlog;
use app\models\User;
use app\models\Medias;

$page = 'Overview';

$this->title = Yii::$app->name . ' - ' . $page;

?>
<style>
	.count_top
	{
		text-align:center;
	}
	.col-sm-10.col-sm-offset-2.col-md-10.col-md-offset-2.main {
		margin-top: -502px;
	}
</style>
<div class="row">
    <?= Yii::$app->session->getFlash('error'); ?>
    <?= Yii::$app->session->getFlash('success'); ?>
</div> 
<?php if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE) :?>
	<!-- top tiles -->
	<div class="row tile_count" id="loaded-overview-inventory-stats">
	<?php foreach($inventories as $inventory) :?>
		<div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count" style="text-align:center;border-right: 1px solid #BBB;">
			<div class="" style="min-height: 100px">
				<div class="count_top" style="min-height: 32px">
					<?php 
                                        
//						$date = date('Y-m-d');
//						$nbDay = date('N', strtotime($date));
//						//$monday = new DateTime($date);
//						$sunday = new DateTime($date);
//						//$monday->modify('-'.($nbDay-1).' days');
//						$sunday->modify('+'.(7-$nbDay).' days');
//						$lastweekday = $sunday->format('Y-m-d H:i:s');					
//						//
//						$count = Item::find()
//									->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory->customer])
//									->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory->customer])
//									->count();	
//						//
//						$lastweekcount = Item::find()
//									->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory->customer])
//									->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory->customer])
//									->andWhere("(DATE(lastupdated) = date_sub(date('$lastweekday'), INTERVAL 1 week))")
//									->count();						
//						$percentage = (1 - $lastweekcount / $count) * 100; 
//                                                $thisWeekMonday = date('Y-m-d', strtotime('Monday this week'));
//                                                $thisWeekMonday = '2015-09-10';
//						$count = Item::find()
//                                                            ->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory['customer']])
//                                                            ->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory['customer']])
//                                                            ->count();	
//						//
//						$lastweekcount = Item::find()->where("DATE_FORMAT(lastupdated,'%Y-%m-%d') < '".$thisWeekMonday."'")
//                                                                    ->andWhere("status IN (".array_search('In Stock', Item::$status).",". array_search('Ready to ship', Item::$status).")")
//                                                                    ->andWhere(['customer'=>$inventory->customer])
////									->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory->customer])
////									->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory->customer])
////									->andWhere("(DATE(lastupdated) = date_sub(date('$lastweekday'), INTERVAL 1 week))")
//                                                                    ->count();
//                                                $percentage = (($lastweekcount - $count) / $lastweekcount) * 100;
                        $percentage = ceil($inventory['percent'] * 100);               
						$_output = "";
						$customer = Customer::findOne($inventory['customer']);
						$_my_media = Medias::findOne($customer->picture_id);
						if(!empty($_my_media->filename)){
							$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
							if (file_exists(dirname(__FILE__) . '/../../public/images/customers/'.$_my_media->filename)) 
								$_output = Html::img($target_file, ['alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:100px;max-height:32px;']);						 
							else
								$_output = $customer->companyname;					
						}else 
							$_output = $customer->companyname;
					?> 
					<?= Html::a($_output, ['/customers/ownstockpage', 'id'=>$customer->id]) ?>
				</div>
				<div class="count"><?php echo number_format($inventory['count']);?></div>
				<span class="count_bottom"><i class="<?php echo ($percentage > 0)?'green':'red'; ?>"><?php if($percentage > 0):?><i class="fa fa-sort-asc"></i><?php else :?><i class="fa fa-sort-desc"></i><?php endif;?><?php echo abs($percentage);?>% </i> From last Week</span>
			</div>
		</div>
	<?php endforeach;?>
	</div>
	<div class="text-right"><a href="javascript:;" class="btn btn-info btn-xs" data-href="<?php echo Yii::$app->request->baseUrl;?>/customers/loadinventorystats?page=2" id="inv-stat-load-more-button">View More</a></div>
	<br/>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="dashboard_graph">
				<div class="row x_title">
					<div class="col-md-6">
						<h3>Shipping & Receiving <small>Items Incoming & Outgoing</small></h3>
					</div>
					<div class="col-md-6">
						<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
							<span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
						</div>
					</div>
				</div>

				<div class="col-md-9 col-sm-9 col-xs-12">
					<div id="placeholder33" style="height: 260px; display: none" class="demo-placeholder"></div>
					<div style="width: 100%;">
						<div id="canvas_dahs" class="demo-placeholder" style="width: 100%; height:270px;"></div>
					</div>
				</div>
				
				<div class="col-md-3 col-sm-3 col-xs-12 bg-white">
					<div class="x_title">
						<h2>Top Shipment Activity</h2>
						<div class="clearfix"></div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-6" id="loaded-shipments-classments">
						<?php //echo count($shipments);?>
						<?php foreach($shipments as $shipment) :?>
                                                <?php 
                                                $percent = ($shipment['nb_customer_shipments'] / $total_shipments[0]) * 100;
                                                $percent = number_format($percent, 2);
                                                ?>
							<div class="animated flipInX">
								<p><?php echo $shipment['companyname'];?> (<b><?php echo $shipment['nb_customer_shipments'];?></b> Shipments)</p>
								<div class="">
									<div class="progress progress_sm" style="width: 76%;">
										<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $percent; ?>"></div>
									</div>
								</div>
							</div>
						<?php endforeach;?>
					</div>
				</div>
				<div class="text-right"><a href="javascript:;" class="btn btn-info btn-xs" data-href="<?php echo Yii::$app->request->baseUrl;?>/customers/loadshipmentsclassments?page=2" id="shipment-classment-load-more-button">View More</a></div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<br />
	<div class="row">
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_panel tile">
				<div class="x_title">
					<h2>Awaiting Distribution</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Settings 1</a>
								</li>
								<li><a href="#">Settings 2</a>
								</li>
							</ul>
						</li>
						<li><a class="close-link"><i class="fa fa-close"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content" id="awaiting-distribution">
				<?= $this->render('@app/views/site/_awaitingdistribution', ['dataProvider' => $dataProvider]) ?>			
				</div>
			</div>
		</div>

	<!-- Awaiting delivery to lab -->
				<div class="col-md-4 col-sm-4 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Awaiting Delivery To Lab</h2>
							<ul class="nav navbar-right panel_toolbox">
								<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
								</li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Settings 1</a>
										</li>
										<li><a href="#">Settings 2</a>
										</li>
									</ul>
								</li>
								<li><a class="close-link"><i class="fa fa-close"></i></a>
								</li>
							</ul>
							<div class="clearfix"></div>
						</div> 
						<div class="x_content">
							<div class="" id="load-awaiting-delivery-lab-content"></div>
						</div>
					</div>
				</div>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Recent Activities <small>Sessions</small></h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Settings 1</a>
								</li>
								<li><a href="#">Settings 2</a>
								</li>
							</ul>
						</li>
						<li><a class="close-link"><i class="fa fa-close"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div class="dashboard-widget-content">

						<ul class="list-unstyled timeline widget">
                                                    <?php $i = 0;?>
                                                    <?php foreach($recentActivities as $key => $recentActivity): ?>
                                                    <li>
								<div class="block">
									<div class="block_content">
										<h2 class="title">
                                                                                    <?php $recentActivity['activity_type'] = ($recentActivity['activity_type'] == 'itemlog')?"model":$recentActivity['activity_type']; ?>
                                                                                    <?php if($recentActivity['activity_type']=='shipment'): ?>
                                                                                        <a><?php echo $recentActivity['name']; ?></a>
                                                                                    <?php else: ?>
                                                                                        <a><?php echo ucfirst($recentActivity['activity_type']); ?> <?php echo ($recentActivity['type'] == 'created')?"Creation":"Updatation"; ?></a>
                                                                                    <?php endif; ?>
                                                                                </h2>
										<div class="byline">
											<span><?php echo $this->context->time_ago($recentActivity['dateupdated']); ?></span> by <a><?php echo $recentActivity['customer_name']; ?></a>
										</div>
                                                                            <?php if(($recentActivity['activity_type']!='itemlog' || $recentActivity['activity_type']!='model') && $recentActivity['type']=='updated'):?>
                                                                            <p class="excerpt">
                                                                                <?php echo ucwords($recentActivity['customer_name']); ?> Started on <?php echo $recentActivity['name']; ?> for <?php echo $recentActivity['project_name'];?>.
										</p>
                                                                            <?php else: ?>
                                                                            <p class="excerpt">
                                                                                <?php echo ucwords($recentActivity['customer_name']); ?> <?php echo $recentActivity['type']; ?>  a <?php echo ($recentActivity['type'] == 'created')?"new":""; ?> <?php echo $recentActivity['activity_type'];?>.
										</p>
                                                                            <?php endif; ?>
									</div>
								</div>
							</li>
                                                        <?php 
                                                        if($i==3):
                                                            break;
                                                        endif;
                                                        $i++;
                                                        ?>
                                                    <?php endforeach; ?>
<!--							<li>
								<div class="block">
									<div class="block_content">
										<h2 class="title">
								<a>Model Creation</a>
							</h2>
										<div class="byline">
											<span>13 hours ago</span> by <a>Matt E.</a>
										</div>
										<p class="excerpt">Matt E. Created a new model.</a>
										</p>
									</div>
								</div>
							</li>-->
<!--							<li>
								<div class="block">
									<div class="block_content">
										<h2 class="title">
								<a>Equipment Repair</a>
							</h2>
										<div class="byline">
											<span>13 hours ago</span> by <a>Paul S.</a>
										</div>
										<p class="excerpt">Paul S. Started on IBM 4900-783 for Ahold</a>
										</p>
									</div>
								</div>
							</li>
							<li>
								<div class="block">
									<div class="block_content">
										<h2 class="title">
								<a>Equipment Repair</a>
							</h2>
										<div class="byline">
											<span>13 hours ago</span> by <a>Will K.</a>
										</div>
										<p class="excerpt">Will K. Finished all 35 IBM 4610-2CRs for POS Surplus</a>
										</p>
									</div>
								</div>
							</li>-->
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-md-8 col-sm-8 col-xs-12">
					<div class="x_panel">
						<div class="x_title">
							<h2>Visitors location <small>geo-presentation</small></h2>
							<ul class="nav navbar-right panel_toolbox">
								<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
								</li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Settings 1</a>
										</li>
										<li><a href="#">Settings 2</a>
										</li>
									</ul>
								</li>
								<li><a class="close-link"><i class="fa fa-close"></i></a>
								</li>
							</ul>
							<div class="clearfix"></div>
						</div>
						<div class="x_content">
							<div class="dashboard-widget-content">
								<div class="col-md-4 hidden-small">
									<h2 class="line_30">125.7k Views from 60 countries</h2>

									<table class="countries_list">
										<tbody>
											<tr>
												<td>United States</td>
												<td class="fs15 fw700 text-right">33%</td>
											</tr>
											<tr>
												<td>France</td>
												<td class="fs15 fw700 text-right">27%</td>
											</tr>
											<tr>
												<td>Germany</td>
												<td class="fs15 fw700 text-right">16%</td>
											</tr>
											<tr>
												<td>Spain</td>
												<td class="fs15 fw700 text-right">11%</td>
											</tr>
											<tr>
												<td>Britain</td>
												<td class="fs15 fw700 text-right">10%</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div id="world-map-gdp" class="col-md-8 col-sm-12 col-xs-12" style="height:230px;"></div>
							</div>
						</div>
					</div>
				</div>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_panel tile fixed_height_320 overflow_hidden">
				<div class="x_title">
					<h2>Device Usage</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#">Settings 1</a>
								</li>
								<li><a href="#">Settings 2</a>
								</li>
							</ul>
						</li>
						<li><a class="close-link"><i class="fa fa-close"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">

					<table class="" style="width:100%">
						<tr>
							<th style="width:37%;">
								<p>Top 5</p>
							</th>
							<th>
								<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
									<p class="">Device</p>
								</div>
								<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
									<p class="">Progress</p>
								</div>
							</th>
						</tr>
						<tr>
							<td>
								<canvas id="canvas1" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
							</td>
							<td>
								<table class="tile_info">
									<tr>
										<td>
											<p><i class="fa fa-square blue"></i>IOS </p>
										</td>
										<td>30%</td>
									</tr>
									<tr>
										<td>
											<p><i class="fa fa-square green"></i>Android </p>
										</td>
										<td>10%</td>
									</tr>
									<tr>
										<td>
											<p><i class="fa fa-square purple"></i>Blackberry </p>
										</td>
										<td>20%</td>
									</tr>
									<tr>
										<td>
											<p><i class="fa fa-square aero"></i>Symbian </p>
										</td>
										<td>15%</td>
									</tr>
									<tr>
										<td>
											<p><i class="fa fa-square red"></i>Others </p>
										</td>
										<td>30%</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
			</div>
		</div>
	</div>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/overview.js"></script>
<?php endif;?>