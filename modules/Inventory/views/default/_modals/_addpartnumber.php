<!-- Modal -->
<div class="modal fade" id="addLocation" tabindex="-1" role="dialog" aria-labelledby="addLocationLabel" style="z-index:10000">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add A New Location</h4>
	  </div>
	  <div class="form-group form-group-sm">
		  <div class="modal-body">
			<form>
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
					<div class="col-sm-6">
						<label class="sr-only" for="address"></label>
						<input type="text" class="form-control" id="address" placeholder="Address (Required)">
					</div>
					<div class="col-sm-6">
						<label class="sr-only" for="country"></label>
						<input type="text" class="form-control" id="country" placeholder="Country (Required)">
					</div>
				</div>
				<br/>
					<div class="row">
						<div class="col-sm-6">
							<label class="sr-only" for="city"></label>
							<input type="text" class="form-control " id="city" placeholder="City (Required)">
						</div>
						<div class="col-sm-3">
							<label class="sr-only" for="state"></label>
							<input type="text" class="form-control" id="state" placeholder="State (Required)">
						</div>
						<div class="col-sm-3">
							<label class="sr-only" for="zip"></label>
							<input type="text" class="form-control" id="zip" placeholder="Zip (Required)">
						</div>
					</div>
				<br/>
				<div class="row">
					<div class="col-sm-6">
						<label class="sr-only" for="email"></label>
						<input type="email" class="form-control" id="email" placeholder="Email (Optional)">
					</div>
					<div class="col-sm-6">
						<label class="sr-only" for="phone"></label>
						<input type="tel" class="form-control" id="phone" placeholder="Phone (Optional)">
					</div>
				</div>
			</form>
		  </div>
	  </div>
	  <div class="modal-footer">
		<div class="row">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-success">Save</button>
		</div>
	  </div>
	</div>
  </div>
</div>
<!-- End -->