<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'In Progress';
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/inprogress.js"></script>
<script>
	loadOrders("all", "", <?=$customer?>);
</script>
<style>
<!--

-->
ul.bar_tabs > li a {
    padding: 10px 17px;
    background: #1ABB9C;
    margin: 0;
    border-radius: 0;
    color: #FFF;
    font-weight: bolder;
    font-size: 13px;
    border-radius: 5px 5px 0 0;
}
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, #myTab a:hover {
	color:#333;
} 
</style>
<?= $this->render("@app/modules/Orders/views/default/_modals/_customerdetails");?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_sendmail");?>
<?php //LOAD Delete Confirmation Popup --->?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_deleteconfirm");?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_quoteconvertconfirm");?>
<div class="order-index">
<!-- Sales Order Dashboard -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-7 vcenter">
					<h4>
						<span class="glyphicon glyphicon-cog"></span>
						<?= Html::encode($this->title) ?> 
					</h4>
				</div>
				<div class="col-md-5 vcenter text-right">
					    <?php $form = ActiveForm::begin([
						        'action' => ['index'],
						        'method' => 'get',
					    		//'options' => ['onkeypress'=>"return event.keyCode != 13;"]
						    ]); ?>
						<div id="searchorder-group" class="pull-right top_search">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-success" id="searchOrderBtn" type="button"><b style="color:#FFF;">?</b></button> 
								</span>
								<input type="search" placeholder="Search" id="searchOrder" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
								<span class="input-group-btn">
                                        <a href="<?= Url::to(['/itemlog/index']) ?>" class="btn btn-success" style="margin-left: 5px;border-radius:4px;">
                                            <span class="glyphicon glyphicon-eye-open"></span> Item Logger
                                        </a>
								</span>
							</div>
						</div>
					<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12">  
                 <div class="x_panel">
                                <div class="x_content">
                                <!-- main gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="order-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#orderhome" onClick="loadOrders('all', '', <?=$customer;?>);" id="order-tab-1" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <?php if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER) :?>
												<li role="presentation" class=""><a href="#orderpurchase" onClick="loadOrders('purchase', '', <?=$customer;?>);" role="tab" id="order-tab-2" data-toggle="tab"  aria-expanded="false">Purchase</a>
												</li>
												<li role="presentation" class=""><a href="#orderservice" onClick="loadOrders('service', '', <?=$customer;?>);" role="tab" id="order-tab-3" data-toggle="tab" aria-expanded="false">Service</a>
												</li>
												<li role="presentation" class=""><a href="#orderintegration" onClick="loadOrders('integration', '', <?=$customer;?>);" role="tab" id="order-tab-4" data-toggle="tab" aria-expanded="false">Integration</a>
												</li> 
												<li role="presentation" class=""><a href="#orderwarehouse" onClick="loadOrders('warehouse', '', <?=$customer;?>);" role="tab" id="order-tab-5" data-toggle="tab" aria-expanded="false">Warehouse</a>
												</li>      
	                                            <li role="presentation" class="left" id="order-deleted-tab"><a href="#orderdelete" onClick="deleteOrders('',<?=$customer;?>);" id="order-tab-6" role="tab" data-toggle="tab" aria-expanded="true">Deleted (<span class="delete_count"><?=$basket_count;?></span>)</a>
	                                            </li>											
											<?php else :?>
												<li role="presentation" class=""><a href="#orderservice" onClick="loadOrders('service', '', <?=$customer;?>);" role="tab" id="order-tab-3" data-toggle="tab" aria-expanded="false">Service</a>
												</li>
												<li role="presentation" class=""><a href="#orderwarehouse" onClick="loadOrders('warehouse', '', <?=$customer;?>);" role="tab" id="order-tab-5" data-toggle="tab" aria-expanded="false">Advance Exchange</a>
												</li>  
												<li role="presentation" class=""><a href="#ordercompleted" onClick="loadOrders('rcompleted', '', <?=$customer;?>);" role="tab" id="order-tab-5" data-toggle="tab" aria-expanded="false">Completed</a>
												</li>																								
											<?php endif;?>										
                                        </ul>
                         				<!--<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div> -->            
                                        <div id="myTabContent" class="tab-content">                                           
                                        	<div role="tabpanel" class="tab-pane fade active in" id="orderhome" aria-labelledby="order-home-tab">                 
												<div id="order-loaded-content-all"></div>
											</div>
                                        	<div role="tabpanel" class="tab-pane fade in" id="orderpurchase" aria-labelledby="order-purchase-tab">                 
												<div id="order-loaded-content-purchase"></div>
											</div>
                                        	<div role="tabpanel" class="tab-pane fade in" id="orderservice" aria-labelledby="order-service-tab">                 
												<div id="order-loaded-content-service"></div>
											</div>
                                        	<div role="tabpanel" class="tab-pane fade in" id="orderintegration" aria-labelledby="order-integration-tab">                 
												<div id="order-loaded-content-integration"></div>
											</div>
                                        	<div role="tabpanel" class="tab-pane fade in" id="orderwarehouse" aria-labelledby="order-warehouse-tab">                 
												<div id="order-loaded-content-warehouse"></div>
											</div>	
                                        	<div role="tabpanel" class="tab-pane fade in" id="ordercompleted" aria-labelledby="order-completed-tab">                 
												<div id="order-loaded-content-rcompleted"></div>
											</div>																						
                                        	<div role="tabpanel" class="tab-pane fade in" id="orderdelete" aria-labelledby="order-backet-tab">        
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Orders Deleted (<span class="orders_delete_count"></span>)</h2>
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
						                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">                                        	         
																<div id="order-deleted-content"></div>
															</div>
														</div>
													</div>
												</div>									
											</div>																																																			
										</div>
									</div>
									<!-- search gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="order-search-gridview" style="display:none;">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#ordersearch" id="order-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="order-results-count"><b>0</b></span>) </a>
                                            </li>                                                              
                                        </ul>
                         				<!--<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>     -->          
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="ordersearch" aria-labelledby="home-tab">                 
												<div id="order-loaded-content-search"></div>
											</div>																																									
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>
    </div>
</div>
<!-- End -->