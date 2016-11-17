<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\Location;
use app\models\Itemcondition;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
	.tooltip
	{
		width: 200px;
	}
</style>
<?= $this->render("@app/modules/Shared/_modals/_oserials", ["order"=>array()]);?>
<?php 
//$session = Yii::$app->session;
//	echo $session['currentquantity'];
?>
<div class="row row-margin">
<?php $form = ActiveForm::begin(['options' => ['action'=>['/receiving/create'], 'id'=>'receive-unscheduled-inventory-form']]); ?>
	<div class="col-md-12 col-sm-6 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">
					    	<div class="modal-body">
								<div class="container">
									<div class="row row-margin">
										<div id="r_customer-group" class="form-group col-md-6">
											<input type="text" id="receiving-customer" class="form-control" name="customer" placeholder="Choose a Customer or Project" autocomplete="off" value="">
											<input type="hidden" id="customer_Id" name="customerId" />
										</div>	
										<div id="r_location-group" class="form-group col-md-6">
											<select class="form-control receiving_location_select2_single" tabindex="-1" id="rselectLocation" name="location" >
												<option selected="selected" value="">Select A Location</option>
												<?php 
													$locations = Location::find()->where(['customer_id'=>4])->all();
													foreach($locations as $location)
													{
														$output = "";
														if(!empty($location->storenum))
															$output .= "Store#: " . $location->storenum . " - ";
														if(!empty($location->storename))
															$output .= $location->storename  . ' - ';
														//
														$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
														echo '<option value="' . $location->id . '">' . $output . '</option>';
													}											
												?>
											</select>						
										</div>
									</div>
									<div class="row row-margin">
										<div id="returnstore-group" class="form-group col-md-6" style="text-align:center;">
											<label for="requireboxcount" class="checkbox-inline" style="padding-left:5px;font-weight:bold;">Is this a return shipment from a specific location? </label>
											<input type="checkbox" data-on-text="Yes" data-off-text="No" id="returnstore" name="returnstore">									
										</div>	
										<div id="storenumber-group" class="form-group col-md-6">									
											<select class="form-control store_select2_single storenumberinput" tabindex="-1" name="storenumber" id="storenumber" style="display:none;">
												<option value="" selected="selected">Returned From</option>
											</select>	
										</div>	
									</div>							
									<div class="row row-margin">
										<div class="form-group">					
											<div class="well well-sm">
												<label for="selectItem"><small>Add An Item:</small></label>
												<div id="entry1" class="clonedInput">
													<div class="row form-group">
														<div class="form-group col-sm-1 r_qty-group">
															<input class="rquantity form-control" type="number" name="quantity[]" id="quantity_1" value="" placeholder="Qty">
														</div>
														<div class="form-group col-sm-8 r_model-group">
															<div class="input-group">
																<!--<span class="input-group-btn">
																	<button class="btn btn-success edit_item_button" id="Edit_1" type="button" style="display:none;"><span class="glyphicon glyphicon-pencil"></span></button>
																</span>	  -->	
																<span class="input-group-btn">
																	<button class="btn btn-success add_serial_button" id="Serial_1" type="button" disabled><span class="glyphicon glyphicon-barcode"></span></button>																																														
																</span>					
																<input class="typeahead form-control input_fn" type="text" name="description[]" id="autocompleteitem_1" value="" placeholder="Select an Item" data-provide="typeahead" autocomplete="off" type="search">
																<input class="form-control input_h" type="hidden" name="modelid[]" id="autocompletevalitem_1" />																																								
																<input class="r_serialnumber" value="0" id="serialnumber_1" type="hidden" disabled/>												
																<!--<span class="input-group-btn">
																	<button class="btn btn-success comment_item_button" id="Comment_1" type="button" disabled><span class="glyphicon glyphicon-comment"></span></button>														
																</span>-->
															</div>																																											
														</div>
														<div class="row form-group palletnumber-group col-sm-2 clear_row_margin" style="padding-left: 5px; display:none;" id="palletnumber-group">												
															<div class="input-group">
																<input class="form-control palletnumber" type="text" name="palletnumber[]" id="palletnumber_1" placeholder="Count pallet..." value="0"/>														
																<span class="input-group-btn">
																	<button class="btn btn-success next_up_pallet_button" id="uppallet_1" type="button" disabled>Next Pallet</button>														
																</span>	
															</div>														
														</div>	
														<div class="row form-group boxnumber-group col-sm-2 clear_row_margin" style="display:none;" id="boxnumber-group">													
															<div class="input-group">
																<input class="form-control boxnumber" type="text" name="boxnumber[]" id="boxnumber_1" placeholder="Count box..." value="0"/>														
																<span class="input-group-btn">
																	<button class="btn btn-success next_up_box_button" id="upbox_1" type="button" disabled>Next Box</button>														
																</span>	
															</div>													
														</div>
														<div class="form-group col-sm-2 r_model-group clear_row_margin">
															<select class="form-control itemoption" name="itemoption[]" id="itemoption_1">
																<option value="">Select An Option</option>
																	<?php foreach(Itemcondition::find()->all() as $option) :?>
																		<option value="<?php echo $option->id;?>" <?php if($option->id==4):?>selected<?php endif;?>><?php echo $option->name;?></option>
																	<?php endforeach;?>
															</select>														
														</div>															
														<div class="form-group col-sm-1 clear_serialized-group clear_row_margin" style="display:none;padding-left:5px;">
															<button class="btn btn-success clear_item_button" id="Clearbtn_1" type="button"><span class="glyphicon glyphicon-remove-circle"></span></button>
														</div>																													
													</div>													
													<!--<div class="row form-group">
														<div class="col-sm-1"></div>
														<div class="col-sm-9">
															<textarea class="comment form-control"  id="itemNote_1" placeholder="Add additional notes or instructions..." style="display: none;" rows="3" name="itemnotes[]" ></textarea>
														</div>	
														<div class="col-sm-1"></div>					
													</div>	-->										
												</div>
											</div>	
										</div>
									</div>	
									<div class="row">
										<div class="col-sm-12">
											<div class="actions text-right">
												<button class="btn btn-success btn-xs" id="btnRAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
												<button class="btn btn-success btn-xs" id="btnRDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
											</div>
										</div>
									</div>
									<div class="row-margin"></div>	
									<div class="row">
										<div class="col-md-12 text-right">
											<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-primary', 'onClick'=>'redirectReceive();']) ?>										
											<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create',['class' => 'btn btn-success']) ?>											
										</div>
									</div>																																		
								</div>        		
					    	</div>									
				</div>
			</div>
		</div>
	</div>
	</div>
<?php ActiveForm::end(); ?>	
</div>
<script>
    $("[name='returnstore']").bootstrapSwitch("size", "mini");
</script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/receiving_create.js"></script>