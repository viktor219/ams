<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Url;
?>
<div class="modal fade" id="warehouseModal" role="dialog" aria-labelledby="newModelLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> Request An Item: <b><span id="modelname"></span></b></h4>
	  </div>
		<?php $form = ActiveForm::begin(['options' => ['id'=>'warehouse-form', 'class'=>'form-group']]); ?>
			<div class="modal-body">
				<div id="loaded-warehouse-content"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Save</button>
			</div>
		<?php ActiveForm::end(); ?>		
		</div>
	</div>
</div>