<?php 
	use app\models\Location;
?>
<div style="text-align: center;">
	<div class="form-group" id="order-location-group">
		<label for="">Choose A Location</label>
		<select name="order_location" id="selectWarehouseLocation" class="form-control">
			<option value="">Choose A Location</option>
			<?php foreach(Location::findAll(['customer_id'=>$customerid]) as $location) :?>
				<?php 
					$output = "";
					if(!empty($location->storenum))
						$output .= "Store#: " . $location->storenum . " - ";
					if(!empty($location->storename))
						$output .= $location->storename  . ' - '; 
					//
					$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
				?>
				<option value="<?php echo $location->id;?>"><?php echo $output;?></option>
			<?php endforeach;?>			
		</select>	
	</div>
	<div class="form-group" id="order-qty-group">
		<label for="">Enter A Quantity :</label>
		<input type="number" name="order_qty"  id="orderQty" class="form-control" min="0">
	</div>
</div>
<input type="hidden" name="modelId" value="<?php echo $model->id;?>" />