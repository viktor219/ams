<div class="modal fade" id='ReadytoShipAllConfirmation'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Confirmation</h4>
		    </div>
		    <div class="modal-body">
		        <div id="content">
					<div class="row row-margin">			
						<p><b>Are you sure that all items are ready for ship ?</b></p>
					</div>		
				</div>		
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></button>		        
		        <button type="button" class="btn btn-primary" id="sconfirmAllButton"><?php echo Yii::t('app', 'Confirm'); ?></button>		        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>