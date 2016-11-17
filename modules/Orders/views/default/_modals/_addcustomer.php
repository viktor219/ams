<?php 
	use yii\widgets\ActiveForm;
	use dosamigos\fileupload\FileUploadUI;
	use dosamigos\fileupload\FileUpload;
	use app\models\Medias;
	use app\models\ShippingCompany;
	use yii\helpers\Html;
	
?>
<div class="alert alert-success fade in" id="customer-error" style="display:none;"></div>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-group form-group-sm', 'id'=>'add-customer-form', 'style'=>'display:none;']]) ?>
	<div class="col-md-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> New Customer</h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">  
		  <div class="modal-body">
				<div class="row row-margin">
					<div class="col-md-4 col-sm-6 col-xs-12 form-group has-feedback" id="companyname-group">
						<input type="text" class="form-control" id="companyname" placeholder="Company Name (Required)">
						<span class="fa fa-building form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-4" id="firstname-group">
						<input type="text" class="form-control" id="firstname" placeholder="Firstname (required)">
						<span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-4" id="lastname-group">
						<input type="text" class="form-control" id="lastname" placeholder="Lastname (required)">
						<span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
				<div class="row row-margin">
					<input id="applyowncustomerdetails" type="checkbox"> Apply Our details				
				</div>
				<div class="row row-margin">
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="defaultaccountnumber-group">
						<input type="text" class="form-control" id="defaultaccountnumber" placeholder="Default Account Number (Required)">
					</div>
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="c1-shippingcompany-group">
						<select class="c1_shipping_company_select2_single form-control" id="c1_shippingcompany" name="c1_shippingcompany">
							<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
							<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
								<option value="<?php echo $shippingcompany->id;?>" <?php echo ($model->shippingcompany_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
							<?php endforeach;?>
						</select>		
					</div>			
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="defaultshippingmethod-group">
						<select class="shipping_method_select2_single form-control" id="defaultshippingmethod">
							<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
						</select>					
						<!-- <input type="text" class="form-control" id="defaultshippingmethod" placeholder="Default Shipping method (Required)"> -->
					</div>
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="secondaryaccountnumber-group">
						<input type="text" class="form-control" id="secondaryaccountnumber" placeholder="Secondary Account Number (Optionnal)">
					</div>	
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="c2-shippingcompany-group">
						<select class="c2_shipping_company_select2_single form-control" id="c2_shippingcompany" name="c2_shippingcompany">
							<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
							<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
								<option value="<?php echo $shippingcompany->id;?>" <?php echo ($model->shippingcompany_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
							<?php endforeach;?>
						</select>		
					</div>
					<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="secondaryshippingmethod-group">
						<!-- <input type="text" class="form-control" id="secondaryshippingmethod" placeholder="Secondary Shipping method (Optionnal)"> -->
						<select class="shipping_method_select2_single form-control" id="secondaryshippingmethod">
							<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
						</select>						
					</div>								
				</div>
				<div class="row-margin"></div>
				<div class="row row-margin">
					<div class="col-sm-6" id="email-group">
						<input type="email" class="form-control" id="customeremail" placeholder="Email (Required)">
						<span class="fa fa-at form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-6" id="phone-group">
						<input type="tel" class="form-control" id="customerphone" placeholder="Phone (Optional)">
						<span class="fa fa-phone form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row row-margin">
					<div class="col-sm-12">
					<h6><strong>Default Shipping Address:</strong></h6>
					</div>
				</div>
				<div class="row row-margin">
					<div class="col-sm-6" id="shipping_address-group">
						<input type="text" class="form-control" id="shipping_address" placeholder="Address (Required)">
						<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-6" id="shipping_country-group">
						<input type="text" class="form-control location_country" id="shipping_country" placeholder="Country (Required)">
						<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row row-margin">
					<div class="col-sm-6" id="shipping_city-group">
						<input type="text" class="form-control location_city" id="shipping_city" placeholder="City (Required)">
						<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-3" id="shipping_state-group">
						<input type="text" class="form-control location_state" id="shipping_state" placeholder="State (Required)">
					</div>
					<div class="col-sm-3" id="shipping_zip-group">
						<input type="text" class="form-control location_zip" id="shipping_zip" placeholder="Zip (Required)">
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row row-margin">
					<div class="col-md-12">
						<button class="btn btn-success btn-xs" id="collapsingbutton-billing" type="button" data-toggle="collapse" data-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample"><span class="glyphicon glyphicon-collapse-down"></span> Add a different billing address.</button>
					</div>   
				</div>
				<input type="hidden" id="billing_required" value="0"/>
				<div class="collapse" id="collapseExample2">
					<div class="row row-margin">
						<div class="col-sm-12">
						<h6><strong>Default Billing Address:</strong> <small>(If different from shipping address.)</small></h6>
						</div>
					</div>
					<div class="row row-margin">
						<div class="col-sm-6" id="billing_address-group">
							<input type="text" class="form-control" id="billing_address" placeholder="Address (Required)">
							<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
						</div>
						<div class="col-sm-6" id="billing_country-group">
							<input type="text" class="form-control location_country" id="billing_country" placeholder="Country (Required)">
							<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
						</div>
					</div>
					<div class="row-margin"></div>
					<div class="row row-margin">
						<div class="col-sm-6" id="billing_city-group">
							<input type="text" class="form-control location_city" id="billing_city" placeholder="City (Required)">
						</div>
						<div class="col-sm-3" id="billing_state-group">
							<input type="text" class="form-control location_state" id="billing_state" placeholder="State (Required)">
						</div>
						<div class="col-sm-3" id="billing_zip-group">
							<input type="text" class="form-control location_zip" id="billing_zip" placeholder="Zip (Required)">
						</div>
					</div>
				</div>
				<div class="row row-margin">
					<div class="col-sm-12">
					<h6><strong>Custom Settings:</strong></h6>
					</div>
				</div>
				<div class="row row-margin">
					<div class="col-sm-6 text-center">
					    <div class="panel-body demo-panel-files" id='demo-files'>
						  <span class="demo-note">No Files have been selected/droped yet...</span>
						</div>
						<div class="input-group" id="upload_logo-group">
							<div id="drag-and-drop-zone" class="uploader">
								<div class="browser">
									<label>
										<span>Click to open the file Browser</span>
										<input type="file" name="files[]"  accept="image/*" multiple="multiple" title='Click to add Images'>
									</label>
								</div>
							</div>						
						</div>	<!--
						<div class="input-group" id="upload_logo-group">
								<span class="input-group-btn">
									<span class="file-input btn btn-success btn-sm btn-file">
										Upload Logo&hellip; <input type="file" id="logo" multiple>
									</span>
								</span>
							<input type="text" class="form-control" readonly>
						</div>   -->                                         
					</div>
					<div class="col-sm-6 right">
							<div class="checkbox">
								<label class="">
									<div style="position: relative;" class="icheckbox_flat-green checked"><input style="position: absolute; opacity: 0;" class="flat" id="requireordernumber" checked="checked" type="checkbox"><ins style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;" class="iCheck-helper"></ins></div> Require Order Number
								</label>
							</div>		
							<div class="checkbox">
								<label class="">
									<div style="position: relative;" class="icheckbox_flat-green checked"><input style="position: absolute; opacity: 0;" class="flat" id="trackserials" checked="checked" type="checkbox"><ins style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;" class="iCheck-helper"></ins></div> Require Serial Numbers
								</label>
							</div>													
					</div>
				</div>
				<div class="row row-margin">
					<div class="col-sm-6">
						<input type="email" class="form-control" id="defaultreceivinglocation" placeholder="Default Receiving Location (Optional)">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="close-customer-order-form" >Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>
			</div>
			</div>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>
<!-- End -->