<?php 
	use app\models\User;
	use app\models\ShippingCompany;
	use app\models\ShipmentMethod;
	use yii\widgets\ActiveForm;
	
?>
<!-- Modal -->
<div class="modal fade" id="LocationDetailsModal" role="dialog" aria-labelledby="LocationDetailsModalLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-wrench"></span> <b>Edit Configurations Settings (<span id="edit-configuration-settings"></span>)</b></h4>
	  </div>
		<div class="alert alert-success fade in" id="location-details-msg" style="display:none;"></div>
		<?php $form = ActiveForm::begin(['action'=>['/location/savelocationdetails'], 'options' => ['id'=>'o-add-location-details-form', 'class'=>'form-group']]); ?>
			<div class="modal-body">
				<div id="location-details-content-form"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>
<!-- End -->