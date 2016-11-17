<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\grid\GridView;
	use app\models\Itemspurchased;
	use app\models\Users;
	use app\models\Item;
	use app\models\Models;
	use app\models\Manufacturer;	
	use app\modules\Orders\models\Order;
	use app\models\Customer;
	use app\models\Vendor;
	
	$this->title = 'Purchase Orders';
	
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
} 
</style>
<?= $this->render("_modals/_purchasingdetails");?>
<?= $this->render("_modals/_receiveqtymodal");?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_sendmail");?>
<?= $this->render("_modals/_schedule_delivery");?>
<?= \Yii::$app->view->render("@app/views/layouts/_modals/_deleteconfirm", ['page' => 'Item']); ?>
                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-5 vcenter">
                                    <h4>
                                        <span class="glyphicon glyphicon-tags"></span>
                                        Purchase Orders
                                    </h4>
                                </div>
                                <div class="col-md-7 vcenter text-right">
								    <?php $form = ActiveForm::begin([
									        'action' => ['index'],
									        'method' => 'get',
									    ]); ?>
										<div id="searchpurchasing-group" class="pull-right top_search">
											<div class="input-group">
												<span class="input-group-btn">
													<button class="btn btn-success" id="searchPurchasingBtn" type="button"><b style="color:#FFF;">?</b></button> 
												</span>
												<input type="text" placeholder="Search" id="searchPurchasing" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
												<span class="input-group-btn">
													<?= Html::a('<span class="glyphicon glyphicon-user"></span> Vendors', ['/vendor/index'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
													<?= Html::a('<span class="glyphicon glyphicon-plus"></span> Create A Purchase Order', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 1px;border-radius:4px;']) ?>								
												</span>
											</div>						
										</div>
									<?php ActiveForm::end(); ?>                                
                                </div>
                            </div>
                        </div>
                    <div class="panel-body">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="purchasing-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="" style="display:none;" id="purchasing-search-tab"><a href="#purchasingsearch" id="purchasing-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="order-results-count"><b>0</b></span>) </a>
                                            </li> 
                                            <li role="presentation" class="active"><a href="#purchasinghome" onclick="loadMainPurchasing('')" id="purchasing-tab-1" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class=""><a href="#purchasingservice" onclick="loadOrderTypeItems('service', '')" id="purchasing-tab-2" role="tab" data-toggle="tab" aria-expanded="true">Service</a>
                                            </li>                                               
                                            <li role="presentation" class=""><a href="#purchasingintegration" onclick="loadOrderTypeItems('integration', '')" id="purchasing-tab-3" role="tab" data-toggle="tab" aria-expanded="true">Integration</a>
                                            </li>                                            
                                            <li role="presentation" class=""><a href="#purchasingwarehouse" onclick="loadOrderTypeItems('warehouse', '')" id="purchasing-tab-4" role="tab" data-toggle="tab" aria-expanded="true">Warehouse</a>
                                            </li> 
                                            <li role="presentation" class=""><a href="#purchasingdeleted" onclick="deletePurchasing('')" id="purchasing-tab-5" role="tab" data-toggle="tab" aria-expanded="true">Deleted(<span class="total_delete_count"><?= ($deletedIncomingPurchases + $deletedItemsRequested); ?></span>)</a>
                                            </li>                                              
                                        </ul>         
                         				<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>                                                       
                                        <div id="myTabContent" class="tab-content">     
                                        	<div role="tabpanel" class="tab-pane fade in" id="purchasingsearch" aria-labelledby="home-tab">                 
												<div id="purchasing-loaded-content-search"></div>
											</div>	                                        
                                        	<div role="tabpanel" class="tab-pane fade active in" id="purchasinghome" aria-labelledby="purchasinghome-tab">                                            
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
						                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="items-load-content">
						                                    </div>
						                                </div>
						                            </div>
						                        </div>
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Incoming Purchases</h2>
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
															<div class="" role="tabpanel" data-example-id="togglable-tabs" id="incoming-purchase-gridview">
																<ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
																	<li role="presentation" class="active"><a href="#incomingpurchasingall" id="incoming-purchasing-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Actives</a>
																	</li> 
																	<li role="presentation" class=""><a href="#incomingpurchasingexhausted" id="purchasing-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Inactives</a>
																	</li>                                                                                                                                                                                                                         
																</ul>         
																<div id="myTabContent" class="tab-content">     
																	<div role="tabpanel" class="tab-pane fade active in" id="incomingpurchasingall" aria-labelledby="home-tab">                 
																	</div>
																	<div role="tabpanel" class="tab-pane fade in" id="incomingpurchasingexhausted" aria-labelledby="purchasinghome-tab">
																	</div>
																</div>
															</div>
						                                </div>
						                            </div>
						                        </div>    
						                     </div>
							                 <div role="tabpanel" class="tab-pane fade in" id="purchasingintegration" aria-labelledby="purchasingintegration-tab">    
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
						                                    <div id="integration-items-requested-loaded-content"  role="tabpanel" data-example-id="togglable-tabs">
						                                    </div>
						                                </div>
						                            </div>
						                        </div>							                     
							                  </div>
							                     <div role="tabpanel" class="tab-pane fade in" id="purchasingwarehouse" aria-labelledby="purchasingwarehouse-tab">    
							                        <div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
							                                    <div id="warehouse-items-requested-loaded-content" role="tabpanel" data-example-id="togglable-tabs">
							                                    </div>
							                                </div>
							                            </div>
							                        </div>							                     
							                     </div>	
							                     <div role="tabpanel" class="tab-pane fade in" id="purchasingservice" aria-labelledby="purchasingservice-tab">  
							                        <div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
							                                    <div id="service-items-requested-loaded-content"  role="tabpanel" data-example-id="togglable-tabs">                                
							                                    </div>
							                                </div>
							                            </div>
							                        </div>							                     
							                     </div>						                     
                                            <div role="tabpanel" class="tab-pane fade in" id="purchasingdeleted" aria-labelledby="deleting-tab">                 
												<div role="tabpanel" class="tab-pane fade in" id="orderdelete" aria-labelledby="order-backet-tab">        
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Items Requested Deleted (<span class="items_delete_count"></span>)</h2>
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
																<div id="items-deleted-content"></div>
															</div>
														</div>
													</div>
												</div>
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
                                                                                    <h2><i class="fa fa-bars"></i> Incoming Purchases Deleted (<span class="purchase_delete_count"></span>)</h2>
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
																<div id="purchasing-deleted-content"></div>
															</div>
														</div>
													</div>
												</div>										
											</div>	
											</div>
							                  </div>             
							               </div>						                   
                    </div>               
                </div>
            <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing.js"></script>
            <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/main_purchasing.js"></script>