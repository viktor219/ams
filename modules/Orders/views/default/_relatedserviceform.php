<div class="row row-margin">
	<div class="form-group">
		<?php foreach($items as $item) :?>
		<?php
			$_model = \app\models\Models::findOne($item->model);
			$manufacturer = \app\models\Manufacturer::findOne($_model->manufacturer);
			$name = $manufacturer->name . ' ' . $_model->descrip;
			$serial = $item->serial;
			$tagnum = $item->tagnum;
		?>
			<?=$name?> <br/> <li style="margin-left:20px;list-style-type: square;">
			<?php if(!empty($serial)):?>
				<?=$serial?>
			<?php endif;?>
			<?php if(!empty($tagnum) && $tagnum!='' && $tagnum!='undefined'):?>
				<?php echo "($tagnum)";?>
			<?php endif;?>
			</li>
		<?php endforeach;?>
	</div>
	<div style="text-align: center;">
		<div class="form-group"><b><i>Your RMA Service request number is: <?= $model->number_generated;?></i></b></div>
		<div style="text-align: center;margin-left: 50px;">
			<div class="form-group row-margin">
				<button type="button" class="col-md-10 btn btn-info"><?=$completepercentage?> % <?=$_currentstatus;?></button>
			</div><br/><br/>
			<div class="form-group row-margin" style="text-align: center;">
				<a href="javascript:;" id="return-label-service_<?=$model->id; ?>" class="btn btn-primary col-md-5">Return Label</a>
				<a href="<?php echo (!empty($_shipment) && !empty($_shipment->master_trackingnumber)) ? "https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=" . $_shipment->master_trackingnumber : "javascript:;"?>" target="_blank" class="btn btn-primary col-md-5">Track Shipment</a>
			</div>
		</div>
	</div>
</div>