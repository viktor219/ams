<?php

/* @var $this yii\web\View */
use app\models\User;
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
        <div class="text-right" style="margin-bottom: 10px;">
                <a href="javascript:;" class="btn btn-info btn-xs disabled prev-page-inventory" data-href="<?php echo Yii::$app->request->baseUrl;?>/customers/loadinventorystats?page=2" id="inv-stat-load-more-button">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a href="javascript:;" class="btn btn-info btn-xs next-page-inventory" data-href="<?php echo Yii::$app->request->baseUrl;?>/customers/loadinventorystats?page=2" id="inv-stat-load-more-button">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>                
        </div>
        <div class="row tile_count" id="loaded-overview-inventory-stats" style="position: relative">
            <div id="loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
	<?php // foreach($inventories as $inventory) :?>
<!--		<div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count" style="text-align:center;border-right: 1px solid #BBB;">
			<div class="" style="min-height: 100px">
				<div class="count_top" style="min-height: 32px">-->
					<?php 
//                                        $percentage = round($inventory['percent'],2);               
//						$_output = "";
//						$customer = Customer::findOne($inventory['customer']);
//						$_my_media = Medias::findOne($customer->picture_id);
//						if(!empty($_my_media->filename)){
//							$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
//							if (file_exists(dirname(__FILE__) . '/../../public/images/customers/'.$_my_media->filename)) 
//								$_output = Html::img($target_file, ['alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:100px;max-height:32px;']);						 
//							else
//								$_output = $customer->companyname;					
//						}else 
//							$_output = $customer->companyname;
					?> 
					<?php /*Html::a($_output, ['/customers/ownstockpage', 'id'=>$customer->id])*/ ?>
<!--				</div>
				<div class="count"><?php //echo number_format($inventory['count']);?></div>
				<span class="count_bottom">
                                    <?php //$class = ($percentage > 0)?'green':($percentage < 0)?"red":"";?>
                                    <i class="< $class; ?>">
                                        <?php //if($percentage > 0):?>
                                            <i class="fa fa-sort-asc"></i>
                                        <?php //elseif($percentage < 0):?>
                                            <i class="fa fa-sort-desc"></i>
                                                <?php //endif;?>
                                         <?php //if($percentage != 0): ?>
                                             <?php //echo abs($percentage);?>% From last Week
                                        <?php //else: ?>
                                            No Activity This Week
                                        <?php //endif; ?>
                                    </i>
                                </span>-->
<!--			</div>
		</div>-->
	<?php //endforeach;?>
	</div>
<!--	<div class="text-right"><a href="javascript:;" class="btn btn-info btn-xs" data-href="<?php //echo Yii::$app->request->baseUrl;?>/customers/loadinventorystats?page=2" id="inv-stat-load-more-button">View More</a></div>-->
	<br/>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="dashboard_graph">
				<div class="row x_title">
					<div class="col-md-6">
                                            <div class="col-md-6">
                                                <h3>Shipping & Receiving</h3>
                                            </div>
                                            <div class="col-md-6" style="font-size: 11px;">
                                                <div>
                                                    <span class="ship-color"></span> 
                                                    <span style="vertical-align: top;">Shipping</span>
                                                </div>
                                                <div>
                                                    <span class="receive-color"></span> 
                                                    <span style="vertical-align: top;">Receiving</span>
                                                </div>
                                            </div>
					</div>
					<div class="col-md-6">
<!--						<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
							<span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
						</div>-->
					</div>
				</div>

				<div class="col-md-9 col-sm-9 col-xs-12">
					<div id="placeholder33" style="height: 260px; display: none" class="demo-placeholder"></div>
                                        <div id="loading-canvas" style="background : transparent;position:absolute;top:45%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
					<div style="width: 100%;">
						<div id="canvas_dahs" class="demo-placeholder" style="width: 100%; height:270px;"></div>
					</div>
				</div>
				
				<div class="col-md-3 col-sm-3 col-xs-12 bg-white">
					<div class="x_title">
                                            <h2 class="pull-left">Top Shipment Activity</h2>
                                    <div class="pull-right">
                                            <a href="javascript:;" class="btn btn-info btn-xs disabled prev-page-shipment" data-href="" id="shipment-classment-load-more-button">
                                                <span style="color: white" class="glyphicon glyphicon-chevron-left"></span>
                                            </a>
                                            <a href="javascript:;" class="btn btn-info btn-xs next-page-shipment" data-href="<?php echo Yii::$app->request->baseUrl;?>/customers/loadshipmentsclassments?page=2" id="shipment-classment-load-more-button">
                                                <span style="color: white" class="glyphicon glyphicon-chevron-right"></span>
                                            </a>                
                                    </div>                                                
						<div class="clearfix"></div>
					</div>
                                    <div id="loading" style="background : transparent;position:absolute;top:45%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
					<div class="col-md-12 col-sm-12 col-xs-12" id="loaded-shipments-classments">
                                            
						<?php //echo count($shipments);?>
						<?php //foreach($shipments as $shipment) :?>
                                                <?php 
//                                                $percent = ($shipment['nb_customer_shipments'] / $total_shipments[0]) * 100;
  //                                              $percent = number_format($percent, 2);
                                                ?>
<!--							<div class="animated flipInX">
								<p><?php //echo $shipment['companyname'];?> (<b><?php //echo $shipment['nb_customer_shipments'];?></b> Shipments)</p>
								<div class="">
									<div class="progress progress_sm" >
										<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php //echo $percent; ?>"></div>
									</div>
								</div>
							</div>-->
						<?php //endforeach;?>
					</div>
				</div>
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
				<div class="x_content min-height-300" id="awaiting-distribution">
                                    <div id="loading-awaiting-dist" style="background: transparent;position:absolute;top:35%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>    
                                    <div id="awaiting-distribution-content"></div>
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
                                                    <div class="min-height-300" id="load-awaiting-delivery-lab-content">
                                                        <div id="loading-awaiting-delivery" style="background: transparent;position:absolute;top:35%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>    
                                                    </div>
						</div>
					</div>
				</div>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Recent Activity</h2>
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
					<div class="dashboard-widget-content min-height-300">
                                            <div id="loading-timeline" style="background: transparent;position:absolute;top:35%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
						<ul class="list-unstyled timeline widget">
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/overview.js"></script>
<?php endif;?>