<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal fade" id="testingReview" role="dialog" aria-labelledby="testingReviewLabel" style="z-index: 1600;">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> Testing</h4>
	  </div>
	  <div class="alert alert-success fade in" id="new-testing-review-msg" style="display:none;text-align:center;font-weight:bold;"></div>
	  	<?php $form = ActiveForm::begin(['action'=>['/inprogress/turntotesting'], 'options' => ['id'=>'new-testing-review-form']]); ?> 	
			<div class="modal-body">
				<div id="loadedModelForm">
					<div class="form-group" id="r_problem-group">
						<label for="description">Problem <span class="small">(tape for use existing problem or add new)</span></label>
						<input type="text" id="autocomplete-problem" class="form-control" name="problem" placeholder="Find problem..." autocomplete="off" autofocus>	
					</div>
					<div class="form-group" id="r_description-group">
						<label for="description">Resolution</label>
						<textarea class="form-control " id="r_resolution" name="resolution" rows="3" style="min-height:120px;resize:none;" placeholder="Description (Required)"></textarea>
					</div>
					<div class="form-group" id="r_part-group">
						<label for="rpart_used">Part used</label>
						<input class="typeahead form-control input_fn" type="text" name="description" id="autocompleteitem" placeholder="Select an Item" data-provide="typeahead" autocomplete="off">
						<input class="form-control input_h" type="hidden" name="modelid" id="autocompletevalitem" />	
					</div>					
				</div>	
			</div>
			<div class="modal-footer">
				<div class="row row-margin">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" value="Save" >
				</div>
			</div>				
			<input type="hidden" id="itemId" name="itemId">
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>	