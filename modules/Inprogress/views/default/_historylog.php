<div>
	<?php foreach ($models as $model) :?>
		<?php 
				$dates = split(",", $model["rows_dates"]);
				$problems = split(",", $model["rows_problems"]);
		?>
		<?php foreach ($dates as $key=>$date) : ?>
			<div style="text-align: center;font-weight: bold;"><?php echo date("F d, Y", strtotime($date));?></div>
			<hr style="margin: 0 0 5px 0"/>
			<div style="margin-left: 60px;margin-top: 10px; margin-bottom: 10px;">
				<?php foreach ($problems as $key=>$problem) : ?> 
					<div class="row">
						&raquo;<i>This item was serviced and had a <b><?=$problem;?></b></i>
					</div>
				<?php endforeach;?>
			</div>
		<?php endforeach;?>
	<?php endforeach;?>
</div>