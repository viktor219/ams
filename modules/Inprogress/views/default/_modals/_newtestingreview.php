<?php 
use yii\helpers\Html;

?>
<div class="modal fade" id="testingReview" role="dialog" aria-labelledby="testingReviewLabel" style="z-index: 1600;">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Choose An Issue</h4>
	  </div>
	  <div class="alert alert-success fade in" id="new-testing-review-msg" style="display:none;text-align:center;font-weight:bold;"></div>	
		<div class="modal-body">
			<div id="loadedModelForm"></div>	
		</div>
		<div class="modal-footer">
			<div class="row row-margin">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<!-- <input type="submit" class="btn btn-success" value="Save" > -->
			</div>
		</div>				
	</div>
  </div>
</div>	