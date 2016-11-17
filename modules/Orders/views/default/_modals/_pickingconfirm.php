<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Url;
?>
<div class="modal fade modal-child" id='PickingConfirmation' data-backdrop-limit="1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-modal-parent="#addSerials" style="z-index: 1400;">
	<div class="modal-dialog">
		<div class="modal-content">
			<?php $form = ActiveForm::begin(['action' => Url::to(['/orders/picknonserialized']), 'options' => ['id'=>'picking-non-serialized-confirm-form']]); ?> 
			    <div class="modal-header"> 
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Picking Confirmation</h4>
			    </div>
			    <div class="modal-body">
			        <div id="load-picking-confirmed-content">		
					</div>		
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></button>		        
			        <button type="submit" class="btn btn-primary" id="confirmButton"><?php echo Yii::t('app', 'Confirm'); ?></button>		        
			    </div>
			<?php ActiveForm::end(); ?>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>