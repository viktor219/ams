<?php 
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Item;
	use app\models\Itemcondition;
	use app\models\Department;

?>
	<?php if($_totalcount === 0) :?>
		<div class="row row-margin" style="text-align:center;font-weight:bold;">All items are delivered.</div>
	<?php else :?>	
		<div class="row row-margin" id="delivery-confirmation-items">
			<?php if($_countshippingitems!=0):?>
				<h2>Shipping Department</h2>
				<div style="margin-left: 15px;" id="shipping-m-items">
					<?php foreach ($delivertoshippingitems as $delivertoshippingitem) :?>
						<?php 
							$_model = Models::findOne($delivertoshippingitem->model);
							 
							$_manufacturer = Manufacturer::findOne($_model->manufacturer);  
							
							$itemname = $_manufacturer->name . ' ' . $_model->descrip;		
						?>
						<div class="row form-group">
							<div class="col-md-10">- Deliver <b><?php echo Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'model'=>$_model->id])->count();?></b> <?php echo Itemcondition::findOne($delivertoshippingitem['conditionid'])->name;?> <b><?php echo $itemname?></b></div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning shipping-m-item" onClick="confirmReadyButton(1, <?php echo $delivertoshippingitem->id;?>, 0);" id="btn_1-<?php echo $delivertoshippingitem->id;?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
						</div>		
					<?php endforeach;?> 
				</div>
				<?php if(count($delivertoshippingitems)>1):?>
					<div class="row form-group text-right">
						<button type="button" class="btn btn-warning shipping-m-item" onClick="confirmReadyButton(1, 0, <?php echo $order->id;?>);" id="btn_1-<?php echo $order->id;?>-shipping"><span class="glyphicon glyphicon-share-alt"></span></button>				
					</div>
				<?php endif;?>
			<?php endif;?>
			<?php if($_countcleaningitems!=0):?>
				<h2>Cleaning Department</h2>
				<div style="margin-left: 15px;">
					<?php foreach ($delivercleaningitems as $delivercleaningitem) :?>
						<?php 
							$_model = Models::findOne($delivercleaningitem['model']);
							 
							$_manufacturer = Manufacturer::findOne($_model->manufacturer);  
							
							$itemname = $_manufacturer->name . ' ' . $_model->descrip;				
						?>
						<div class="row form-group">
							<div class="col-md-10">- Deliver <b><?php echo Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'model'=>$_model->id, 'conditionid'=>$delivercleaningitem['conditionid']])->count();?></b> <?php echo Itemcondition::findOne($delivercleaningitem['conditionid'])->name;?> <b><?php echo $itemname?></b></div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning cleaning-m-item" onClick="confirmReadyButton(2, <?php echo $delivercleaningitem['id'];?>, 0);" id="btn_2-<?php echo $delivercleaningitem['id'];?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
						</div>		
					<?php endforeach;?>
				</div>
				<?php if(count($delivercleaningitems)>1):?>
					<div class="row form-group text-right">
						<button type="button" class="btn btn-warning cleaning-m-item" onClick="confirmReadyButton(2, 0, <?php echo $order->id;?>);" id="btn_1-<?php echo $order->id;?>-cleaning"><span class="glyphicon glyphicon-share-alt"></span></button>				
					</div>
				<?php endif;?>
			<?php endif;?>		
			<?php if($_counttestingitems!=0):?>
				<h2>Testing Labs</h2>
				<div style="margin-left: 15px;">
					<?php foreach ($delivertestingitems as $delivertestingitem) :?>
						<?php 
							$_model = Models::findOne($delivertestingitem['model']);
							 
							$_manufacturer = Manufacturer::findOne($_model->manufacturer);  
							
							$itemname = $_manufacturer->name . ' ' . $_model->descrip;				
						?>
						<div class="row form-group">
							<div class="col-md-10">- Deliver <b><?php echo Item::find()->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'model'=>$_model->id, 'conditionid'=>$delivertestingitem['conditionid']])->count();?></b> <?php echo Itemcondition::findOne($delivertestingitem['conditionid'])->name;?> <b><?php echo $itemname?></b> to <?php echo Department::findOne($_model->department)->name;?> Department</div> <div class="col-md-2"><button type="button" class="btn btn-xs btn-warning testing-m-item" onClick="confirmReadyButton(3, <?php echo $delivertestingitem['id'];?>, 0);" id="btn_3-<?php echo $delivertestingitem['id'];?>"><span class="glyphicon glyphicon-share-alt"></span></button></div>	
						</div>		
					<?php endforeach;?>
					</div>
				<?php if(count($delivertestingitems)>1):?>
					<div class="row form-group text-right">
						<button type="button" class="btn btn-warning testing-m-item" onClick="confirmReadyButton(3, 0, <?php echo $order->id;?>);" id="btn_1-<?php echo $order->id;?>-testing"><span class="glyphicon glyphicon-share-alt"></span></button>				
					</div>
				<?php endif;?>				
			<?php endif;?>		
		</div>
	<?php endif;?>