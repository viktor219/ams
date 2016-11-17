<?php 
use app\models\Customer;
use app\models\Manufacturer;
use app\models\Models;

$model = Models::findOne($item);
$manufacturer = Manufacturer::findOne($model->manufacturer);
$i = 0;
?>
<h3 style="text-align:center;"><b><?php echo $customer->companyname;?> - <?php echo $manufacturer->name;?> <?php echo $model->descrip;?></b></h3>
<?php //for ($i=0; $i < $count; $i++) :?>
	<div class="row row-margin">
		<div class="col-md-12" id="serial-group">
			<label >Enter Or Scan Your Next Serial Number</label>
			<input type="text" name="serialnumber" id="qserialnumber" class="form-control" placeholder="Enter your serial number..." value="" autofocus/>
			<input type="hidden" id="serialCurrentModel" value="<?php echo $model->id;?>"/>
			<input type="hidden" id="serialQuantity" value="<?php echo $quantity;?>"/>
			<input type="hidden" id="customerId" value="<?php echo $customer->id;?>"/>
			<input type="hidden" id="triggerRow" value="<?php echo $row;?>"/>
			<span class="fa fa-suitcase form-control-feedback right" style="margin-top: 28px;" aria-hidden="true"></span>
		</div>
	</div>
<?php //endfor;?>