<div class="modal fade" id='picklistReady'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Delivering Confirmation</h4>
		    </div>
		    <div class="modal-body" style="padding-top: 0">
		        <div id="detaisOfPicklistReady"></div>		
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>	
		         <button type="button" class="btn btn-primary" id="main-delivery-items" onClick="confirmReadyButton(0, 0, <?php echo $order->id;?>);"><?php echo Yii::t('app', 'Deliver All'); ?></button>	        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>