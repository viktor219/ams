<?php /*	<div class="row" <?php if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE):?>style="display:none;"<?php endif;?>>
		<div class="col-sm-4" id="lshippingcompany-group">
			<select class="form-control" id="lshippingcompany" name="lshippingcompany">
				<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
				<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
					<option value="<?php echo $shippingcompany->id;?>"><?php echo $shippingcompany->name;?></option>							  		
				<?php endforeach;?>
			</select>								
		</div>					
		<div class="col-sm-4" id="laccountnumber-group">
			<input type="text" class="form-control" placeholder="Account Number" id="laccountnumber">
		</div>
		<div class="col-sm-4">
				<div class="form-group" id="ldeliverymethod-group">
					<select class="shipping_method_select2_single form-control" id="lshippingmethod" name="lshippingmethod">
						<option selected="selected" disabled="disabled" value="">Select Shipping Method</option>
					</select>								
				</div>					
		</div>				
	</div>*/ ?>
<div class="row">
	<div class="col-sm-6">
		<label class="sr-only" for="storename"></label>
		<input type="text" class="form-control" id="storename" name="storename" placeholder="Location Name (Optional)" value="<?=$location->storename?>">
	</div>
	<div class="col-sm-6">
		<label class="sr-only" for="storenum"></label>
		<input type="text" class="form-control" id="storenum" name="storenum" placeholder="Location Number (Optional)" value="<?=$location->storenum?>">				
	</div>
</div>
<br/>
<div class="row">
	<div class="col-sm-4" id="location_address-group">
		<label class="sr-only" for="address"></label>
		<input type="text" class="form-control" id="location_address" name="address" placeholder="Address (Required)" value="<?=$location->address?>">
		<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
	</div>
	<div class="col-sm-4" id="location_secondaddress-group">
		<label class="sr-only" for="address"></label>
		<input type="text" class="form-control" id="location_secondaddress" name="address2" placeholder="Second Address (Opt)" value="<?=$location->address2?>">
		<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
	</div>					
	<div class="col-sm-4" id="location_zip-group">
		<label class="sr-only" for="zip"></label>
		<input type="text" class="form-control location_zip" id="location_zip" name="zip" placeholder="Zip (Required)" value="<?=$location->zipcode?>">
	</div>					
</div>
<br/>
	<div class="row">
		<div class="col-sm-4" id="location_country-group">
			<label class="sr-only" for="country"></label>
			<input type="text" class="form-control location_country" id="location_country" name="country" placeholder="Country (Required)" value="<?=(!empty($location->country)) ? $location->country : ""?>">
			<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
		</div>					
		<div class="col-sm-4" id="location_city-group"> 
			<label class="sr-only" for="city"></label>
			<input type="text" class="form-control location_city" id="location_city" name="city" placeholder="City (Required)" value="<?=$location->city?>">
			<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
		</div>
		<div class="col-sm-4" id="location_state-group">
			<label class="sr-only" for="state"></label>
			<select name="state" class="form-control location_state state_location_select2_single" id="location_state">
				<option value="">Choose a State (Required)</option>
				<?php foreach (\app\models\State::find()->all() as $sate) :?>
					<option value="<?=$sate->code?>" <?php if($location->state==$sate->code) :?>selected<?php endif;?>><?=$sate->code?> - <?=$sate->state?></option>
				<?php endforeach;?>
			</select>	
		</div>
	</div>
<br/>
<div class="row">
	<div class="col-sm-6" id="location_email-group">
		<label class="sr-only" for="email"></label>
		<input type="email" class="form-control" id="location_email" name="email" placeholder="Email (Optional)" value="<?=$location->email?>">
	</div>
	<div class="col-sm-6">
		<label class="sr-only" for="phone"></label>
		<input type="tel" class="form-control" id="location_phone" name="phone" placeholder="Phone (Optional)" value="<?=$location->phone?>">
	</div>
</div>
<input type="hidden" class="form-control" id="l_customer" name="customerid" value="<?=$customer?>"/>
<input type="hidden" class="form-control" id="l_location" name="id" value="<?php echo (!empty($location->id)) ? $location->id : 0;?>"/>