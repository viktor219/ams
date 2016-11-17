<?php 
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use app\models\Customer;
	use app\models\Location;
?>
<div class="modal fade" id='receivingCreateModal'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Receive Unscheduled Inventory</h4>
		    </div>
		    <div class="alert alert-success fade in" id="itemrequest-msg" style="display:none;"></div>
		    <?php $form = ActiveForm::begin(['action'=>['/receiving/create'], 'options' => ['id'=>'receive-unscheduled-inventory-form', 'class'=>'form-group form-group-sm']]); ?>
		    	<div class="modal-body">
					<div class="container">
						<div class="row row-margin" id="r_customer-group">
							<input type="text" id="receiving-customer" class="form-control col-md-4" name="customer" placeholder="Choose A Customer" autocomplete="off" value="<?php echo Customer::findOne($model->customer_id)->companyname;?>">
							<input type="hidden" id="customer_Id" name="customerId" />
						</div>		
						<div class="row row-margin" id="r_model-group">
							<input class="typeahead form-control input_fn" data-link='<?php echo Yii::$app->getUrlManager()->createUrl('ajaxrequest/listitem') ?>' type="text" name="description[]" id="autocompleteitem_1" value="" placeholder="Loading data ..." data-provide="typeahead" autocomplete="off" type="search" disabled>
							<input class="form-control input_h" type="hidden" name="modelid" id="autocompletevalitem_1" />						
						</div>
						<div class="row row-margin" id="r_location-group">
							<select class="form-control" tabindex="-1" id="rselectLocation" name="location">
								<option selected="selected" value="">Select A Location</option>
							</select>						
						</div>
						<div class="row">
							<div class="col-md-4 row-margin" id="r_qty-group">
								<input type="number" id="rec_qty" class="form-control" name="receivedquantity" placeholder="Enter A Quantity"/>
							</div>
							<div class="col-md-4" id="r_serialnumber-group"></div>
						</div>
					</div>        		
		    	</div>
			<?php ActiveForm::end(); ?>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>		
		        <button type="button" class="btn btn-primary" id="ReceiveUnschInventory"><?php echo Yii::t('app', 'Save'); ?></button>	        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>