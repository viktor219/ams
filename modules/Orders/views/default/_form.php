<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ordertype;
use app\models\Models;
use app\models\Manufacturer;
use app\models\ModelOption;
use app\models\OrderPackageOptoin;
use app\models\Customer;
use app\models\Location;
use app\models\Medias;
use app\models\Item;
use app\models\Shipment;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use app\models\ShipmentType;
use app\models\Itemsordered;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
/*$shippingmethod = ShipmentMethod::findOne($assetSetting->shipping_method);
$_shipping_method = null;
*/
//
$_shipment = array();
$_shipping_method = array();
$_shipping_company = array();
if(!$model->isNewRecord)
{
	$_shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
	$_shipping_method = ShipmentMethod::find($_shipment->shipping_deliverymethod)->one();
}
//var_dump($_shipping_method);exit(1);
?>
<style>
	.col-md-6
	{
		padding: 0 3px 0 3px;
	}
</style>
<?= $this->render('_modals/_addcustomer', ['model' => $model]);?>
<?= $this->render("_modals/_pdfmodal");?>
<?= $this->render("_modals/_editmodel");?>
<?= $this->render("_modals/_newconfiguration");?>
<div class="row row-margin">			
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'action'=>['/orders/create'], 'id'=>'add-order-form']]); ?>
	<div class="col-lg-12 col-xs-12">
		<div class="x_panel" style="padding: 10px 10px;">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="padding:0;margin-top:0;">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
					<div class="row row-margin">
						<div class="col-md-6" style="padding: 3px 0 0 0">
						<div class="col-md-6">
							<div class="row row-margin" id="custom_upload">
								<div class="col-md-2" id="i-upload-pdf-icon" style="visibility:hidden;">
									<button type="button" class="btn btn-dark btn-xs" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>									
								</div>							
								<div class="col-md-8" id="i-upload-view-button" style="visibility:hidden;">
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button class="btn btn-info btn-xs" type="button" onClick="ViewCurrentPdf();">Click To View File</button>
										</div>
									</div>
									<!--<div class="jFiler-jProgressBar"><div class="custom_progress_bar"></div></div>-->
								</div>
								<div class="col-md-2" style="visibility:hidden;" id="i-upload-trash-button">
									<a class="icon-jfi-trash jFiler-item-trash-action btn btn-danger btn-xs" style="cursor:pointer;"></a>
								</div>
							</div>
							<!-- Upload file -->
							<div id="fileupload-group">
								<input type="file" name="files" id="orderpdf" accept="application/pdf" multiple="multiple"/>
								<?php if(!$model->isNewRecord && $model->orderfile!==null) :?>
									<a href="javascript:;" class="btn btn-success" onClick='OpenPdfViewer("<?php echo Yii::$app->request->baseUrl . '/uploads/orders/'. Medias::findOne($model->orderfile)->filename;?>");'><b>Preview</b> (<?php echo Medias::findOne($model->orderfile)->filename; ?>)</a>	
								<?php endif;?>
							</div>								
						</div>
						<div class="col-md-6">
						<h5 style="text-align:center;font-weight:bold;"><i class="fa fa-level-down"></i> Step #1: Customer Details</h5>
							<!-- Customer --> 
							<div id="customer-group">					
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-success" id="addCustomerBtn"><span class="glyphicon glyphicon-plus"></span></button>
									</span>
									<input type="text" id="autocomplete-customer" tabindex="1" class="form-control inputs" name="customer" placeholder="Choose Customer" autocomplete="off" value="<?php if(!$model->isNewRecord) echo Customer::findOne($model->customer_id)->companyname;?>" autofocus>
									<input type="hidden" id="customer_Id" name="customerId" value="<?php echo $model->customer_id;?>"/>
								</div>					
							</div>	
							<!-- end Customer -->
							<!-- Location -->
								<div class="" id="location-group"> 
									<div class="input-group input-button-right">
										<span class="input-group-btn">
											<button type="button" class="btn btn-success btn-md locationField" onClick="openOrderLocation(document.getElementById('customer_Id').value)"><span class="glyphicon glyphicon-plus"></span></button>
										</span>
										<select class="shipping_select2_single form-control inputs" id="selectLocation" name="location" <?php if($model->isNewRecord) :?> disabled <?php endif;?>>
											<option value="">Select A Location</option>
											<?php if(!$model->isNewRecord) :?> 
												<?php foreach(Location::findAll(['customer_id'=>$model->customer_id]) as $location) :?>
													<?php 
														$output = "";
														if(!empty($location->storenum))
															$output .= "Store#: " . $location->storenum . " - ";
														if(!empty($location->storename))
															$output .= $location->storename  . ' - '; 
														//
														$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
													?>
													<option <?php if($model->location_id===$location->id) : ?>selected="selected"<?php endif;?> value="<?php echo $location->id;?>"><?php echo $output;?></option>
												<?php endforeach;?>
											<?php endif;?>
										</select>
									</div> 
								</div>
								<div class="" id="customerorder-group" style="margin-top:5px;">
									<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #8 : Add Customer Order Number (Optional)</label>
									<input type="text" class="form-control" id="customerOrder" placeholder="Customer Order#" name="customerorder" value="<?php echo $model->enduser_po;?>" >
								</div>	
								<div class="" id="enduserorder-group" style="margin-top:5px;">
									<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #7 : Add End-User Order Number (Optional)</label>
									<input type="text" class="form-control" id="enduserOrder" placeholder="End User Order#" name="enduser" value="<?php echo $model->customer_po;?>" >
								</div>
							</div>					
						</div>
							<div class="col-md-6" style="padding: 0 0 3px 0">
								<h5 style="text-align:center;font-weight:bold;"><i class="fa fa-level-down"></i> Step #2: Order Details</h5>
								<div class="col-md-6">
									<div id="shipbydate-group">
										<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #6 : Choose A Ship-By Date</label>
										<div class="xdisplay_inputx form-group has-feedback" style="margin-left: 0;">
											<input class="form-control has-feedback-left shipbydatef" id="single_cal3" placeholder="Ship By Date" name="shipby" aria-describedby="inputSuccess2Status3" type="text" value="<?php echo (!$model->isNewRecord) ? date('m/d/Y', strtotime($model->shipby)) : date('m/d/Y');?>" >
											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true" style="left:5px;"></span>
											<span id="inputSuccess2Status3" class="sr-only">(success)</span>
										</div>
									</div>														
									<div class="form-group" style="display:none;">
										<!-- Shipment -->
										<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #4 : Choose A Shipment Method</label>
										<input type="text" id="autocomplete-shipment-type" class="form-control" name="shipmenttype" placeholder="Select Shipment type" autocomplete="off" >
									</div>	
									<!-- End Shipment -->
									<div class="form-group" style="display:none;">
										<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #5: Choose The Type Of Order</label>
										<select class="form-control" id="purchasetype" name="purchasetype">
											<option selected="selected" disabled="disabled" value="">Select A Type Of Order</option>
											<?php foreach(Ordertype::find()->all() as $type) :?>	
												<option value="<?php echo $type->id;?>" <?php echo ($model->ordertype===$type->id) ? 'selected' : '';?>><?php echo $type->name;?></option>							  		
											<?php endforeach;?>
										</select>									
									</div>
									<div id="notes-group">
										<label for="selectCustomer" class="sr-only"><i class="fa fa-level-down"></i> Step #9 : Add Additional Notes (Optional)</label>
										<textarea class="form-control"  id="orderNotes" placeholder="Add additional notes or instructions..." style="border-radius: 3px;" name="notes" rows="3" ><?php echo $model->notes;?></textarea>
									</div>
								</div>
							<div class="col-md-6">
								<div class="btn-group btn-group-justified" id="switch-shipping-detail-tab" role="group" aria-label="Shipping Details">
									 <div class="btn-group" role="group">
										<button type="button" data-switch-set="shippingdetail" data-switch-value="0" class="btn btn-success" data-toggle="tooltip" title="Load Asset Shipping method.">Asset</button>
									 </div>
									 <div class="btn-group" role="group">
										<button type="button" data-switch-set="shippingdetail" data-switch-value="1" class="btn btn-success <?php if(!$model->isNewRecord) :?>active<?php endif;?>" data-toggle="tooltip" title="Choose your Shipping method.">Customer</button>
									 </div>
									 <div class="btn-group" role="group">
										<button type="button" data-switch-set="shippingdetail" data-switch-value="2" class="btn btn-success" data-toggle="tooltip" title="Load Customer Secondary Shipping method.">Other</button>
									 </div>
								</div>
								<div class="" id="shipping-detail-form-content" style="margin-top:5px">
									<h2 class="sr-only">Shipping Company</h2>
									<div class="form-group" id="shippingcompany-group">
										<select class="shipping_company_select2_single form-control" id="shippingcompany" name="shippingcompany" disabled>
											<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
											<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
												<option value="<?php echo $shippingcompany->id;?>" <?php if(!$model->isNewRecord) echo ($_shipping_method->shipping_company_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
											<?php endforeach;?>
										</select>								
									</div>	
									<div class="form-group" id="accountnumber-group">
										<h2 class="sr-only">Account Number</h2>
										<input type="text" class="form-control" id="accountnumber" placeholder="Account Number" name="accountnumber" value="<?php if(!$model->isNewRecord) echo $_shipment->accountnumber;?>" disabled>
									</div>
									<h2 class="sr-only">Delivery Method</h2>
									<div class="form-group" id="shippingmethod-group">
										<select class="shipping_method_select2_single form-control" id="shippingmethod" name="shippingmethod" disabled>
											<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
											<?php if(!$model->isNewRecord) :?>
												<?php foreach(ShipmentMethod::findAll(['shipping_company_id'=>$_shipping_method->shipping_company_id]) as $shipmethod) :?>
													<?php 
														if($shipmethod->shipping_company_id===1)
														{
															$ups = new \Ups\Entity\Service;
															$ups->setCode($shipmethod->_value);	
															$__shipping_method = $ups->getName();
														}
														else if($shipmethod->shipping_company_id===3) //Waiting DHL issues solved
														{}
														else
														{
															$__shipping_method = $shipmethod->_value;
														}
													?>						
													<option value="<?php echo $shipmethod->id;?>" <?php if(!$model->isNewRecord) echo ($_shipment->shipping_deliverymethod===$shipmethod->id) ? 'selected' : '';?>><?php echo $__shipping_method;?></option>
												<?php endforeach;?>
											<?php endif;?>
										</select>								
									</div>
								</div>
							</div>
						</div>
					</div>
		<!-- End Location -->
					<div class="row row-margin"></div>
					<div class="row row-margin">
						<div class="row-margin" id="puchasetype-group">
							<div class="btn-group btn-group-justified" id="switch-order-type" role="group" aria-label="Order types" > 								
								<?php foreach(Ordertype::find()->all() as $key=>$type) :?>
									<div class="btn-group" role="group">
								     	<button type="button" data-switch-set="ordertype" data-switch-value="<?php echo $type->id;?>" class="btn btn-default bt-switch-btn <?php echo ($model->ordertype===$type->id) ? 'active' : '';?>"><?php echo $type->name;?></button>
									</div>
								<?php endforeach;?>
							</div>		
						</div>
						<div class="row-margin" id="shipment-group">
							<div class="btn-group btn-group-justified" id="switch-shipment-type" role="group" aria-label="Shipment types" style="display:none;">
								<?php foreach(ShipmentType::find()->all() as $key=>$type) :?>
									<div class="btn-group" role="group">
								     	<button type="button" data-switch-set="shipmenttype" data-switch-value="<?php echo strtolower($type->name);?>" class="btn btn-default <?php echo ($model->type===$type->id) ? 'active' : '';?>"><?php echo $type->name;?></button>
									</div>
								<?php endforeach;?>
							</div>
						</div>					
					</div>
                    <div class="row row-margin">
                    <?php if($model->isNewRecord) :?>
                        <div class="well well-sm">
                            <label for="selectItem">Step #4 - <small>Add An Item:</small></label>
                            <div id="entry1" class="clonedInput">
                                <div class="row">
                                    <div class="col-sm-1">
                                        <button class="btn btn-success btn-xs btnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                                    </div>
                                    <div class="col-sm-1 qty-group">
                                        <input class="select_ttl form-control" type="text" name="quantity[]" id="quantity_1" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-sm-8 desc-group">
                                        <div class="input-group input-button-left">
                                            <span class="input-group-btn">
                                                <button class="btn btn-success edit_item_button" id="Edit_1" type="button" style="display:none;"><span class="glyphicon glyphicon-pencil"></span></button>
                                            </span>
                                            <input class="typeahead form-control input_fn" type="text" style="border-radius:3px 0 0 3px;" name="description[]" id="autocompleteitem_1" placeholder="Select an Item" data-provide="typeahead" autocomplete="off" disabled>
                                            <input class="form-control input_h" type="hidden" name="modelid[]" id="autocompletevalitem_1" />
                                            <select class="form-control selectedItems" style="display:none;" name="modelsid[]" id="item_s-1">
                                                <option selected="selected" value="">Select An Item</option>
                                            </select>
                                            <span class="input-group-btn">
                                                <button class="btn btn-success input_sr" id="showRequestItem_1" style="border-radius: 0 3px 3px 0" data-toggle="tooltip" title="Having trouble finding your item? Request help." type="button" >+</button>
                                                <button class="btn btn-success configuration_item_button" id="Config_1" type="button" style="display:none;"><span class="glyphicon glyphicon-wrench"></span></button>
                                                <button class="btn btn-success comment_item_button" id="Comment_1" type="button" style="display:none;"><span class="glyphicon glyphicon-comment"></span></button>
                                            </span>
                                        </div>
                                        <div id="item-available-in-stock-1" class="itemqtystock" style="color:red;font-weight:bold;"></div>
                                    </div>
                                    <div class="col-sm-2 price-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">$</div>
                                            <input class="form-control priceorder" type="text" name="price[]" id="price_1" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div ><textarea class="comment form-control"  id="itemNote_1" placeholder="Add additional notes or instructions..." style="display: none;" rows="1" name="itemnotes[]" ></textarea></div>
                                <div class="warehousing-panel" style="display: none;">
                                    <div class="row-margin">
                                        <div class="title">Package Option :</div>
                                        <div class="btn-group item-options" id="package-option" data-toggle="buttons">
                                            <?php foreach (OrderPackageOptoin::find()->all() as $option) :?>
                                                <div class="item-option">
                                                    <input class="package_option" type="radio" value="<?php echo $option->id;?>" name="package_option[1][]">
                                                    <div class="option-title"><?php echo $option->name;?></div>
                                                </div>
                                            <?php endforeach;?>
                                        </div>

                                        <div class="title">Testing Options :</div>
                                        <div class="btn-group item-options" id="testing-options" data-toggle="buttons">
                                            <?php foreach (ModelOption::find()->where(['optiontype'=>3, 'level'=>1])->all() as $option) :?>
                                                <div class="item-option">
                                                    <input class="testing_option" type="checkbox" value="<?php echo $option->id;?>" name="testing_option[1][]">
                                                    <div class="option-title"><?php echo $option->name;?></div>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>

                                    <div class="row-margin clear">
                                        <div class="title">Cleaning Options :</div>
                                        <div class="btn-group item-options" id="cleaning-options" data-toggle="buttons">
                                            <?php foreach (ModelOption::find()->where(['optiontype'=>1, 'level'=>1])->all() as $option) :?>
                                                <div class="item-option">
                                                    <input class="cleaning_option" type="checkbox" value="<?php echo $option->id;?>" name="cleaning_option[1][]">
                                                    <div class="option-title"><?php echo $option->name;?></div>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>

                                </div>
                                <div class="configuration-options row" id="configuration_options1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <button class="btn btn-success btn-xs" id="btnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                                <button class="btn btn-success btn-xs" id="btnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                            </div>
                            <div id="item-count-order-txt" class="col-md-3 text-right"><b><span id="item-count-order">Total : $0.00</span></b></div>
                        </div>
                        </div>
                    </div>
							<?php else : ?>
								<div class="well well-sm">
									<label for="selectItem">Step #3 - <small>Add An Item:</small></label>
								<?php 
									$i = 1;
									$items = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();
								?>	
								<?php foreach($items as $item) : ?>
									<?php 
										$qty = null;
										$price = null;
										$_model = Models::findOne($item->model);
										$item_name = Manufacturer::findOne($_model->manufacturer)->name . ' ' . $_model->descrip;
										$is_picked = false;
										$qty = $item->qty;
										$price = $item->price;
										$picked_items = Item::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model, 'status'=>array_search('Picked', Item::$status)])->count();
										if($picked_items===$qty)
											$is_picked = true;
									?>
									<div id="entry<?= $i;?>" class="clonedInput">
										<div class="row form-group">
											<div class="col-sm-1 qty-group">
											<?php if(!$is_picked) :?>
												<input class="select_ttl form-control" type="text" name="quantity[]" id="quantity_<?= $i;?>" value="<?php echo $qty;?>" placeholder="Qty">
											<?php else :?>
												<b><?php echo $qty;?></b>
											<?php endif;?>
											</div>
											<div class="col-sm-9 desc-group">
												<span class="input-group-btn"> 
													<button class="btn btn-success edit_item_button" id="Edit_<?= $i;?>" type="button" style="display:none;"><span class="glyphicon glyphicon-pencil"></span></button>
												</span>												
												<div class="input-group">
													<?php if(!$is_picked) :?>
														<input class="form-control input_fn" type="text" name="description[]" id="autocompleteitem_<?= $i;?>" value="<?php echo $item_name;?>" placeholder="Select an Item" data-provide="typeahead" autocomplete="off">
														<input class="form-control input_h" type="hidden" name="modelid[]" id="autocompletevalitem_<?= $i;?>" value="<?php echo $_model->id;?>"/>													
														<select class="form-control selectedItems" style="display:none;" name="modelsid[]" id="item_s-<?= $i;?>">
															<option selected="selected" value="">Select An Item</option>
														</select>														
														<span class="input-group-btn">
															<button class="btn btn-success input_sr" id="showRequestItem_<?= $i;?>" data-toggle="tooltip" title="Having trouble finding your item? Request help." type="button" >+</button>
															<button class="btn btn-success comment_item_button" id="Comment_<?= $i;?>" type="button"><span class="glyphicon glyphicon-comment"></span></button>
														</span>
													<?php else :?>
														<b><?php echo $item_name;?>  (already picked)</b>
													<?php endif;?>
												</div>
												<div id="item-available-in-stock-<?= $i;?>" class="itemqtystock" style="color:red;font-weight:bold;"></div>	
											</div>
											<div class="col-sm-2 price-group">
												<div class="input-group">
												<?php if(!$is_picked) :?>
													<div class="input-group-addon">$</div>
													<input class="form-control priceorder" type="text" name="price[]" id="price_<?= $i;?>" value="<?php echo $price;?>" placeholder="3.77">
												<?php else :?>
													<b><?php echo $price;?></b>
												<?php endif;?>												
												</div> 
											</div>
										</div>	
										<?php if(!empty(Item::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->one()->notes)) :?>
											<div ><textarea class="comment form-control"  id="itemNote_<?= $i;?>" placeholder="Add additional notes or instructions..." rows="1" name="itemnotes[]" ></textarea></div>
										<?php endif;?>
										<div class="warehousing-panel" style="display: none;">		
											<div class="row-margin">
												<span>
													<b>Package Option :</b>
													<div class="btn-group" id="package-option" data-toggle="buttons">
														<?php foreach (OrderPackageOptoin::find()->all() as $option) :?>
	                                                        <input class="package_option" type="radio" value="<?php echo $option->id;?>" name="package_option[1][]"> <?php echo $option->name;?>
														<?php endforeach;?>
													</div>		
												</span>	
											</div>			
											<div class="row-margin">					
												<b>Cleaning Options :</b>
												<div class="btn-group" id="cleaning-options" data-toggle="buttons">
													<?php foreach (ModelOption::find()->where(['optiontype'=>1, 'level'=>1])->all() as $option) :?>
                                                        <input class="cleaning_option" type="checkbox" value="<?php echo $option->id;?>" name="cleaning_option[1][]"> <?php echo $option->name;?>
													<?php endforeach;?>
												</div>		
												<b>Testing Options :</b>
												<div class="btn-group" id="testing-options" data-toggle="buttons">											
													<?php foreach (ModelOption::find()->where(['optiontype'=>3, 'level'=>1])->all() as $option) :?>
                                                        <input class="testing_option" type="checkbox" value="<?php echo $option->id;?>" name="testing_option[1][]"> <?php echo $option->name;?>
													<?php endforeach;?>
												</div>									
											</div>																									
										</div>
										<div class="configuration-options row row-margin" id="configuration_options<?= $i;?>"></div>										
									</div>
									<?php $i++;?>
								<?php endforeach;?>
								<div id="item-count-order-txt" class="row text-right"><b><span id="item-count-order">Total : $0.00</span></b></div>
								<div class="row">
									<div class="actions">
										<button class="btn btn-success btn-xs" id="btnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
										<button class="btn btn-success btn-xs" id="btnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
									</div>
								</div>								
								</div>
							<?php endif;?>
						</div>
						<div class="row-margin"></div>
						<div class="row">
							<div class="col-md-12 text-right">
								<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-danger', 'onClick'=>'redirectOrders();']) ?>
								<?= Html::submitButton('<i class="fa fa-quote-left"></i> Save as Quote',[ 'name'=>'saveQuote', 'value' => 'saveQuote', 'class' => 'btn btn-warning']) ?>
								<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create',[ 'name'=>'saveOrder', 'value' => 'saveOrder', 'class' => 'btn btn-success']) ?>							
							</div>
						</div>											
			</div>
		</div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div> 
</div>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/order_create.js"></script>
	 <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/location.js"></script>
<?php //LOAD LOCATION ADD FORM --->?>
<?= $this->render("_modals/_addlocation");?>
<?php //LOAD REQUEST ITEM FORM --->?>
<?php /* $this->render("_modals/_requestitem");*/?>
<?php //LOAD ADD MODEL FORM --->?>
<?= $this->render("_modals/_newmodel");?>