<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Url;
?>
<div class="modal fade" id="refurbishModal" role="dialog" aria-labelledby="newModelLabel" style="z-index: 1400;">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> Refurbish</h4>
	  </div>
		<div class="alert alert-success fade in" id="newmodel-msg" style="display:none;"></div>
		<?php $form = ActiveForm::begin(['action'=>Url::to(['/orders/refurbish', 'id'=>$order->id]), 'options' => ['id'=>'refurbish-form', 'class'=>'form-group form-group-sm']]); ?>
			<div class="modal-body">
				<div class="container"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success">Save</button>
			</div>
		<?php ActiveForm::end(); ?>		
		</div>
	</div>
</div>