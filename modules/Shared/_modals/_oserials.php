<?php 
	use yii\widgets\ActiveForm;
?>
<div class="modal fade" id="addSerials" tabindex="-1" role="dialog" aria-labelledby="addSerialsLabel" style="z-index: auto important;">
  <div class="modal-dialog" role="document">
  <?php $form = ActiveForm::begin(['options' => ['id'=>'add-receiving-serial-form']]); ?> 
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add Serial Number</h4>
	  </div>
	  <div class="alert alert-success fade in" id="serial-msg" style="display:none;"></div>
			<div class="modal-body">	  				
				<div id="serialsInput"></div>				
			</div>
			<div class="modal-footer">
				<div id="current_serial_description"><b>You have added <span class="countserializeditems"></span> of <span class="countnotserializeditems"></span> to this inventory</b></div>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>	
	</div>
	<?php ActiveForm::end(); ?>
  </div>
</div>