<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="modal fade" id='PickSerials'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>		
		        <h4 class="modal-title">Add Serial: <span id="rec-title"></span></h4>     
		    </div>
		    <div class="modal-body">
		    	<?php $form = ActiveForm::begin(['options' => ['id'=>'add-requested-service-item-serial']]); ?>
		        	<div id="detaisOfQtySerials"></div>	
		        <?php ActiveForm::end(); ?>			
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>		        
		        <!-- <button type="button" class="btn btn-primary" id="SaveInStockQtyModal"><?php echo Yii::t('app', 'Save'); ?></button> -->	        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>