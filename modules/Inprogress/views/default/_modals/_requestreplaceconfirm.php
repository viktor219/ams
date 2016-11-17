<div class="modal fade" id='RequestReplaceConfirmation'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Confirmation</h4>
		    </div>
		    <div class="modal-body">
		        <div id="content" style="text-align: center;">
					<div class="row row-margin">			
						<p><b>Are you sure to replace this item ?</b></p>
					</div>		
				</div>		
		    </div>
		    <div class="modal-footer"> 
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></button>		        
		        <a class="btn btn-primary" id="rconfirmButton" data-href="" data-id=""><?php echo Yii::t('app', 'Confirm'); ?></a>		        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>