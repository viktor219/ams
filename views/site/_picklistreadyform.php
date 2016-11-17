<?php 
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Item;
	use app\models\Itemcondition;
	use app\models\Department;

	$itemname = $_manufacturer->name . ' ' . $_model->descrip;
?>
<div class="row row-margin" id="delivery-confirmation-items">
	<?php if($delivertoshippingitems!=0 && $delivercleaningitems==0 && $delivertestingitems==0):?>
		<h2>Shipping Department</h2>
		<div style="margin-left: 15px;" id="shipping-m-items">
			<div class="row form-group">
				<div class="col-md-10">- Deliver <b>1</b> <?php echo Itemcondition::findOne($item['conditionid'])->name;?> <b><?php echo $itemname?></b></div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning shipping-m-item" onClick="confirmSReadyButton(1, <?php echo $item->id;?>);" id="btn_1-<?php echo $item->id;?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
			</div>		
		</div>
	<?php endif;?>
	<?php if($delivercleaningitems > 0){?>
		<h2>Cleaning Department</h2>
		<div style="margin-left: 15px;">
			<div class="row form-group">
				<div class="col-md-10">- Deliver <b>1</b> <?php echo Itemcondition::findOne($item['conditionid'])->name;?> <b><?php echo $itemname?></b></div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning cleaning-m-item" onClick="confirmSReadyButton(2, <?php echo $item->id;?>);" id="btn_2-<?php echo $item->id;?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
			</div>		
		</div>
	<?php } else if($delivertestingitems > 0){?>
		<h2>Testing Labs</h2>
		<div style="margin-left: 15px;">
			<div class="row form-group">
				<div class="col-md-10">- Deliver <b>1</b> <?php echo Itemcondition::findOne($item['conditionid'])->name;?> <b><?php echo $itemname?></b> to <?php echo Department::findOne($_model->department)->name;?> Department</div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning testing-m-item" onClick="confirmSReadyButton(3, <?php echo $item->id;?>);" id="btn_3-<?php echo $item->id;?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
			</div>		
		</div>			
	<?php }?>		
</div> 