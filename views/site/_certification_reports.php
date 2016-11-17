<?php 
	$count = count($models);
	$columns = 12;
	
	//echo $columns;
	
	//$maxRows= 
	$v=0;
	
	//$table_break = array(80, 160, 240, 320, 400, 480, 560, 640, 720, 800, 880, 960, )
?>
<div style="font-weight:bold;position:absolute; top: 15px;right:20px">Date : 09/20/2016</div>
<section style="overflow-y:auto;" id="generated_pdf">
	<div style="height:120px"></div>
	<div style="text-align: center;font-weight:bold;">
		Radius Solutions - Project MX 7200 <br/>
		Model : Verifone MX860 Payment Terminal
	</div>
	<div class="serials">
		<table>
			<tr>
				<?php for ($i=0; $i<=$columns; $i++) :?>
					<th>Serial</th>
				<?php endfor;?>
			</tr>
				<?php for ($i=0; $i<56;$i++) : ?>
					<tr>
						<?php for ($j=0; $j<=$columns; $j++) :?>
							<td><?php echo $models[$v]->serialnumber;?></td>
							<?php $v++?>
						<?php endfor;?>
					</tr>
				<?php endfor;?>		
		</table>
	</div>
</section>