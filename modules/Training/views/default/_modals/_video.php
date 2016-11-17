<?php 
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
?>

<!-- Modal -->
<div class="modal fade" id="videoPlayer" tabindex="-1" role="dialog" aria-labelledby="newModelLabel" style="z-index:10000">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> <span id="box-actions"></span><span id="dim_box_model_name"></span></h4>
	  </div>
		<div class="alert alert-success fade in" id="box-dimension-msg" style="display:none;"></div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
		</div>
	</div>
</div>