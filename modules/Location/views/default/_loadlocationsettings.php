	<?php if(!empty($_location->connection_type) || !empty($_location_details->ipaddress) || !empty($_location_details->gateway) || !empty($_location_details->primary_dns) || !empty($_location_details->secondary_dns)) :?>
		<?php if(!empty($_location->storenum)) :?>
			<div class="row">
				<h4 style="font-weight: bold;">Store#: <?= $_location->storenum?> Configuration Settings</h4>
			</div>
		<?php endif;?>
		<?php if(!empty($_location->connection_type)) :?>
			<div class="row">
				Connection Type : <b><?= $_location->connection_type;?></b>
			</div>
		<?php endif;?>
			<?php if(!empty($_location->connection_type) && strtolower($_location->connection_type)=='dial-up') :?>
				<div class="">
					Require "9" to dial out : <b><?php echo ($_location_details->require_dialout==1) ? 'Yes' : 'No'?></b>
				</div>
			<?php endif;?>
			<?php if(!empty($_location_details->ipaddress)) :?>
			<div class="row">
				IP Address : <b><?= $_location_details->ipaddress;?></b>
			</div>
			<?php endif;?>
			<?php if(!empty($_location_details->subnet_mask)) :?>
			<div class="row">
				Subnet Mask : <b><?= $_location_details->subnet_mask;?></b>
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