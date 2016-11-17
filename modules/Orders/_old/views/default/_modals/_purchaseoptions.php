<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="modal fade" id="addOptions" tabindex="-1" role="dialog" aria-labelledby="addOptionsLabel">
  <div class="modal-dialog" role="document">
  	<?php $form = ActiveForm::begin(['action'=>Url::to(['/orders/addpurchaseoptions']), 'options' => ['id'=>'add-purchase-option-form']]); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add Options : <span id="item-name"></span></h4>
			</div>
			<div class="alert alert-success fade in" id="purchase-option-msg" style="display:none;"></div>
			<div class="modal-body">
				<div id="options-loaded-content"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" ><span class="glyphicon glyphicon-remove"></span> Close</button>
				<button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-edit"></span> Update</button>
			</div>
	  	</div>
  	<?php ActiveForm::end(); ?>
</div>
</div>