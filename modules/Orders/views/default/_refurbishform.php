<?php 
	use app\models\Manufacturer;
	
	$manufacturer = Manufacturer::findOne($model->manufacturer);
?>
<div class="row row-margin">
	<input type="hidden" id="customer_Id" value="<?php echo $order->customer_id;?>"> 
	<div class="col-sm-6">					 
		<input type="checkbox" id="preowneditems" name="preowneditems" <?php echo ($model->preowneditems==1) ? 'checked' : '';?>/>
		<label for="requirelabelmodel" class="checkbox-inline" style="padding-left:0px;">Are pre-owned/used items of this model sent through the cleaning department before the service labs?</label>									
	</div>
	<div class="col-sm-6">					 
		<input type="checkbox" id="requiretestingreferb" name="requiretestingreferb" <?php echo ($model->requiretestingreferb==1) ? 'checked' : '';?>>
		<label for="requirelabelbox" class="checkbox-inline" style="padding-left:0px;">Does this model require testing before refurbishing is completed?</label>									
	</div>	
</div>
<div class="row row-margin form-group" >
	<div class="input-group">
		<input class="form-control" type="text" name="description" value="<?php echo $manufacturer->name . ' ' . $model->descrip;?>" disabled>	
		<input class="form-control input_h" type="hidden" name="modelid" value="<?php echo $model->id;?>"/>																								
		<span class="input-group-btn">
			<button class="btn btn-success edit_item_button" id="Edit_1" type="button" style=""><span class="glyphicon glyphicon-pencil"></span></button>
		</span>
	</div>
</div>