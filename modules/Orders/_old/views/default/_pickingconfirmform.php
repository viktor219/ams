<?php if($confirmed_qty!=0) :?>
	<div class="row row-margin" style="text-align: center;">
		<div class="col-sm-4">
			<label for="serialized" class="checkbox-inline" style="padding-left:0px;">Serialized</label>																	
			<input type="checkbox" data-on-text="Yes" data-off-text="No" id="serialized" name="serialized" <?php echo ($model->serialized==1) ? "checked" : '';?>>
		</div>
		<div class="col-sm-4">
			<label for="cleaning" class="checkbox-inline" style="padding-left:0px;">Cleaning</label>																	
			<input type="checkbox" data-on-text="Yes" data-off-text="No" id="cleaning" name="cleaning" <?php echo ($model->preowneditems==1) ? "checked" : '';?>>
		</div>
		<div class="col-sm-4">
			<label for="testing" class="checkbox-inline" style="padding-left:0px;">Testing</label>																	
			<input type="checkbox" data-on-text="Yes" data-off-text="No" id="testing" name="testing" <?php echo ($model->requiretestingreferb==1) ? "checked" : '';?>>
		</div>
	</div> <br/>
	<div class="row row-margin" style="text-align: center;">			
		<p><b>Please confirm that you are picking <span id="confirmed-qty"><?= $confirmed_qty?></span> <span id="confirmed-model"><?php echo $manufacturer->name . ' ' . $model->descrip;?></span></b></p>
	</div>
	<script>							
	    $("[name='serialized']").bootstrapSwitch("size", "mini");
	    $("[name='cleaning']").bootstrapSwitch("size", "mini");
	    $("[name='testing']").bootstrapSwitch("size", "mini");
	</script>
	<input type="hidden" name="serialOrderId" value="<?php echo $order->id;?>"/>
	<input type="hidden" name="serialModelId" value="<?php echo $model->id;?>"/>
<?php else :?>
	<div class="row row-margin" style="text-align: center;">No items available for picking!</div>
<?php endif;?>