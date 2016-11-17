<?php 
	use yii\widgets\ActiveForm;
?>
<div class="modal fade" id="addSerials" tabindex="-1" role="dialog" aria-labelledby="addSerialsLabel" style="z-index: 1600;">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Add Serial Number</h4>
	  </div>
	  <div class="alert alert-success fade in" id="serial-msg" style="display:none;"></div>
			<div class="modal-body">
		<?php if($customer->requireserialphoto) :?>
			<?php $form = ActiveForm::begin(['options' => ['id'=>'scan-picture-serial-form', 'enctype' => 'multipart/form-data'], 'action'=>Yii::$app->getUrlManager()->createUrl('orders/scanserialpicture')]); ?> 
				<div class="row-margin">
					<div class="col-md-4">
						<span class="btn btn-warning btn-file">
							<b>Scan from Picture</b> <input type="file" name="serialUnconvertedPicture" id="serialUnconvertedPicture" accept="image/*">
						</span>
					</div>
					<div class="col-md-3">
						<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Checking...</p></div> 
					</div>
		        </div>
	            <div id="pictureScanErrors" style="color:red;"></div>
	       <?php ActiveForm::end(); ?>
       <?php endif;?>
	  	<?php $form = ActiveForm::begin(['options' => ['id'=>'add-serial-form']]); ?> 			
				<div id="serialsInput"></div>				
				<input type="hidden" id="serialOrderId" value="<?php echo $order->id;?>"/>
			</div>
			<div class="modal-footer">
				<div id="current_serial_description"><b>You have added <span class="countserializeditems"></span> of <span class="countnotserializeditems"></span> for this order</b></div>
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>	
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>