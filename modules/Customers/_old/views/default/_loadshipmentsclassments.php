<?php foreach($shipments as $shipment) :?>
    <?php 
        $percent = ($shipment['nb_customer_shipments'] / $total_shipments[0]) * 100;
        $percent = number_format($percent, 2);    
    ?>
	<div class="animated flipInY">
		<p><?php echo $shipment['companyname'];?> (<b><?php echo $shipment['nb_customer_shipments'];?></b> Shipments)</p>
		<div class="">
			<div class="progress progress_sm">
				<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $percent; ?>"></div>
			</div>
		</div>
	</div>
<?php endforeach;?>