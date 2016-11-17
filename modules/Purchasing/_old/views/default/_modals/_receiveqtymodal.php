<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Item;
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Receive;

?>
<div class="modal fade" id='ReceivePQtyDetails'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Receive <span id="number_generated"></<pan></h4>
		    </div>
			    <div class="modal-body">
			        <div id="detaisOfPurchasingReceive">
						<div class="row row-margin">			
								<p><b>Are you sure that the following items have arrived ?</b></p>					
						</div>		
					</div>		
			    </div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>		        
			        <button type="submit" class="btn btn-primary" name="saveReceiveQty" id="SaveReceiveQtyModal"><?php echo Yii::t('app', 'Save'); ?></button>		        
			    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>