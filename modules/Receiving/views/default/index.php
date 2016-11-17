<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	
	$this->title = 'Receiving';
	
	$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render("_modals/_receiveqtymodal");?>
<?php //= $this->render("_modals/_receivingcreatedetails");?>
<?= $this->render("@app/modules/Purchasing/views/default/_modals/_receiveqtymodal");?>
<?php //= $this->render("@app/modules/Orders/views/default/_modals/_serials", ["order"=>array()]);?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_customerdetails");?>
                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-6 vcenter">
									<h4><span class="glyphicon glyphicon-save"></span> Receiving Overview</h4>
                                </div>
                                <div class="col-md-6 vcenter text-right">                             
                                        <a href="<?= Url::to(['/receiving/create']) ?>" class="btn btn-success"> 
                                            <span class="glyphicon glyphicon-plus"></span> Receive Inventory
                                        </a>
                                </div>
                            </div>
                        </div>
                    <div class="panel-body" style="padding: 15px 0 0 0"> 
        <div class="panel-body">
		<div class="row row-margin">
            <div class="">
                 <div class="x_panel" style="padding: 0px; border: none;">
                                <div class="x_content">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="receiving-main-gridview">
                                        <ul id="myTab" class="hide-mobile nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#receivinghome" id="receiving-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class=""><a href="#receivingrir" id="receiving-tab-2" role="tab" data-toggle="tab" aria-expanded="true">Received Items(<span id="received-items-count"><?php echo ($received_po_count + $received_so_count); ?></span>)</a>
                                            </li>                                            
                                            <li role="presentation" class=""><a href="#receivingrip" id="receiving-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Incoming Purchases</a>
                                            </li>  
                                            <li role="presentation" class=""><a href="#receivingricr" id="receiving-tab-3" role="tab" data-toggle="tab" aria-expanded="true">Incoming Customer Inventory</a>
                                            </li>                                                                                                                                                                                                                        
                                        </ul>
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="receivinghome" aria-labelledby="receivinghome-tab">                 
												<div id="receivinghome-loaded-content">
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
								                                    <div class="load-incomingpurchase-content" role="tabpanel" data-example-id="togglable-tabs">
								                                    </div>
								                                </div>
								                            </div>
								                        </div>		        
								                    <div class="panel-body" style="padding: 15px 0 0 0">
								                        <div class="row row-margin">
								                            <div class="x_panel">
								                                <div class="x_title">
								                                    <h2><i class="fa fa-bars"></i> Incoming Customer Inventory</h2>
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
								                                    <div class="load-customer-inventory" role="tabpanel" data-example-id="togglable-tabs">						                                    
								                                    </div>
								                                </div>
								                            </div>
								                       </div>
								                 </div>   											
												</div>
											</div>    
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingrip" aria-labelledby="receivingrip-tab">                
												<div id="receivingrip-loaded-content">
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
							                                    <div class="load-incomingpurchase-content" role="tabpanel" data-example-id="togglable-tabs">
							                                    </div>
							                                </div>
							                            </div>
							                        </div>																								
												</div>
											</div>  
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingrir" aria-labelledby="receivingrir-tab">                
												<div id="receivingrir-loaded-content">
													<div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Received SO Items(<span id="so-received-items-count"><?php echo $received_so_count;?></span>)</h2>
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
  																		<?= $this->render('_received_soitem', ['dataProvider' => $dataProvider2]); ?> 
																	</div>
																</div>
															</div>
														</div>													
													<div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Received PO Items(<span id="po-received-items-count"><?php echo $received_po_count;?></span>)</h2>
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
																		<?= $this->render('_received_poitem', ['dataProvider' => $dataProvider]); ?>  
																	</div>
																</div>
															</div>
														</div>															
												</div>
											</div>  
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingricr" aria-labelledby="receivingricr-tab">                                    	                
												<div id="receivingricr-loaded-content">
								                    <div class="panel-body" style="padding: 15px 0 0 0">
								                        <div class="row row-margin">
								                            <div class="x_panel">
								                                <div class="x_title">
								                                    <h2><i class="fa fa-bars"></i> Incoming Customer Inventory</h2>
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
								                                    <div class="load-customer-inventory" role="tabpanel" data-example-id="togglable-tabs">
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
							</div>
						</div>
					</div>																			                                        
        </div>                                               			
                    </div>
                </div>    
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing.js"></script>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/receiving.js"></script>