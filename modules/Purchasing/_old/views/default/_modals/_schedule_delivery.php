<?php 
	use yii\widgets\ActiveForm;	
?>

<div class="modal fade" id="ScheduleDelivery" tabindex="-1" role="dialog" aria-labelledby="ScheduleDeliveryLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-dashboard"></span> Schedule A Delivery : <span id="sch_modelname"></span></h4>
	  </div>
	  <?php $form = ActiveForm::begin(['options' => ['id'=>'add-schedule-delivery-form', 'class' => 'form-group']]); ?>	
		  <div class="modal-body">
		  	<div id="schedule-delivery-content"></div>
		  </div>
		  <div class="modal-footer">
			  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  <button type="submit" class="btn btn-success">Save</button>
		  </div>
	 <?php ActiveForm::end(); ?>
	</div>
  </div>
</div>