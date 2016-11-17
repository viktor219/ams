<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\Category;
use app\models\User;
use app\models\UserHasCustomer;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = "Inventory Models";
$this->params['breadcrumbs'][] = $this->title;

$customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid'=>Yii::$app->user->id])->asArray()->all(), 'customerid');			
?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/tabs.css" rel="stylesheet">
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<?php //LOAD REORDER FORM --->?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_reorder");?>
<?= $this->render("@app/views/layouts/_modals/_deleteconfirm", ['page' => 'Inventory']); ?>
<?php /*$this->render("_modals/_merge"); */?>
<?= $this->render("_modals/_transfer"); ?>
<?= $this->render("_modals/_warehouse"); ?>
<?php /*<iframe src="<?php echo Yii::$app->request->baseUrl;?>/uploads/orders/03282101.pdf" onload="window.print()"></iframe>*/?>
<div class="order-index" id="inventory-page-responsive">
    <!-- Sales Order Dashboard -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="col-md-5 vcenter inventory-head">
                    <h4>
                        <span class="glyphicon glyphicon-list-alt"></span>
                        <?= Html::encode($this->title) ?>
                    </h4>
                </div>
				<div class="col-md-7 vcenter text-right"> 
					    <?php $form = ActiveForm::begin([
						        'action' => ['index'],
						        'method' => 'get',
						    ]); ?>
						<div id="searchinventory-group" class="pull-right top_search">
							<div class="input-group <?php echo (Yii::$app->user->identity->usertype==User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)?"search-representative":"";?>">
								<span class="input-group-btn">
									<button class="btn btn-success" id="searchInventoryBtn" type="button"><b style="color:#FFF;">?</b></button> 
								</span>
								<input type="text" placeholder="Search" id="searchInventory" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
                                <?php if(Yii::$app->user->identity->usertype==User::REPRESENTATIVE || Yii::$app->user->identity->usertype==User::TYPE_CUSTOMER): ?>
                                	<span class="input-group-btn navigate-buttons">
                                		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> New Order', 'javascript:;', ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;', 'id'=>'show-inventory-order']) ?>
                                	</span>
                                <?php else :?>
									<span class="input-group-btn navigate-buttons">
										<?= Html::a('<span class="glyphicon glyphicon-plus"></span> Model', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
										<?= Html::a('<span class="glyphicon glyphicon-plus"></span> Assembly', ['/assembly/create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 1px;border-radius:4px;']) ?>								
									</span>
                                <?php endif; ?>
							</div>						
						</div>
					<?php ActiveForm::end(); ?>
				</div>                
            </div>
        </div>
        <div class="panel-body">
		<div class="row row-margin">
            <div class="col-md-12 col-sm-6 col-xs-12">
                 <div class="x_panel">
                                <div class="x_content">
                                <!-- main gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="inventory-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
                                            <li role="presentation" class="active"><a href="#inventoryhome" onClick="loadInventory('');" id="inventory-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <?php if(Yii::$app->user->identity->usertype!==User::REPRESENTATIVE && Yii::$app->user->identity->usertype!==User::TYPE_CUSTOMER): ?>
                                            <li role="presentation" class=""><a href="#inventoryassemblies" onClick="loadIAssembly('');" id="inventory-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Assemblies</a>
                                            </li>  
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Categories <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu">
												  	<?php 
														if(Yii::$app->user->identity->usertype != 1)
															$queryCategory = Category::find()->select('lv_categories.id, categoryname')->join('INNER JOIN', 'lv_models', 'lv_models.category_id =lv_categories.id')->groupBy('category_id')->all();
														else 
															$queryCategory = Category::find()->select('lv_categories.id, categoryname')->join('INNER JOIN', 'lv_models', 'lv_models.category_id =lv_categories.id')->join('INNER JOIN', 'lv_items', 'lv_items.model =lv_models.id')->where(['lv_items.customer'=>$customers])->groupBy('category_id', 'model')->all();															
													?>
												  	<?php foreach ($queryCategory as $category) :?>
														<li class="list"><a href="javascript:;" onClick="loadCategoryModels(<?php echo $category->id;?>, '', '<?php echo $category->categoryname;?>');"><?php echo $category->categoryname;?></a></li>										  	
												  	<?php endforeach;?>
												  </ul>                                            
                                            </li>  
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Customers <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu">
												  <li><input type="search" class="form-control" autocomplete="off" id="searchCustomer" style="border-radius:5px;margin:5px;"/></li>
												  	<?php 
														if(Yii::$app->user->identity->usertype != 1)
															$queryCustomer = Customer::find()->select('lv_customers.*')->join('INNER JOIN', 'lv_items', 'lv_items.customer =lv_customers.id')->groupBy('customer')->all();
														else 
															$queryCustomer = Customer::find()->select('lv_customers.*')->join('INNER JOIN', 'lv_items', 'lv_items.customer =lv_customers.id')->where(['lv_items.customer'=>$customers])->groupBy('customer')->all();
													?>
													<?php foreach ($queryCustomer as $customer) :?>
														<li class="list"><a href="javascript:;" onClick="loadCustomerModels(<?php echo $customer->id;?>, '', '<?php echo $customer->companyname; ?>');"><?php echo $customer->companyname;?></a></li>										  	
												  	<?php endforeach;?>
												  </ul>                                           
                                            </li>
                                            <li role="presentation" class="left" id="inventory-deleted-tab"><a href="#inventorydelete" onClick="deletedInventory('');" id="order-tab-6" role="tab" data-toggle="tab" aria-expanded="true">Deleted (<span class="delete_count"><?= $basket_count; ?></span>)</a>
	                                            </li>
                                            <?php endif; ?>
                                        </ul>
                         				<!--<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>     -->          
                                                    <div class="x_panel" style="padding: 10px 10px;display: none;" id="inventory-customer-new-order">
                                                        <div class="x_title">
                                                            <h2><i class="fa fa-bars"></i><span style="color: #73879C"> New Order</span></h2>
                                                                <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                                        <li class="dropdown">
                                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                                <li><a href="#">Settings 1</a>
                                                                                </li>
                                                                                <li><a href="#">Settings 2</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                                                </ul>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content" style="padding:0;margin-top:0;">
                                                        	<?php $form = ActiveForm::begin(['action'=>['/inventory/createwarehouseorder'], 'options' => ['id'=>'add-warehouse-order-form']]); ?>
                                                        	<div class="form-group">
                                                        		<label>Choose A Location :</label>
																<div class="input-group" style="margin-bottom: 5px;"> 																	
																	<span class="input-group-btn">
																		<button type="button" class="btn btn-success btn-md locationField" onClick="openOrderLocation(<?php echo $customer->id;?>)"><span class="glyphicon glyphicon-plus"></span></button>
																	</span>
																	<select class="form-control default_select2_single" name="order_location" id="selectlocation">
																		<option value="">Select A Location</option>
																	</select>
																</div>
															  </div>
															  <div id="loaded-order-item-content">
															  	<?= $this->render('_loaded_model_warehouse', ['dataProvider' => $dataCartProvider]); ?>
															  </div>
																<div class="row row-margin">
																	<div class="col-md-12 text-right rmaform-actions">			
																		<?= Html::button('<span class="glyphicon glyphicon-remove"></span> Reset', ['class'=>'btn btn-danger', 'id'=>'resetFormButton']) ?>										
																		<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create Order',['class' => 'btn btn-success']) ?>									
																	</div>
																</div>	
															<?php ActiveForm::end(); ?>										  
                                                        </div>
                                                     </div>                                                                                          
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="inventoryhome" aria-labelledby="home-tab">
                                                    <div class="x_panel" style="padding: 10px 10px;" id="inventory-panel">
                                                        <div class="x_title">
                                                            <h2><i class="fa fa-bars"></i><span style="color: #73879C"> All</span></h2>
                                                                <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                                        <li class="dropdown">
                                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                                <li><a href="#">Settings 1</a>
                                                                                </li>
                                                                                <li><a href="#">Settings 2</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                                                </ul>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content" style="padding:0;margin-top:0;">
                                                            <div id="inventory-loaded-content"></div>                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane fade" id="inventorydelete" aria-labelledby="inventory-delete-tab">
                                                    <div class="x_panel" style="padding: 10px 10px;">
                                                        <div class="x_title">
                                                            <h2><i class="fa fa-bars"></i> Deleted</h2>
                                                                <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                                        <li class="dropdown">
                                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                                <li><a href="#">Settings 1</a>
                                                                                </li>
                                                                                <li><a href="#">Settings 2</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                                                </ul>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content" style="padding:0;margin-top:0;">
                                                            <div id="assembly-msg"></div>                  
												<div id="inventory-deleted-content"></div>                                           
                                                        </div>
                                                    </div>
                                                </div>
                                        	<div role="tabpanel" class="tab-pane fade" id="inventoryassemblies" aria-labelledby="home-tab">
                                                    <div class="x_panel" style="padding: 10px 10px;">
                                                        <div class="x_title">
                                                            <h2><i class="fa fa-bars"></i> All</h2>
                                                                <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                                        <li class="dropdown">
                                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                                <li><a href="#">Settings 1</a>
                                                                                </li>
                                                                                <li><a href="#">Settings 2</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                                                </ul>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content" style="padding:0;margin-top:0;">
                                                            <div id="assembly-msg"></div>                  
												<div id="iassembly-loaded-content"></div>                                           
                                                        </div>
                                                    </div>
                                                </div>										
                                            </div>
                                    </div>	
									<!-- search gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="inventory-search-gridview" style="display:none;">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#inventorysearch" id="order-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="inventory-results-count"><b>0</b></span>) </a>
                                            </li>                                                                                                        
                                        </ul>
                         				<!--<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               -->
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="inventorysearch" aria-labelledby="home-tab">                 
												<div id="inventory-loaded-content-search"></div>
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
<!-- End -->
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/inventory.js"></script>