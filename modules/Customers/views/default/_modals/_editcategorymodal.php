<?php 
	use yii\widgets\ActiveForm;
?>
<div class="modal fade" id='UpdateCategory'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Update <span id="mod_model_name"></span></h4>
		    </div>
	  		<div class="alert alert-success fade in" id="category-update-msg" style="display:none;text-align:center;font-weight:bold;"></div>
	  		<?php $form = ActiveForm::begin(['options' => ['id'=>'update-category-form']]); ?>
			    <div class="modal-body">
			        <div id="update-loaded-content"></div>
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>		        
			        <button type="submit" class="btn btn-primary"><?php echo Yii::t('app', 'Save'); ?></button>		        
			    </div>
		    <?php ActiveForm::end(); ?>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>		        