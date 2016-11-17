<div class="modal fade" id='relatedServiceDetails' style="z-index: 10000">
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title"><?php echo Yii::t('app', 'Service Order: '); ?><span id="service_number"></span></h4>
		    </div>
		    <div class="modal-body">
		        <div id="loaded-service-info-content"></div>		
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
		        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>