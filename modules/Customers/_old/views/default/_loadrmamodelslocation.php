<?php 
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Partnumber;
	use app\models\Item;
?>
<div class="col-md-9">
	<?php foreach($items as $item) : ?>
		<?php 
			$models = split(",",$item["rows_model"]);
			$serializeds = split(",",$item["rows_serialized"]);
			$serials = split(",",$item["rows_serial"]);
			$ids = split(",",$item["rows_id"]);
			$tagnums = split(",",$item["rows_tagnums"]);
			//var_dump($ids);
		?>	
		<?php foreach ($models as $key=>$model) : ?>
			<?php 
				$_model = Models::findOne($model);
				$_modelname = Manufacturer::findOne($_model->manufacturer)->name . ' ' . $_model->descrip;
				$iscustomerrequiretag = Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$_model->id, 'requiretag'=>1])->count();
			?>
			<div class="row row-margin">
				<div class="row">
			    	<h4><span style="color:#566F88;font-weight: bold;"><?php echo $_modelname ?></span> (<?= count($ids)?>) <?php if(!$_model->serialized) :?><input type="checkbox" id="unserializedcheckbox_<?=$_model->id?>"><?php endif;?></h4>
					<?php if(!$_model->serialized) :?><input type="number" name="modelsqty[]" class="form-control" id="unserializedqtyinput_<?=$_model->id?>" disabled="disabled" style="display:none;" max="<?= count($ids)?>" min="0"/><?php endif;?>
					<input type="hidden" name="modelsid[]" value="<?php echo $_model->id;?>" id="hunserializedqtyinput_<?=$_model->id?>" disabled="disabled"/>
				</div>	
				<div class="form-inline">
				  	<?php foreach ($ids as $key=>$id) : ?> 
				  		<?php $_item = Item::findOne($id);?>
						<?php if($serializeds[$key] == 1) :?>
						<div class="form-inline">
							<input type="checkbox" <?php if($_item->confirmed==0) :?>disabled="disabled"<?php endif;?> id="checkbox_<?=$id?>" name="itemsid[<?=$_model->id?>][]" value="<?=$id?>">
						    <label style="font-weight: normal;font-size: 14px;">Add This Item To The RMA?</label>
						    <input type="text" class="form-control" id="serial_<?=$id?>" placeholder="Please Enter A Serial Number" value="<?php echo $serials[$key];?>" name="serials[<?=$_model->id?>][]">
						    <?php if($iscustomerrequiretag) :?>
						    	<input type="text" class="form-control" <?php if($_item->confirmed==1) :?>readonly="readonly"<?php endif;?> style="width: 150px;" id="tagnum_<?=$id?>" placeholder="Tag# Description" name="tagnumber[<?=$_model->id?>][]" <?php if($_item->confirmed==1) :?>readonly="readonly"<?php endif;?> value="<?php echo $tagnums[$key];?>"/>
						    <?php endif;?>
						    <?php if($_item->confirmed==0) :?> 
						    	<button type="button" class="btn btn-default" id="confirmed_<?=$id?>">Confirm</button>
						    	<button type="button" class="btn btn-warning" id="modify_<?=$id?>" style="display:none;">Modify</button>
						    <?php endif;?>
						</div>
						<?php if(count($ids)>1) : ?><div class="row row-margin"></div><?php endif;?>
						<?php endif;?>
					<?php endforeach;?>  	
				</div>
			</div>
		<?php endforeach;?>
	<?php endforeach;?>
</div>
<div class="col-md-3">
	<button class="btn btn-warning" type="button" onClick="EditLocationDetails(<?=$_location->id?>);"><span class="glyphicon glyphicon-pencil"></span> Edit Configuration Settings</button>
	<div id="loaded-location-setting-content">
		<?php if(!empty($_location->storenum)) :?>
			<div class="row">
				<h4 style="font-weight: bold;">Store#: <?= $_location->storenum?> Configuration Settings</h4>
			</div>
		<?php endif;?>
			<?php if(empty($_location->connection_type)) :?>
				<div class="row">
					Connection Type : <b>DHCP</b>
				</div>
			<?php endif;?>	
		<?php if(!empty($_location->connection_type) || !empty($_location_details->ipaddress) || !empty($_location_details->gateway) || !empty($_location_details->primary_dns) || !empty($_location_details->secondary_dns)) :?>
				<?php if(!empty($_location->connection_type)) :?>
					<div class="row">
						Connection Type : <b><?= $_location->connection_type;?></b>
					</div>
				<?php endif;?>
					<?php if(!empty($_location_details->ipaddress)) :?>
					<div class="row">
						IP Address : <b><?= $_location_details->ipaddress;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->gateway)) :?>
					<div class="row">
						Gateway : <b><?= $_location_details->gateway;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->primary_dns)) :?>
					<div class="row">
						Primary DNS : <b><?= $_location_details->primary_dns;?></b>
					</div>
					<?php endif;?>
					<?php if(!empty($_location_details->secondary_dns)) :?>
						<div class="row">
							Secondary DNS : <b><?= $_location_details->secondary_dns;?></b>
						</div>
					<?php endif;?>
		<?php endif;?>
	</div>
</div>