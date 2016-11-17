<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal fade" id="updateModel" role="dialog" aria-labelledby="updateModelLabel" style="z-index: 1600;">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Edit Model : <span id="selectedModelName"></span></h4>
	  </div>
	  <div class="alert alert-success fade in" id="model-update-msg" style="display:none;text-align:center;font-weight:bold;"></div>
	  	<?php $form = ActiveForm::begin(['options' => ['id'=>'update-model-form']]); ?> 	
			<div class="modal-body" style="padding-top:0;">
				<div id="loadedModelForm"></div>	
			</div>
			<div class="modal-footer">
				<div class="row row-margin">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" value="Save" >
				</div>
			</div>				
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>	