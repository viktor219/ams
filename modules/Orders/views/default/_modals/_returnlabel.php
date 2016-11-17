<?php 
	use yii\helpers\Html;
?>
<!-- Modal -->
<div class="modal fade" id="returnLabel" aria-labelledby="newModelLabel" style="z-index: 100000">
    <div class="modal-dialog" role="document" style="width: 50%;">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Return Label</h4>
            </div>
            <div class="modal-body" style="overflow: auto">
            	<div id="loaded-return-label-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <?= Html::a('<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download', 'javascript:;', ['class' => 'btn btn-primary','target'=>'_blank', 'id' => 'download-label-mail-button']) ?>
                <?= Html::a('<span class="glyphicon glyphicon-envelope"></span> Send Via Email', 'javascript:;', ['class' => 'btn btn-success', 'id' => 'send-label-mail-button']) ?>
            </div>
        </div>
    </div>
</div>  