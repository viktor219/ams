<?php

use app\modules\Orders\models\Order;
use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Ordertype;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Medias;
use app\models\Item;
use app\models\Itemsordered;
use app\models\Customer;
use app\models\User;
use app\models\Shipping;
use app\models\Shipment;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;

$this->title = Yii::t('app', 'Shipping');
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/shipping.js"></script>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<style>
	.popover{
		max-width: 100%; /* Max Width of the popover (depending on the container!) */
	}
</style>
<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-6 vcenter">
				<h4><span class="glyphicon glyphicon-open"></span> Shipping Overview</h4>
			</div>
			<div class="col-md-6 vcenter text-right">
				<!--	<a href="<?= Url::to(['/receiving/create']) ?>" class="btn btn-success">
						<span class="glyphicon glyphicon-plus"></span> Receive
					</a> -->
			</div>
		</div>
	</div>
	<div class="panel-body" style="padding:0;">
		<div class="">
			<div class="x_panel"style="border:none;">
				<div class="x_content">
					<?php if(Yii::$app->user->identity->usertype!=User::TYPE_TECHNICIAN) :?>
						<div class="row row-margin">
							<div class="x_panel">
								<div class="x_title">
									<h2><i class="fa fa-bars"></i> In Shipping</h2>
									<ul class="nav navbar-right panel_toolbox">
										<li>									
											<div class="seach-area-in-shipping">
												<div style="float:left">
													<button id="search-btn-in-shipping" class="btn btn-success" style="margin-right:0;">
														<b style="color:#FFF;">?</b>
													</button>
												</div>
												<div style="float:right">
													<input type="search" id="search-in-shipping" class="form-control" placeholder="Search" style="font-weight:bold;border: 1px solid #ddd;"/>
												</div>
											</div>
										</li>
										<li>
											<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
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
										<!--<li><a class="close-link"><i class="fa fa-close"></i></a>
										</li>-->
									</ul>
									<div class="clearfix"></div>
								</div>
								<div id="shipping-content-data" class="x_content">
									<div id="in-shipping-search-panel" class="" role="tabpanel" data-example-id="togglable-tabs">	
									</div>
								</div>
							</div>
						</div>
					<?php endif;?>
					<div class="row row-margin">
						<div class="x_panel">
							<div class="x_title">
								<h2><i class="fa fa-bars"></i> Ready To Ship</h2>
								<ul class="nav navbar-right panel_toolbox">
									<li>
										<div class="seach-area-ready-to-ship">
											<div style="float:left">
												<button id="search-btn-ready-to-ship" class="btn btn-success" style="margin-right:0;">
													<b style="color:#FFF;">?</b>
												</button>
											</div>
											<div style="float:right">
												<input type="search" id="search-ready-to-ship" class="form-control" placeholder="Search" style="font-weight:bold;border: 1px solid #ddd;"/>
											</div>
										</div>
									</li>
									<li>
										<a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									</li>
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
											<i class="fa fa-wrench"></i>
										</a>
										<ul class="dropdown-menu" role="menu">
											<li><a href="#">Settings 1</a>
											</li>
											<li><a href="#">Settings 2</a>
											</li>
										</ul>
									</li>
									<!--<li><a class="close-link"><i class="fa fa-close"></i></a>
									</li>-->
								</ul>
								<div class="clearfix"></div>
							</div>
							<div class="x_content" id="readyship-content-data">
								<div id="seach-ready-to-ship-panel" class="" role="tabpanel" data-example-id="togglable-tabs">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    $(function(){
    $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                $('#shipping-content-data table, #readyship-content-data table').stacktable({
                        myClass: 'table table-striped table-bordered'
                });
    });
});
</script>
