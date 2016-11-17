<?php 
use app\models\Customer;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Category;
use app\models\Department;

$i = 0;

$manufacturers = Manufacturer::find()->all();
$categories = Category::find()->all();
$departments = Department::find()->all();
?>

<h3 style="text-align:center;"><b><?php echo $manufacturer->name;?> <?php echo $model->descrip;?></b></h3>
<div class="">
	<div class="col-md-12 col-xs-12">
		<div class="col-md-3 col-sm-6 col-xs-12 form-group" id="manufacturer-group">
			<label for="manufacturer">Manufacturer</label>
			<select class="select2_single form-control" name="manufacturer">
				<?php foreach($manufacturers as $man) :?>
					<option <?php if($man->name===$manufacturer->name) :?>selected="selected" <?php endif;?>value="<?php echo $man->id;?>"><?php echo $man->name;?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="col-md-7 col-sm-6 col-xs-12 form-group" id="description-group">
			<label for="description">Description</label>
			<input type="text" class="form-control" placeholder="Enter model description..." name="description" value="<?php echo $model->descrip;?>" />
		</div>
		<div class="col-md-2 col-sm-6 col-xs-12 form-group" id="serialized-group">
			<label for="serialized">Serialized</label>
			<select class="form-control" name="serialized">
				<option value="0" <?php if(!$model->serialized) :?> selected<?php endif;?>>No</option>
				<option value="1" <?php if($model->serialized) :?> selected<?php endif;?>>Yes</option>
			</select>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12 form-group" id="category-group">
			<label for="category">Category</label>
			<select class="select2_single form-control" name="category">
				<?php foreach($categories as $cat) :?>
					<option <?php if($cat->id==$model->category_id) :?> selected<?php endif;?> value="<?php echo $cat->id;?>"><?php echo $cat->categoryname;?></option>
				<?php endforeach;?>
			</select>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12 form-group" id="departement-group">
			<label for="department">Department</label>
			<select class="select2_single form-control" name="department">
				<?php foreach($departments as $dep) :?>
					<option <?php if($dep->id===$model->department) :?>selected="selected" <?php endif;?>value="<?php echo $dep->id;?>"><?php echo $dep->name;?></option>
				<?php endforeach;?>
			</select>
		</div>	
	</div>
</div>
<div class="">
	<div class="col-md-12 col-xs-12">
		<div class="col-md-4 col-sm-6 col-xs-12 form-group">
			<label for="checkit">Checkit</label>
			<input class="form-control" id="inputSuccess2" placeholder="Checkit." type="text" name="checkit" value="<?php echo $model->checkit;?>">
		</div>	
		<div class="col-md-4 col-sm-6 col-xs-12 form-group">
			<label for="stripcharacters">Strip Barcode characters</label>
			<input class="form-control" id="inputSuccess2" placeholder="Strip Barcode characters." name="stripcharacters" type="text" value="<?php echo $model->stripcharacters;?>">
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12 form-group">
			<label for="charactercount">Character Length</label>
			<input class="form-control" id="inputSuccess3" placeholder="Character length." name="charactercount" type="text" value="<?php echo $model->charactercount;?>">
		</div>
	</div>
</div>
<div class="">
	<div class="col-md-4 col-sm-6 col-xs-12 form-group">
		<label for="aei">aei</label>
		<input class="form-control" id="inputSuccess2" placeholder="AEI." name="aei" type="text" value="<?php echo $model->aei;?>">
	</div>	
	<div class="col-md-4 col-sm-6 col-xs-12 form-group">
		<label for="frupartnum">Fru Part number</label>
		<input class="form-control" id="inputSuccess2" placeholder="Fru Part number." name="frupartnum" type="text" value="<?php echo $model->frupartnum;?>">
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12 form-group">
		<label for="manpartnum">Man part number</label>
		<input class="form-control" id="inputSuccess3" placeholder="Man part number." name="manpartnum" type="text" value="<?php echo $model->manpartnum;?>">
	</div>
</div>
<!--<div class="well well-sm">-->
	<label for="selectItem"><small>Add A <?php echo $customer->companyname;?> Part Number:</small></label>
	<div id="partEntry1" class="partClonedInput">
		<div class="row form-group">
			<div class="col-md-6 customer-group">
				<input class="typeahead form-control input_cust" type="text" name="editModelCustomer[]" id="editModelCustomer_1" value="" placeholder="Select a customer" data-provide="typeahead" autocomplete="off" type="search" />
				<input class="form-control input_h" type="hidden" name="editModelCustomerval[]" id="editModelCustomerval_1" />				
			</div>	
			<div class="col-md-3 partid-group">
				<input class="form-control input_partid" id="inputSuccess4" placeholder="Part ID." type="text" name="partid[]" id="partid_1" />			
			</div>	
			<div class="col-md-3 partdesc-group">
				<input class="form-control input_partdesc" id="inputSuccess5" placeholder="Part Description." type="text" name="partdesc[]" id="partdesc_1" />
			</div>			
		</div>
	</div>
<!--</div>--> 
<div class="row">
	<div class="col-sm-12">
		<div class="actions text-right">
			<button class="btn btn-success btn-xs" id="PartbtnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
			<button class="btn btn-success btn-xs" id="PartbtnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
		</div>
	</div>
</div>
<input type="hidden" name="customerId" value="<?php echo $customer->id;?>" />