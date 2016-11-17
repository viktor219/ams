<?php 
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
?>

<!-- Modal -->
<div class="modal fade" id="boxDimension" tabindex="-1" role="dialog" aria-labelledby="newModelLabel" style="z-index:10000">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> <span id="box-actions">Add</span> Weight & Dimensions : <span id="dim_box_model_name"></span></h4>
	  </div>
		<div class="alert alert-success fade in" id="box-dimension-msg" style="display:none;"></div>
			<?php $form = ActiveForm::begin(['action'=>['/shipping/saveboxdimension'], 'options' => ['id'=>'box-dimension-form', 'class'=>'form-group form-group-sm']]); ?>
				<div class="modal-body">
					<div id="load-box-dimension-form-content"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success" id="save_box_dimensions">Save</button>
				</div>
			<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>