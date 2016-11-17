<?php 
	use app\models\Manufacturer;
	use app\models\Category;
	use app\models\Department;
	use app\models\Customer;
	use yii\widgets\ActiveForm;
?>
<!-- Modal -->
<div class="modal fade" id="ReOrder" tabindex="-1" role="dialog" aria-labelledby="reorderLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="reorderModalLabel"> <span class="glyphicon glyphicon-list-alt"></span> ReOrder</h4>
	  </div>
		<div class="alert alert-success fade in" id="reorder-msg" style="display:none;"></div>
		<?php $form = ActiveForm::begin(["action"=>Yii::$app->getUrlManager()->createUrl('orders/rvalidate'), 'options' => ['class'=>'form-group form-group-sm', 'id'=>'reorder-form']]); ?>
			<div class="modal-body">
				<div class="container">
					<div class="row">
						<div id="r_item_name" style="font-weight:bold;font-size:22px;text-align:center;color:#888"></div>
					</div>
					<div class="row">
					<div class="col-md-12" id="rqty-group">
						<label for="qty">Quantity</label>
						<input type="text" class="form-control" name="rqty" id="rqty" placeholder="Quantity (Required)">
						<span class="fa fa-suitcase form-control-feedback right" style="margin-top: 28px;" aria-hidden="true"></span>
					</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>
			<input type="hidden" name="rorder_id" value="" />
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>