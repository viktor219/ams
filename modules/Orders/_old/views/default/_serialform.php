<?php 
use app\models\Customer;
use app\models\Manufacturer;
use app\models\Models;

$customer = Customer::findOne($order->customer_id);
$model = Models::findOne($item->model);
$manufacturer = Manufacturer::findOne($model->manufacturer);
$i = 0;
?>
<h3 style="text-align:center;"><b><?php echo $customer->companyname;?> - <?php echo $manufacturer->name;?> <?php echo $model->descrip;?></b></h3>
<?php //for ($i=0; $i < $count; $i++) :?>
	<div class="row row-margin">
		<div class="col-md-12" id="serial-group">
			<label >Enter Or Scan Your Next Serial Number</label>
			<input type="text" name="serialnumber" id="qserialnumber" class="form-control" placeholder="Enter your serial number..." value=""/>
			<?php if($customer->requirelanenumber && $model->islanesepecific) :?>
				<input type="text" name="lanenumber" id="qlanenumber" class="form-control" placeholder="Enter lane number..." value=""/>			
			<?php endif;?>
			<input type="hidden" id="serialCurrentModel" value="<?php echo $model->id;?>"/>
			<input type="hidden" id="serialCurrentItem" value="<?php echo $item->id?>"/>
			<input type="hidden" id="customerId" value="<?php echo $customer->id?>"/>
			<span class="fa fa-suitcase form-control-feedback right" style="margin-top: 28px;" aria-hidden="true"></span>
		</div>
	</div>
<?php //endfor;?>