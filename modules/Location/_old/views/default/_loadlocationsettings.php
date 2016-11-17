	<?php if(!empty($_location->connection_type) || !empty($_location_details->ipaddress) || !empty($_location_details->gateway) || !empty($_location_details->primary_dns) || !empty($_location_details->secondary_dns)) :?>
		<div class="col-md-3">
			<?php if(!empty($_location->storenum)) :?>
				<div class="row">
					Store#: <?= $_location->storenum?> Configuration Settings
				</div>
			<?php endif;?>
			<?php if(!empty($_location->connection_type)) :?>
				<div class="row">
					Connection Type : <?= $_location->connection_type;?>
				</div>
			<?php endif;?>
				<?php if(!empty($_location_details->ipaddress)) :?>
				<div class="row">
					IP Address : <?= $_location_details->ipaddress;?>
				</div>
				<?php endif;?>
				<?php if(!empty($_location_details->gateway)) :?>
				<div class="row">
					Gateway : <?= $_location_details->gateway;?>
				</div>
				<?php endif;?>
				<?php if(!empty($_location_details->primary_dns)) :?>
				<div class="row">
					Primary DNS : <?= $_location_details->primary_dns;?>
				</div>
				<?php endif;?>
				<?php if(!empty($_location_details->secondary_dns)) :?>
					<div class="row">
						Secondary DNS : <?= $_location_details->secondary_dns;?>
					</div>
				<?php endif;?>
		</div>
	<?php endif;?>