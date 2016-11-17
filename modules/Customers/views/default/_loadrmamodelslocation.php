<?php 
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Partnumber;
	use app\models\Item;
?>
<div class="col-md-8" style="padding-left:10px;">
	<?php foreach($modelitems as $modelitem) : ?>
		<?php 
			$_model = Models::findOne($modelitem['model']);
			$_modelname = Manufacturer::findOne($_model->manufacturer)->name . ' ' . $_model->descrip;
			$iscustomerrequiretag = Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$_model->id, 'requiretag'=>1])->count();		
			$items = Item::find()->where(['location'=>$modelitem['location'], 'customer'=>$customer->id, 'model'=>$_model->id])->orderBy('serial, tagnum')->all();
			$nb_items = count($items);
		?>	
		<div class="row">
			<h4><span style="color:#566F88;font-weight: bold;"><?php echo $_modelname ?></span> (<?= $nb_items?>) <?php if(!$_model->serialized) :?><input type="checkbox" id="unserializedcheckbox_<?=$_model->id?>"><?php endif;?></h4>
			<?php if(!$_model->serialized) :?><input type="number" name="modelsqty[]" class="form-control" id="unserializedqtyinput_<?=$_model->id?>" disabled="disabled" style="display:none;" max="<?= $nb_items?>" min="0"/><?php endif;?>
			<input type="hidden" name="modelsid[]" value="<?php echo $_model->id;?>" id="hunserializedqtyinput_<?=$_model->id?>" disabled="disabled"/>
		</div>	
		<?php if($_model->serialized) :?>
			<?php foreach ($items as $key=>$item) : ?>
				<div class="row row-margin">
					<div class="form-inline">
						<div class="form-inline">
							<input type="checkbox" <?php if($item->confirmed==0) :?>disabled="disabled"<?php endif;?> id="checkbox_<?=$item->id?>" name="itemsid[<?=$_model->id?>][<?=$key?>]" value="<?=$item->id?>">
							<label style="font-weight: normal;font-size: 14px;">Add This Item To The RMA?</label>
							<input type="text" class="form-control" id="serial_<?=$item->id?>" placeholder="Please Enter A Serial Number" value="<?php echo $item->serial;?>" name="serials[<?=$_model->id?>][<?=$key?>]">
							<?php if($iscustomerrequiretag) :?>
								<input type="text" class="form-control" <?php if($item->confirmed==1) :?>readonly="readonly"<?php endif;?> style="width: 150px;" id="tagnum_<?=$item->id?>" placeholder="Tag# Description" name="tagnumber[<?=$_model->id?>][<?=$key?>]" <?php if($item->confirmed==1) :?>readonly="readonly"<?php endif;?> value="<?php echo $item->tagnum;?>"/>
							<?php endif;?>
							<?php if($item->confirmed==0) :?> 
								<button type="button" class="btn btn-default" id="confirmed_<?=$item->id?>">Confirm</button>
								<button type="button" class="btn btn-warning" id="modify_<?=$item->id?>" style="display:none;">Modify</button>
							<?php endif;?>
						</div>
						<?php if($nb_items>1) : ?><div class="row row-margin"></div><?php endif;?>
					</div>
				</div>
			<?php endforeach;?>
		<?php endif;?>
	<?php endforeach;?>
</div>
<div class="col-md-4" style="padding:0;padding-top:5px">
	<div class="form-inline">
		<button class="btn btn-sm btn-warning" type="button" onClick="loadCustomerLocation(<?=$_location->id?>, <?=$_location->customer_id?>);"><span class="glyphicon glyphicon-pencil"></span> Location Settings</button>
		<button class="btn btn-sm btn-warning" type="button" onClick="EditLocationDetails(<?=$_location->id?>);"><span class="glyphicon glyphicon-pencil"></span> Configuration Settings</button>
	</div>
	<div id="loaded-location-setting-content">
		<?php if(!empty($_location->storenum)) :?>
			<h4 style="font-weight: bold;margin-bottom:5px">Store#: <?= $_location->storenum?> Configuration Settings</h4>
		<?php endif;?>
			<?php if(empty($_location->connection_type)) :?>
				<div class="">
					Connection Type : <b>DHCP</b>
				</div>
			<?php endif;?>	
		<?php if(!empty($_location->connection_type) || !empty($_location_details->ipaddress) || !empty($_location_details->gateway) || !empty($_location_details->primary_dns) || !empty($_location_details->secondary_dns)) :?>
				<?php if(!empty($_location->connection_type)) :?>
					<div class="">
						Connection Type : <b><?= $_location->connection_type;?></b>
					</div>
				<?php endif;?>
					<?php if(!empty($_location->connection_type) && strtolower($_location->connection_type)=='dial-up') :?>
						<div class="">
							Require "9" to dial out : <b><?php echo ($_location_details->require_dialout==1) ? 'Yes' : 'No'?></b>
						</div>
					<?php endif;?>				
					<?php if(!empty($_location_details->ipaddress)) :?>
					<div class="">
						IP Address : <b><?= $_location_details->ipaddress;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->subnet_mask)) :?>
					<div class="">
						Subnet Mask : <b><?= $_location_details->subnet_mask;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->gateway)) :?>
					<div class="">
						Gateway : <b><?= $_location_details->gateway;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->primary_dns)) :?>
					<div class="">
						Primary DNS : <b><?= $_location_details->primary_dns;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->secondary_dns)) :?>
						<div class="">
							Secondary DNS : <b><?= $_location_details->secondary_dns;?></b>
						</div>
					<?php endif;?>
		<?php endif;?>
	</div>
</div>