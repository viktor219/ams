<?php 
	use app\models\User;
	use app\models\ShippingCompany;
	use app\models\ShipmentMethod;
	use yii\widgets\ActiveForm;
	
?>
<!-- Modal -->
<div class="modal fade" id="saveLocation" role="dialog" aria-labelledby="addLocationLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> <span id="rep_location_title"></span></h4>
	  </div>
		<?php $form = ActiveForm::begin(['options' => ['id'=>'rma-add-location-form', 'class'=>'form-group']]); ?>
			<div class="modal-body">
				<div id="loaded-location-content"></div>		
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