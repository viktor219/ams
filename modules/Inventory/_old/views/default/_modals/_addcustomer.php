<!-- Modal -->
<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="addCustomerLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add A New Customer</h4>
	  </div>
	  <div class="alert alert-success fade in" id="customer-error" style="display:none;"></div>
	  <form class="form-group form-group-sm" id="add-customer-form">
		  <div class="modal-body">
				<div class="row">
					<div class="col-sm-6" id="companyname-group">
						<label class="sr-only" for="companyname"></label>
						<input type="text" class="form-control" id="companyname" placeholder="Company Name (Required)">
					</div>
					<div class="col-sm-6" id="contactname-group">
						<label class="sr-only" for="contactname"></label>
						<input type="text" class="form-control" id="contactname" placeholder="Contact Name (Optional)">
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-sm-6" id="email-group">
						<label class="sr-only" for="email"></label>
						<input type="email" class="form-control" id="customeremail" placeholder="Email (Required)">
					</div>
					<div class="col-sm-6" id="phone-group">
						<label class="sr-only" for="phone"></label>
						<input type="tel" class="form-control" id="customerphone" placeholder="Phone (Optional)">
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-sm-12">
					<h6><strong>Default Shipping Address:</strong></h6>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6" id="shipping_address-group">
						<label class="sr-only" for="address" ></label>
						<input type="text" class="form-control" id="shipping_address" placeholder="Address (Required)">
					</div>
					<div class="col-sm-6" id="shipping_country-group">
						<label class="sr-only" for="country"></label>
						<input type="text" class="form-control" id="shipping_country" placeholder="Country (Required)">
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-sm-6" id="shipping_city-group">
						<label class="sr-only" for="city"></label>
						<input type="text" class="form-control " id="shipping_city" placeholder="City (Required)">
					</div>
					<div class="col-sm-3" id="shipping_state-group">
						<label class="sr-only" for="state"></label>
						<input type="text" class="form-control" id="shipping_state" placeholder="State (Required)">
					</div>
					<div class="col-sm-3" id="shipping_zip-group">
						<label class="sr-only" for="zip"></label>
						<input type="text" class="form-control" id="shipping_zip" placeholder="Zip (Required)">
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-success btn-xs" id="collapsingbutton-billing" type="button" data-toggle="collapse" data-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample"><span class="glyphicon glyphicon-collapse-down"></span> Add a different billing address.</button>
					</div>   
				</div>
				<input type="hidden" id="billing_required" value="0"/>
				<div class="collapse" id="collapseExample2">
					<div class="row">
						<div class="col-sm-12">
						<h6><strong>Default Billing Address:</strong> <small>(If different from shipping address.)</small></h6>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6" id="billing_address-group">
							<label class="sr-only" for="address"></label>
							<input type="text" class="form-control" id="billing_address" placeholder="Address (Required)">
						</div>
						<div class="col-sm-6" id="billing_country-group">
							<label class="sr-only" for="country"></label>
							<input type="text" class="form-control" id="billing_country" placeholder="Country (Required)">
						</div>
					</div>
					<div class="row-margin"></div>
					<div class="row">
						<div class="col-sm-6" id="billing_city-group">
							<label class="sr-only" for="city"></label>
							<input type="text" class="form-control " id="billing_city" placeholder="City (Required)">
						</div>
						<div class="col-sm-3" id="billing_state-group">
							<label class="sr-only" for="state"></label>
							<input type="text" class="form-control" id="billing_state" placeholder="State (Required)">
						</div>
						<div class="col-sm-3" id="billing_zip-group">
							<label class="sr-only" for="zip"></label>
							<input type="text" class="form-control" id="billing_zip" placeholder="Zip (Required)">
						</div>
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-sm-12">
					<h6><strong>Custom Settings:</strong></h6>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6 text-center">
						<div class="input-group" id="upload_logo-group">
								<span class="input-group-btn">
									<span class="file-input btn btn-success btn-sm btn-file">
										Upload Logo&hellip; <input type="file" id="logo" multiple>
									</span>
								</span>
							<input type="text" class="form-control" readonly>
						</div>                                            
					</div>
				</div>
				<div class="row-margin"></div>
				<div class="row">
					<div class="col-sm-6">
						<label class="sr-only" for="defaultreceivinglocation"></label>
						<input type="email" class="form-control" id="defaultreceivinglocation" placeholder="Default Receiving Location (Optional)">
					</div>
					<div class="col-sm-6">
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