<div class="row row-margin">
	<div class="form-group">
		<div class="col-md-6">
			<label>Weight</label>
			<input type="text" class="form-control" name="weight" value="<?php echo $shipmentboxdetail->weight;?>" placeholder="0.00"/>
		</div>
		<div class="col-md-6">
			<label>Height</label>
			<input type="text" class="form-control" name="height" value="<?php echo $shipmentboxdetail->height;?>" placeholder="0.00"/>
		</div>
	</div>
</div>
<div class="row row-margin">
	<div class="form-group">
		<div class="col-md-6">
			<label>Length</label>
			<input type="text" class="form-control" name="length" value="<?php echo $shipmentboxdetail->length;?>" placeholder="0.00"/>
		</div>
		<div class="col-md-6">
			<label>Depth</label>
			<input type="text" class="form-control" name="depth" value="<?php echo $shipmentboxdetail->depth;?>" placeholder="0.00"/>
		</div>
	</div>
</div>
<input type="hidden" name="shipmentId" value="<?php echo $shipmentid;?>" />
<input type="hidden" name="modelId" value="<?php echo $_model->id;?>"/>
<input type="hidden" name="pallet_box_number" value="<?php echo $pallet_box_number;?>"/>