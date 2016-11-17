<?php 
	use app\models\User;
	use app\models\ShippingCompany;
	use app\models\ShipmentMethod;
	
?>
<!-- Modal -->
<div class="modal fade" id="addLocation" role="dialog" aria-labelledby="addLocationLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add A New Location</h4>
	  </div>
		<div class="alert alert-success fade in" id="location-msg" style="display:none;"></div>
		<form class="form-group form-group-sm" id="o-add-location-form" >	
			<div class="modal-body">
					<div class="row" <?php if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE):?>style="display:none;"<?php endif;?>>
						<div class="col-sm-4" id="lshippingcompany-group">
							<select class="form-control" id="lshippingcompany" name="lshippingcompany">
								<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
								<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
									<option value="<?php echo $shippingcompany->id;?>"><?php echo $shippingcompany->name;?></option>							  		
								<?php endforeach;?>
							</select>								
						</div>					
						<div class="col-sm-4" id="laccountnumber-group">
							<input type="text" class="form-control" placeholder="Account Number" id="laccountnumber">
						</div>
						<div class="col-sm-4">
								<div class="form-group" id="ldeliverymethod-group">
									<select class="shipping_method_select2_single form-control" id="lshippingmethod" name="lshippingmethod">
										<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
									</select>								
								</div>					
						</div>				
					</div>
				<div class="row">
					<div class="col-sm-6">
						<label class="sr-only" for="storenum"></label>
						<input type="text" class="form-control" id="storenum" placeholder="Store or Tech Number (Optional)">
					</div>
					<div class="col-sm-6">
						<label class="sr-only" for="storename"></label>
						<input type="text" class="form-control" id="storename" placeholder="Store or Tech Name (Optional)">
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-sm-4" id="location_address-group">
						<label class="sr-only" for="address"></label>
						<input type="text" class="form-control" id="location_address" placeholder="Address (Required)">
						<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
					</div>
					<div class="col-sm-4" id="location_secondaddress-group">
						<label class="sr-only" for="address"></label>
						<input type="text" class="form-control" id="location_secondaddress" placeholder="Second Address (Opt)">
						<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
					</div>					
					<div class="col-sm-4" id="location_zip-group">
						<label class="sr-only" for="zip"></label>
						<input type="text" class="form-control location_zip" id="location_zip" placeholder="Zip (Required)">
					</div>					
				</div>
				<br/>
					<div class="row">
						<div class="col-sm-4" id="location_country-group">
							<label class="sr-only" for="country"></label>
							<input type="text" class="form-control location_country" id="location_country" placeholder="Country (Required)">
							<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
						</div>					
						<div class="col-sm-4" id="location_city-group">
							<label class="sr-only" for="city"></label>
							<input type="text" class="form-control location_city" id="location_city" placeholder="City (Required)">
							<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
						</div>
						<div class="col-sm-4" id="location_state-group">
							<label class="sr-only" for="state"></label>
							<input type="text" class="form-control location_state" id="location_state" placeholder="State (Required)">
						</div>
					</div>
				<br/>
				<div class="row">
					<div class="col-sm-6" id="location_email-group">
						<label class="sr-only" for="email"></label>
						<input type="email" class="form-control" id="location_email" placeholder="Email (Optional)">
					</div>
					<div class="col-sm-6">
						<label class="sr-only" for="phone"></label>
						<input type="tel" class="form-control" id="location_phone" placeholder="Phone (Optional)">
					</div>
				</div>
				<input type="hidden" class="form-control" id="l_customer" name="customer_name" />
		  </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			<input type="submit" class="btn btn-success" value="Save" >
		</div>
	</form>
	</div>
  </div>
</div>
<!-- End -->