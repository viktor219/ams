<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Location;
use app\models\User;
use app\models\LocationClassment;
use app\models\LocationParent;
use app\models\Manufacturer;

$page = 'Overview';

$this->title = $customer->companyname . ' - ' . $page;

?>
<style>
	.panel-primary
	{
		border-color: #73879C;
	}
	.panel-primary>.panel-heading, .panel-body .btn-primary
	{
		background: #73879C;
		border-color: #73879C; 
	}
</style>
<?php //LOAD LOCATION ADD FORM --->?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_addlocation");?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet" />
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<?php if(Yii::$app->user->identity->usertype === User::TYPE_CUSTOMER):?>
    <?= $this->render("modals/_transferloc");?>
<?php endif; ?>
<div class="row row-margin">			
	<?php $form = ActiveForm::begin(['action'=>['/orders/createrma'], 'options' => ['enctype' => 'multipart/form-data', 'id'=>'add-rmaorder-form']]); ?>
		<div class="col-lg-12 col-xs-12">
			<div class="x_panel" style="padding: 10px 10px;">
				<div class="x_title">
					<h2><i class="fa fa-bars"></i> New RMA Order</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content" style="padding:0;margin-top:0;">
					<?php echo Html::hiddenInput('customer_Id', $customer->id, ['id' => 'customer_Id']); ?>
					<div class="" role="tabpanel" data-example-id="togglable-tabs">
						<div id="myTabContent" class="tab-content">	
							<div class="row row-margin">
								<div class="panel panel-primary">
								  <div class="panel-heading">
									<h3 class="panel-title">Please Choose A Location:</h3>
								  </div>
								  <div class="panel-body">
									  <div class="form-group">
										<div class="input-group" style="margin-bottom: 5px;"> 
											<span class="input-group-btn">
												<button type="button" class="btn btn-success btn-md locationField" onClick="openOrderLocation(<?php echo $customer->id;?>)"><span class="glyphicon glyphicon-plus"></span></button>
											</span>
											<select class="form-control default_select2_single" name="rma_location" id="selectlocation">
												<option value="">Select A Location</option>
												<?php foreach($locations as $location) :?>
													<?php 
														$output = "";
														if(!empty($location->storenum))
															$output .= "Store#: " . $location->storenum . " - ";
														if(!empty($location->storename)) 
															$output .= $location->storename  . ' - '; 
														//
														$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
													?>
													<option value="<?php echo $location->id;?>"><?php echo $output;?></option>
												<?php endforeach;?>
											</select>
										</div>
									  </div>
								  </div>
								</div>								
							</div>
							<div class="row row-margin" style="display:none;" id="rma-location-items-box">
								<div class="panel panel-primary">
								  <div class="panel-heading">
									<h3 class="panel-title" id="locationmodelsboxtitle">We currently show (0) model at your location</h3>
								  </div>
								  <div class="panel-body">
								  	<div id="location-model-content">Please select a location...</div>
								  </div>
								</div>
							</div>							
							<div class="row row-margin" style="display:none;" id="rma-location-add-items-box">
								<div class="panel panel-primary">
								  <div class="panel-heading">
									<h3 class="panel-title">Do you have any additional items at your location?</h3>
								  </div>
								  <div class="panel-body">
								 	<div id="entry1" class="clonedInput"> 
								 		<div class="row row-margin">
											<div class="form-inline">
											    <label for="selectmodel" style="font-weight: normal;font-size: 14px;">Transfer Additional Models To This Location:</label>
											    <select class="form-control" id="selectmodel_1" name="additional_model[]">
											    	<option disabled="disabled" selected="selected">Select A model</option>
													<?php foreach ($models as $model) :?>
														<?php 
															$_manufacturer = Manufacturer::findOne($model->manufacturer);
														?>
														<option value="<?php echo $model->id;?>"><?php echo $_manufacturer->name . ' ' . $model->descrip;?></option>
													<?php endforeach;?>
											    </select>
											</div>	
											<div class="row row-margin"></div>
											<div class="form-inline" id="additonalmodelbqty_1" style="display: none">
											    <label for="modelqty">How many additional <span id="currentmodelselected_1">Model 1's</span> do you have?:</label>
											    <input type="number" class="form-control" id="modelqty_1" name="additional_model_qty[]" placeholder="Quantity" min="1">
											    <input type="hidden" id="model_serialized_1" value="0" />
											</div>	
											<div class="row row-margin" id="add-serial-number-box_1">
												<div class="form-group" id="qty_unit_box_1" style="display: none">
    												<p class="lead">Please provide the serial numbers for those (<span id="qty_unit_1"></span>) units?</p>
    											</div>
    											<div id="loaded-serial-fields_1"></div>
    										</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="actions text-right">
												<button class="btn btn-primary btn-xs" id="saveTransferItem" type="submit"><span class="glyphicon glyphicon-plus"></span></button>
											</div>
										</div>
									</div>															  
								  </div>
								</div>
							</div>	
							<div class="row row-margin">
								<div class="col-md-12 text-right">							
									<?= Html::button('<span class="glyphicon glyphicon-remove"></span> Reset', ['class'=>'btn btn-danger', 'id'=>'resetFormButton']) ?>										
									<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create RMA Now',['class' => 'btn btn-success']) ?>
									<label for="allowdirectshippingreq" class="checkbox-inline" style="font-weight:bold;">Send replacements for this RMA?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="allowwarehouseorder" name="allowwarehouseorder" checked>									
								</div>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php ActiveForm::end(); ?>
</div>
<div class="row row-margin">	
	<div class="col-lg-12 col-xs-12">
		<div class="x_panel" style="padding: 10px 10px;">
			<div class="x_title">
				<div class="col-md-3">
					<h2><i class="fa fa-bars"></i> <?php echo $customer->companyname;?> Inventory</h2>
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<?php $form = ActiveForm::begin([
						        'action' => ['index'],
						        'method' => 'get',
					    		//'options' => ['onkeypress'=>"return event.keyCode != 13;"]
						    ]); ?>
						<div id="searchorder-group" class="pull-right top_search">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-success" id="searchSerialBtn" type="button"><b style="color:#FFF;">?</b></button> 
								</span>
								<input type="search" placeholder="Search serial number..." id="searchSerial" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
							</div>
						</div>
					<?php ActiveForm::end(); ?>				
				</div>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="padding:0;margin-top:0;">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
						<div class="row row-margin">
							<?php if(!empty($categories)) :?>
								<div class="row row-margin">
									<div class="col-md-12 col-sm-6 col-xs-12">
										 <div class="x_panel">
											<div class="x_content">
												<div class="" role="tabpanel" data-example-id="togglable-tabs" id="rma-main-gridview">
													<ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
														<li role="presentation" class="active" id="customer-inventory-home"><a href="#rmacustomerinventoryhome" id="rma-overview-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
														</li>
														<li role="presentation" class="" id="customer-inventory-division"><a href="#rmacustomerinventorydivision" id="rma-overview-division-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Choose A Division</a>
														</li>														
														<li role="presentation" class="" style="display:none;" id="search-serial-overview-title"><a href="#rmacustomerinventorysearch" id="rma-overview-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Search(<span id="serial-search-count"></span>)</a>
														</li>                                                                                                                                                                                                                                                                  
													</ul>
													<div id="myTabContent" class="tab-content">   
														<div role="tabpanel" class="tab-pane fade active in" id="rmacustomerinventoryhome" aria-labelledby="rmacustomerinventoryhome-tab">
															<?= $this->render('_stockoverview', ['customer'=>$customer, 'categories'=>$categories, '_location'=>$_location]) ?>
														</div>
														<div role="tabpanel" class="tab-pane fade in" id="rmacustomerinventorydivision" aria-labelledby="rmacustomerinventorydivision-tab">
															<?= $this->render('_locationstockoverview', ['customer'=>$customer, 'locations'=>$_inventorylocations, '_location'=>$_location]) ?>
														</div>
														<div role="tabpanel" class="tab-pane fade in" id="rmacustomerinventorysearch" aria-labelledby="rmacustomerinventorysearch-tab">
															<div id="loaded-serial-search-content"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php else :?>
								<div style="text-align: center;"><h2>No Items in Inventory</h2></div>
							<?php endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>							
    $("[name='allowwarehouseorder']").bootstrapSwitch("size", "small");
</script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/overview-representative.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/location.js"></script>