<?php 
	use app\models\Models;
	use app\models\Manufacturer;
?>
<?php foreach ($items as $item):?>
	<?php 
		$_model = Models::findOne($item->model);
		$_man = Manufacturer::findOne($_model->manufacturer);
	?>
	<div class="row row-margin" >
		<div class="col-md-10" id="serial-group-<?php echo $item->id;?>">
			<label><b><?=$_man->name?> <?=$_model->descrip;?></b> <?= (!empty($item->serial)) ? '(' . $item->serial . ')' : '' ?></label>										
			<div class="input-group">
				<input type="text" name="serialnumber_<?php echo $item->id;?>" class="form-control qserialnumber" placeholder="Enter your serial number..." value=""/>											
				<span class="input-group-btn">
					<button type="button" id="saveSerialBtn_<?php echo $item->id;?>" onClick="saveSerializedItem(<?php echo $item->id;?>, <?php echo $item->model;?>)" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></button>
				</span>
			</div>
		</div>
	</div>
<?php endforeach;?>