<?php 
	use app\models\Customer;
?>
<!-- Modal -->
<div class="modal fade" id="requestItem" tabindex="-1" role="dialog" aria-labelledby="requestItemLabel" style="z-index:10000">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> Request new Item</h4>
	  </div>
		<div class="alert alert-success fade in" id="itemrequest-msg" style="display:none;"></div>
		<form class="form-group form-group-sm" id="request-item-form" >	
			<div class="modal-body">
				<div class="container">
					<div class="row" id="r_customer-group">
						<label for="customer">Customer</label>
						<select class="form-control" id="selectCustomer" style="width:260px;line-height: 32px;">
						<?php foreach(Customer::find()->where(['owner_id'=>Yii::$app->user->id])->all() as $customer) :?>
							<option value="<?php echo $customer->id;?>"><?php echo $customer->companyname;?></option>
						<?php endforeach;?>
						</select>
					</div>
					<div class="row" id="r_description-group">
						<label for="description">Description</label>
						<textarea class="form-control " id="r_description" rows="3" style="min-height:120px;resize:none;" placeholder="Description (Required)"></textarea>
					</div>
					<div class="row row-margin"></div>
					<div class="row" id="r_manpart-group">
						<label for="manpart">Manufacturer Part Number</label>
						<input type="text" class="form-control" id="r_manpart" placeholder="Manufacturer Part Number (Required)">
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