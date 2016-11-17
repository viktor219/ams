<?php
//TODO : review file content (was coming from popup modal)
use yii\helpers\Html;
use yii\widgets\ActiveForm; 
use app\models\Location;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use app\models\PaymentTerm;

//var_dump($customer_settings);

$_defaultshipping_method = array();
$_secondaryshipping_method = array();
if(!$model->isNewRecord)
{
	$_defaultshipping_method = ShipmentMethod::findOne($customer_settings->default_shipping_method);
	$_secondaryshipping_method = ShipmentMethod::findOne($customer_settings->secondary_shipping_method);
}

?>
<style>
.bootstrap-switch.bootstrap-switch-animate .bootstrap-switch-container {
      transition: margin-left .1s;
}
</style>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<?= $this->render("_modals/_addlocation");?>
<div class="row row-margin">			
<?php $form = ActiveForm::begin(['options' => ['id'=>'create-customer-form', 'enctype' => 'multipart/form-data']]); ?>
	<div class="col-lg-12 col-xs-12">
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
							<h4>Basic Options</h4>
						<div class="row row-margin well well-sm">
							<div class="row" style="margin-bottom:5px;">
								<div class="row row-margin">
									<div id="upload-group" class="col-sm-4">
										<span class="btn btn-default btn-file">
											Upload Logo <input type="file" name="fileToUpload" id="fileToUpload">
										</span>									
									</div>								
									<div id="code-group" class="col-sm-4">
										<label for="code" class="sr-only"></label>
										<input value="<?php if(isset($customer->code)) echo $customer->code;?>" type="text" name="customercode" placeholder="Code (Required)" id="customercode" class="form-control">
										<input type="hidden" id="customer_Id" value="<?php echo $customer->id;?>"/>
									</div>		
										<div id="companyname-group" class="col-sm-4">
										<label for="companyname" class="sr-only"></label>
										<input value="<?php if(isset($customer->companyname)) echo $customer->companyname;?>" type="text" name="companyname" placeholder="Customer or Project Name (Required)" id="companyname" class="form-control">
									</div>
								</div>
								<div class="row row-margin">
									<div id="contactname-group" class="col-sm-4">
										<label for="contactname1" class="sr-only"></label>
										<input value="<?php if(isset($customer->firstname)) echo $customer->firstname;?>" type="text" name="contactname1" placeholder="First Name (Optional)" id="contactname1" class="form-control">
									</div>								
									<div id="contactname-group" class="col-sm-4">
										<label for="contactname2" class="sr-only"></label>
										<input value="<?php if(isset($customer->lastname)) echo $customer->lastname;?>" type="text" name="contactname2" placeholder="Last Name (Optional)" id="contactname2" class="form-control">
									</div>							
									<div id="email-group" class="col-sm-4">
										<label for="email" class="sr-only"></label>
										<input value="<?php if(isset($customer->email)) echo $customer->email;?>" type="email" name="email" placeholder="Email (Required)" id="email" class="form-control">
									</div>
								</div>
								<div class="row row-margin">
									<!--<div id="fax-group" class="col-sm-4">
										<label for="fax" class="sr-only"></label>
										<input value="<?php if(isset($customer->fax)) echo $customer->fax;?>" type="text" name="fax" placeholder="Fax (Optional)" id="phone" class="form-control">
									</div>-->
									<div id="phone-group" class="col-sm-4">
										<label for="phone" class="sr-only"></label>
										<input value="<?php if(isset($customer->phone)) echo $customer->phone;?>" type="tel" name="phone" placeholder="Phone (Optional)" id="phone" class="form-control">
									</div>									
									<div id="website-group" class="col-sm-4">
										<label for="website" class="sr-only"></label>
										<input value="<?php if(isset($customer->website)) echo $customer->website;?>" type="url" name="website" placeholder="Website (Optional)" id="phone" class="form-control">
									</div>								
									<div class="col-sm-4">
										<label class="sr-only" for="parentId"></label>
										<select class="form-control" id="parentId" name="parentId">
											<option value="" disabled="" selected="">Select a parent Customer or Project</option>
											<?php foreach ($projects as $project) :?>
												<option <?php if(isset($customer->parent_id) && $customer->parent_id == $project->parent_id) echo 'selected';?> value="<?php echo $project->id;?>"><?php echo $project->companyname;?></option>
											<?php endforeach;?>
										</select>
									</div>	
								</div>
							</div>
							<div class="row-margin"></div>
							<?php /*
							<div class="row">
								<div class="col-sm-12">
									<h6><strong>Default Shipping Address:</strong></h6>
								</div>
							</div>
							<div class="row">
								<div id="shipping_address-group" class="col-sm-6">
									<label for="address" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->address)) echo $locationShipping->address;?>" type="text" placeholder="Address" name="shipping_address" id="shipping_address" class="form-control">
								</div>
								<div id="shipping_address2-group" class="col-sm-3">
									<label for="address" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->address2)) echo $locationShipping->address2;?>" type="text" placeholder="Address 2 (Optional)" name="shipping_address_2" id="shipping_address_2" class="form-control">
								</div>					        
								<div id="shipping_zip-group" class="col-sm-3">
									<label for="zip" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->zipcode)) echo $locationShipping->zipcode;?>" type="text" placeholder="Zip" name="shipping_zip" id="shipping_zip" class="form-control location_zip">
								</div>					        
							</div>
							<div class="row-margin"></div>
							<div class="row">
								<div id="shipping_country-group" class="col-sm-6">
									<label for="country" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->country)) echo $locationShipping->country;?>" type="text" placeholder="Country" name="shipping_country" id="shipping_country" class="form-control location_country">
								</div>					    
								<div id="shipping_city-group" class="col-sm-3">
									<label for="city" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->city)) echo $locationShipping->city;?>" type="text" placeholder="City" name="shipping_city" id="shipping_city" class="form-control location_city">
								</div>
								<div id="shipping_state-group" class="col-sm-3">
									<label for="state" class="sr-only"></label>
									<input value="<?php if(isset($locationShipping->state)) echo $locationShipping->state;?>" type="text" placeholder="State" name="shipping_state" id="shipping_state" class="form-control location_state">
								</div>
							</div>
							<div class="row-margin"></div>
							<input type="hidden" value="0" id="billing_required">
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<h6><strong>Default Billing Address:</strong> <small>(If different from shipping address.)</small></h6>
									</div>
								</div>
							</div>*/?>
							<div class="row-margin"></div>
							<input type="hidden" name="customerId" value="<?php if(isset($customer->id)) echo $customer->id;?>" />
							<input type="hidden" name="dshippingId" value="<?php if(isset($locationShipping->id)) echo $locationShipping->id;?>" />
							<input type="hidden" name="dbillingId" value="<?php if(isset($locationBilling->id)) echo $locationBilling->id;?>" />
						</div>
						<h4>Billing Options</h4>
						<div class="row row-margin well well-sm">		
								<div class="row"> <!-- row 0 -->
									<div class="col-md-4 form-group">
										<label for="">Choose the payment terms :</label>
										<select class="form-control" id="paymentterms" name="payment_terms" >
										<option value="">Select A Payment Term</option>
											<?php foreach(PaymentTerm::find()->all() as $payment_term) :?>
												<option <?php if($customer->payment_terms_id==$payment_term->id) : ?>selected="selected"<?php endif;?> value="<?php echo $payment_term->id;?>"><?php echo $payment_term->description;?> (<?php echo $payment_term->code;?>)</option>
											<?php endforeach;?>
										</select>
									</div>								  
								</div>
								<div class="row">
									<div id="billing_address-group" class="col-sm-4">
										<label for="address" class="sr-only"></label>
										<input value="<?php if(isset($locationBilling->address)) echo $locationBilling->address;?>" type="text" placeholder="Address (Required)" name="billing_address" id="billing_address" class="form-control">
									</div>
									<div id="billing_address2-group" class="col-sm-4">
										<label for="address" class="sr-only"></label>
										<input value="<?php if(isset($locationBilling->address2)) echo $locationBilling->address2;?>" type="text" placeholder="Address 2 (Optional)" name="billing_address_2" id="billing_address_2" class="form-control">
									</div>					            
									<div id="billing_zip-group" class="col-sm-4">
										<label for="zip" class="sr-only"></label>
										<input type="text" value="<?php if(isset($locationBilling->zipcode)) echo $locationBilling->zipcode;?>" placeholder="Zip (Required)" name="billing_zip" id="billing_zip" class="form-control location_zip">
									</div>					            
								</div>
								<div class="row-margin"></div>
								<div class="row">
									<div id="billing_country-group" class="col-sm-4">
										<label for="country" class="sr-only"></label>
										<input value="<?php if(isset($locationBilling->country)) echo $locationBilling->country;?>" type="text" placeholder="Country (Required)" name="billing_country" id="billing_country" class="form-control location_country">
									</div>					        
									<div id="billing_city-group" class="col-sm-4">
										<label for="city" class="sr-only"></label>
										<input value="<?php if(isset($locationBilling->city)) echo $locationBilling->city;?>" type="text" placeholder="City (Required)" name="billing_city" id="billing_city" class="form-control location_city">
									</div>
									<div id="billing_state-group" class="col-sm-4">
										<label for="state" class="sr-only"></label>
										<input value="<?php if(isset($locationBilling->state)) echo $locationBilling->state;?>" type="text" placeholder="State (Required)" name="billing_state" id="billing_state" class="form-control location_state">
									</div>
								</div>
						</div>
						<h4>Advanced Options</h4>
						<div class="row row-margin well well-sm">		
							<div class="form-group">	
								<div class="col-sm-6">	
									<label for="allownewcustomerorder" class="checkbox-inline" style="padding-left:0px;">Allow new orders to be created by the customer ?</label>	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="allownewcustomerorder" name="allownewcustomerorder" <?php echo ($customer->allownewcustomerorder==1) ? "checked" : '';?>>
								</div>
								<div class="col-sm-6">					 
									<label for="allowdirectshippingreq" class="checkbox-inline" style="padding-left:0px;">Allow priority direct shipment requests by model and item ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="allowdirectshippingreq" name="allowdirectshippingreq" <?php echo ($customer->allowdirectshippingreq==1) ? "checked" : '';?>>
								</div>								
							</div>
							<div class="row row-margin"></div>
							<div class="form-group">	
								<div class="col-sm-6">					 
									<label for="allowweeklyautorderreq" class="checkbox-inline" style="padding-left:0px;">Allow weekly automatic “Hot Item” order requests ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="allowweeklyautorderreq" name="allowweeklyautorderreq" <?php echo ($customer->allowweeklyautorderreq==1) ? "checked" : '';?>>
								</div>
								<div class="col-sm-6">					 
									<label for="allowincomingoutshchedule" class="checkbox-inline" style="padding-left:0px;">Allow incoming and outgoing schedules to be created ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="allowincomingoutshchedule" name="allowincomingoutshchedule" <?php echo ($customer->allowincomingoutshchedule==1) ? "checked" : '';?>>
								</div>								
							</div>		
							<div class="row row-margin"></div>
							<div class="form-group">
								<div class="col-sm-6">					 
									<label for="customerstoreinventory" class="checkbox-inline" style="padding-left:0px;">Does this customer store their inventory with us?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="customerstoreinventory" name="customerstoreinventory" <?php echo ($customer->customerstoreinventory==1) ? "checked" : '';?>>
								</div>								
							</div>							
						</div>	
						<h4>Receiving Options</h4>
						<div class="row row-margin well well-sm">
							<div class="form-group">	
								<div class="col-sm-6">
									<label for="defaultreceivinglocation" class="sr-only"></label>
									<select class="receiving_select2_single form-control inputs" name="defaultreceivinglocation" <?php if($model->isNewRecord) :?> disabled <?php endif;?>>
										<option value="">Select A Location</option>
											<?php foreach(Location::find()->where(['customer_id'=>4])->all() as $location) :?>
												<?php 
													$output = "";
													if(!empty($location->storenum))
														$output .= "Store#: " . $location->storenum . " - ";
													if(!empty($location->storename))
														$output .= $location->storename  . ' - '; 
													//
													$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
												?>
												<option <?php if($customer->defaultreceivinglocation===$location->id) : ?>selected="selected"<?php endif;?> value="<?php echo $location->id;?>"><?php echo $output;?></option>
											<?php endforeach;?>
									</select>								
								</div>		
								<div class="col-sm-6">					 
									<label for="temporaryinventorystatus" class="checkbox-inline" style="padding-left:0px;">Add items to temporary inventory first ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="temporaryinventorystatus" name="temporaryinventorystatus" <?php echo ($customer->temporaryinventorystatus==1) ? "checked" : '';?>>
								</div>								
							</div>
							<div class="row row-margin"></div>
							<div class="form-group">	
								<div class="col-sm-6">					 
									<label for="requireserialnumber" class="checkbox-inline" style="padding-left:0px;">Add serial numbers when receiving items ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requireserialnumber" name="requireserialnumber" <?php echo ($customer->trackincomingserials==1) ? "checked" : '';?>>
								</div>	
								<div class="col-sm-6">					 
									<label for="requirestorenumber" class="checkbox-inline" style="padding-left:0px;">Add an incoming store number to all items ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirestorenumber" name="requirestorenumber" <?php echo ($customer->requirestorenumber==1) ? "checked" : '';?>>
								</div>								
							</div>
							<div class="row row-margin"></div>
							<div class="form-group">	
								<div class="col-sm-6">	
									<label for="requirepalletcount" class="checkbox-inline" style="padding-left:0px;">Count pallets as they arrive ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirepalletcount" name="requirepalletcount" <?php echo ($customer->requirepalletcount==1) ? "checked" : '';?>>
								</div>								
								<div class="col-sm-6">					 
									<label for="requireboxcount" class="checkbox-inline" style="padding-left:0px;">Count boxes as they arrive ?</label>																	
									<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requireboxcount" name="requireboxcount" <?php echo ($customer->requireboxcount==1) ? "checked" : '';?>>
								</div>									
							</div>	
						</div>		
						<h4>Picking Options</h4>
						<div class="row row-margin well well-sm">
							<label for="requirelanenumber" class="checkbox-inline" style="padding-left:0px;">Add a lane number to specific serialized models ?</label>							
							<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirelanenumber" name="requirelanenumber" <?php  echo ($customer->requirelanenumber==1) ? "checked" : '';?>>
						</div>
						<h4>Shipping Options</h4>
						<div class="row row-margin well well-sm">
							<div class="row" style="margin-bottom:5px;">
								<!--<div class="row">
									<input id="applyowncustomerdetails" type="checkbox"> Apply Our details				
								</div>-->	
								<div class="row row-margin"> <!-- row 0 -->
									<div class="col-md-4 form-group">
									<label for="">Choose the default shipping method for this customer :</label>
										<!--<div class="btn-group btn-group-justified" id="switch-shipping-detail-tab" role="group" aria-label="Shipping Details">
											 <div class="btn-group" role="group">
												<button type="button" data-switch-set="shippingdetail" data-switch-value="1" class="btn btn-dark" data-toggle="tooltip" title="Set Asset Shipping method as default shipping option.">Asset</button>
											 </div>
											 <div class="btn-group" role="group">
												<button type="button" data-switch-set="shippingdetail" data-switch-value="2" class="btn btn-dark" data-toggle="tooltip" title="Set Customer Shipping method as default shipping option.">Customer</button>
											 </div>
											 <div class="btn-group" role="group">
												<button type="button" data-switch-set="shippingdetail" data-switch-value="3" class="btn btn-dark" data-toggle="tooltip" title="Set Other Shipping method as default shipping option.">Other</button>	
											 </div>
										</div>	-->
										<select class="form-control" id="defaultshippingchoice" name="defaultshippingchoice" >
											<option value="1">Asset</option>
											<option value="2">Customer</option>
											<option value="3">Other</option>
										</select>
									</div>								  
								</div>
								<div class="row row-margin"> <!-- row 1 -->
									<div class="col-sm-4 form-group" id="c1-shippingcompany-group">
										<select class="c1_shipping_company_select2_single form-control" id="c1_shippingcompany" name="c1_shippingcompany">
											<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
											<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
												<option value="<?php echo $shippingcompany->id;?>" <?php echo ($_defaultshipping_method->shipping_company_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
											<?php endforeach;?>
										</select>		
									</div>	
									<div id="defaultaccountnumber-group" class="col-sm-4">
										<label for="code" class="sr-only"></label>
										<input value="<?php if(isset($customer_settings->default_account_number)) echo $customer_settings->default_account_number;?>" type="text" name="defaultaccountnumber" placeholder="Customer Account Number (Required)" id="defaultaccountnumber" class="form-control">
									</div>
									<div id="defaultshippingmethod-group" class="col-sm-4">
										<label for="code" class="sr-only"></label>
										<?php if(empty($customer->id) || empty($customer_settings->default_shipping_method)) :?>
											<select class="shipping_method_select2_single form-control" id="defaultshippingmethod" name="defaultshippingmethod">
												<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
											</select>	
										<?php else :?>
											<select class="shipping_method_select2_single form-control" id="defaultshippingmethod" name="defaultshippingmethod">
												<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
												<?php foreach(ShipmentMethod::findAll(['shipping_company_id'=>$_defaultshipping_method->shipping_company_id]) as $shipmethod) :?>
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
													<option value="<?php echo $shipmethod->id;?>" <?php echo ($_defaultshipping_method->id===$shipmethod->id) ? 'selected' : '';?>><?php echo $__shipping_method;?></option>
												<?php endforeach;?>
											</select>								
										<?php endif;?>					           
									</div>  																		
								</div> 		
								<div class="row row-margin"> <!-- row 2 -->
									<div class="col-sm-4 form-group" id="c2-shippingcompany-group">							
										<select class="c2_shipping_company_select2_single form-control" id="c2_shippingcompany" name="c2_shippingcompany">
											<option selected="selected" disabled="disabled" value="">Select Secondary Shipping Company</option>
											<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
												<option value="<?php echo $shippingcompany->id;?>" <?php echo ($_secondaryshipping_method->shipping_company_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
											<?php endforeach;?>
										</select>	
									</div>	
									<div id="secondaryaccountnumber-group" class="col-sm-4">
										<input value="<?php if(isset($customer_settings->secondary_account_number)) echo $customer_settings->secondary_account_number;?>" type="text" class="form-control" name="secondaryaccountnumber" id="secondaryaccountnumber" placeholder="Other Account Number (Optional)">
									</div> 									
									<div id="secondaryshippingmethod-group" class="col-sm-4 form-group">
										<label for="code" class="sr-only"></label>
										<?php if(empty($customer->id) || empty($customer_settings->secondary_shipping_method)) :?>
											<select class="shipping_method_select2_single form-control" id="secondaryshippingmethod" name="secondaryshippingmethod">
												<option selected="selected" disabled="disabled" value="">Select Secondary Shipping Method</option>
											</select>
										<?php else :?>	
											<select class="shipping_method_select2_single form-control" id="secondaryshippingmethod" name="secondaryshippingmethod">
												<option selected="selected" disabled="disabled" value="">Select Secondary Shipping Method</option>
												<?php foreach(ShipmentMethod::findAll(['shipping_company_id'=>$_secondaryshipping_method->shipping_company_id]) as $shipmethod) :?>
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
													<option value="<?php echo $shipmethod->id;?>" <?php echo ($_secondaryshipping_method->id===$shipmethod->id) ? 'selected' : '';?>><?php echo $__shipping_method;?></option>
												<?php endforeach;?>										
											</select>								
										<?php endif;?>									
									</div>  									
								</div>					  	
								<div class="row row-margin"> <!-- row 3 -->
									<div id="defaultshippinglocation-group" class="col-sm-4">
										<label for="">Please select a default shipping location for this customer:</label>
										<div class="input-group"> 
											<span class="input-group-btn">
												<button type="button" class="btn btn-dark btn-md" id="add-shipping-location"><span class="glyphicon glyphicon-plus"></span></button>
											</span>										
											<select class="default_shipping_select2_single form-control inputs" name="shippinglocation" id="selectShippinglocation">
												<option value="">Select A Location</option>
											</select>	
										</div>
									</div>  								
									<div class="col-sm-4">					 
										<label for="billcustomershipping" class="checkbox-inline" style="padding-left:0px;">Bill customer for shipping?</label>																	
										<br/><input type="checkbox" data-on-text="Yes" data-off-text="No" id="billcustomershipping" name="billcustomershipping" <?php echo ($customer->billcustomershipping==1) ? "checked" : '';?>>
									</div>																		
								</div>   
								<div class="row row-margin"> <!-- row 4 -->		
									<div class="col-sm-4">					 
										<label for="requirelabelmodel" class="checkbox-inline" style="padding-left:0px;">Add customer specific labels for each model ?</label>																	
										<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirelabelmodel" name="requirelabelmodel" <?php echo ($customer->requirelabelmodel==1) ? "checked" : '';?>>
									</div>
									<div class="col-sm-4">					 
										<label for="requirelabelbox" class="checkbox-inline" style="padding-left:0px;">Add customer specific labels on each box ?</label>																	
										<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirelabelbox" name="requirelabelbox" <?php echo ($customer->requirelabelbox==1) ? "checked" : '';?>>
									</div>								
									<div class="col-sm-4">					 
										<label for="requirelabelpallet" class="checkbox-inline" style="padding-left:0px;">Add customer specific labels on each pallet ?</label>																	
										<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requirelabelpallet" name="requirelabelpallet" <?php echo ($customer->requirelabelpallet==1) ? "checked" : '';?>>
									</div>									
								</div>        	        
							</div>						
						</div>
					</div>
					<script>							
					    $("[name='billcustomershipping']").bootstrapSwitch("size", "mini");
					    $("[name='customerstoreinventory']").bootstrapSwitch("size", "mini");
					    $("[name='requireordernumber']").bootstrapSwitch("size", "mini");
					    $("[name='requireserialnumber']").bootstrapSwitch("size", "mini");
					    $("[name='allownewcustomerorder']").bootstrapSwitch("size", "mini");
					    $("[name='allowdirectshippingreq']").bootstrapSwitch("size", "mini");
					    $("[name='allowweeklyautorderreq']").bootstrapSwitch("size", "mini");
					    $("[name='allowincomingoutshchedule']").bootstrapSwitch("size", "mini");
					    $("[name='temporaryinventorystatus']").bootstrapSwitch("size", "mini");
					    $("[name='requirestorenumber']").bootstrapSwitch("size", "mini");
					    $("[name='requirepalletcount']").bootstrapSwitch("size", "mini");
					    $("[name='requireboxcount']").bootstrapSwitch("size", "mini");
					    $("[name='requirelanenumber']").bootstrapSwitch("size", "mini");
					    $("[name='requirelabelmodel']").bootstrapSwitch("size", "mini");
					    $("[name='requirelabelbox']").bootstrapSwitch("size", "mini");
					    $("[name='requirelabelpallet']").bootstrapSwitch("size", "mini");
					</script>
					
						<div class="row-margin"></div>
						<div class="row">
							<div class="col-md-12 text-right">
								<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-primary', 'onClick'=>'redirectCustomers();']) ?>
								<?= Html::submitButton(($customer->isNewRecord) ? '<span class="glyphicon glyphicon-save"></span> Create' : '<span class="glyphicon glyphicon-edit"></span> Update', ['class' => ($customer->isNewRecord) ? 'btn btn-success' : 'btn btn-warning']) ?>
							</div>
						</div>					
			</div>
		</div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div> 					
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/customer.js"></script>