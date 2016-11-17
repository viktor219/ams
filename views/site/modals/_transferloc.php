<?php 
use yii\widgets\ActiveForm;
?>
<!-- Modal -->
<div class="modal fade" id="transferLoc" tabindex="-1" role="dialog" aria-labelledby="transferLocLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-move"></span> Inventory Transfer: <span class="inventory-details"></span></h4>
	  </div>
		<div class="alert alert-success fade in" id="sendemail-msg" style="display:none;"></div>
			<?php $form = ActiveForm::begin(['action'=>['/site/transferloc'], 'options' => ['id'=>'transfer-loc-form']]); ?>
				<div class="modal-body">
						<div id="loaded-transfer-location"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" value="Transfer" id="save_transer_loc">
				</div>
			<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>