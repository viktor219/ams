<div class="row form-group">
	<div class="col-md-6">
		<label>Connection Type</label>
		<select id="connection_type" name="connection_type" class="form-control">
			<option value="">Select Connection Type</option>
			<option <?php if(strtolower($location->connection_type)=='dial-up') :?>selected<?php endif;?> value="Dial-up">Dial-up</option>
			<option <?php if(strtolower($location->connection_type)=='dhcp' || empty($location->connection_type)) :?>selected<?php endif;?> value="DHCP">DHCP</option>
			<option <?php if(strtolower($location->connection_type)=='static') :?>selected<?php endif;?> value="Static">Static</option>
			<option <?php if(strtolower($location->connection_type)=='wireless') :?>selected<?php endif;?> value="Wireless">Wireless</option>
		</select>
	</div>
	<div class="col-md-6">
		<label>IP Address</label>
		<input type="text" class="form-control" name="ip_address" placeholder="IP Address" value="<?= $locationdetail->ipaddress;?>">
	</div>
</div>
<div class="row form-group">
	<div class="col-md-6">
		<label>Gateway</label>
		<input type="text" class="form-control" name="gateway" placeholder="Gateway" value="<?= $locationdetail->gateway;?>">
	</div>
	<div class="col-md-6">
		<label>Primary DNS</label>
		<input type="text" class="form-control" name="primary_dns" placeholder="Primary DNS" value="<?= $locationdetail->primary_dns;?>">
	</div>
</div>
<div class="row form-group">
	<div class="col-md-6">
		<label>Secondary DNS</label>
		<input type="text" class="form-control" name="secondary_dns" placeholder="Secondary DNS" value="<?= $locationdetail->secondary_dns;?>">
	</div>
	<div class="col-md-6">
		<label>WINS Server</label>
		<input type="text" class="form-control" name="wins_server" placeholder="WINS Server" value="<?= $locationdetail->wins_server;?>">
	</div>
</div>
<div class="row form-group">
	<div class="col-md-6">
		<label>Subnet Mask</label>
		<input type="text" class="form-control" name="subnet_mask" placeholder="Subnet Mask" value="<?= $locationdetail->subnet_mask;?>">
	</div>
	<div class="col-md-6" id="requireninedialout-group" <?php if(strtolower($location->connection_type)!='dial-up') :?>style="display:none;"<?php endif;?>>
		<label>Require "9" to dial out</label>
		<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requireninedialout" name="requireninedialout" <?php echo ($locationdetail->require_dialout==1) ? "checked" : '';?>>	
	</div>
</div>
<input type="hidden" class="form-control" value="<?= $location->id;?>" name="locationId">
<script>							
    $("[name='requireninedialout']").bootstrapSwitch("size", "mini");
</script>