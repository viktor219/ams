<?php 
	use app\models\Medias;
	use yii\helpers\Html;
	use yii\helpers\Url;
	
	$_my_media = Medias::findOne($customer->picture_id);
	if(!empty($_my_media->filename)){
		$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
		if (file_exists(dirname(__FILE__) . '/../../public/images/customers/'.$_my_media->filename)) 
			$_output_picture = Html::img($target_file, ['class'=>'showCustomer', 'alt'=>$customer->companyname, 'style'=>'cursor:pointer;', 'width'=>'150']);						 
		else
			$_output_picture = $customer->companyname;					
	}else 
		$_output_picture = $customer->companyname;	
?>
<table class="page_header">
	<tr>
		<td style="width: 50%; text-align: left">
		<span class="largeheadertext"><?php if ($storenum != NULL) {echo 'STORE NUMBER: <strong>' . $location->storenum . '</strong>'; } ?></span>
		</td>
		<td style="width: 50%; text-align: right">
		<span class="largeheadertext">SHIPMENT MANIFEST</span>
		</td>
	</tr>
</table>
<table class="page_body">
  <tr>
	<td style="width: 50%;">
	<table>
	  <tr>
		<td><?= $_output_picture;?></td>
		<td class="center"><span style="font-size: 12px;"><?php if ($location->storename != NULL) { echo $location->storename; }?> <?php if ($location->storenum != NULL) { echo 'Store#: <strong>' . $location->storenum . '</strong>'; } ?><br />
		  <?php echo $location->address; ?><br />
		  <?php if ($location->address2 != NULL) {echo '<small>' . $location->address2 . '</small><br>'; } ?>
		  <?php echo $location->city . ', ' . $location->state . ', ' . $location->zipcode . ''; ?></span></td>
	  </tr>
	</table>
	</td>
	<td style="width: 49%;" align="right">
	  <strong>Shipped Via:</strong> <?php 
	  if ($model->trackinglink == 1) {echo 'UPS';}
	  if ($model->trackinglink == 2) {echo 'Fedex';}
	  if ($model->trackinglink == 3) {echo 'Estes';}
	  if ($model->trackinglink == 4) {echo 'Other';}
	   ?><br />
	  <strong>Shipped On: </strong><?php echo $model->dateshipped; ?> <br />
	  <strong>Tracking Number: </strong><?php echo $model->trackingnumber; ?>
	<?php if (!empty($order->enduser_po)) {echo '<br><strong>End User Order#:</strong> ' . $order->enduser_po ; } ?>
	</td>
  </tr>
</table>   
<?php
	if ($countSerializedresults >= 1) { $calcwidth = 100 / $countSerializedresults; }
	$calcchar = 30;
	if ($countSerializedresults >= 3) { $calcchar = 28; }
	if ($countSerializedresults >= 4) { $calcchar = 28; }
	if ($countSerializedresults >= 5) { $calcchar = 28; }
	if ($countSerializedresults >= 6) { $calcchar = 28; }
	if ($countSerializedresults >= 7) { $calcchar = 28; }
	if ($countSerializedresults >= 8) { $calcchar = 24; }
	if ($countSerializedresults >= 8) { $calcwidth = 100 / 8; }
	$numrowcount = 0;
	foreach($serializedshippingresults as $serializedshippingresult)
	{
		if ($numrowcount == 0)
		{
			echo '<table class="page_body2" style="width:100%;"><tr style="width:100%;">';	
		}
		$totalready=$serializedshippingresult["counted"];	
		$mfr=$serializedshippingresult["name"];	
		$descrip=$serializedshippingresult["descrip"];	
		$itemsmodel=$serializedshippingresult["model"];
		echo '<td style="width:' . $calcwidth . '%;" class="center" valign="top">' . substr($descrip,0,$calcchar) . '<br><strong>Qty: ' . $totalready . '</strong>';
				
				echo '<br>';
				
				$checkserials = "SELECT serial, terminalnum FROM lv_items 
								 WHERE model=:model AND ordernumber=:shipmentid AND serial != 'No Serial' ORDER BY terminalnum, serial";
				
				$connection = Yii::$app->getDb();
				
				$command = $connection->createCommand($checkserials, [':model'=>$itemsmodel, ':shipmentid'=>$model->id]);
	
				$checkserialsresults = $command->queryAll();	
				
				$serialrowcount = 0;
				
				$num_rows = count($checkserialsresults);
				
				echo '<small><b>Serial Number';
				if ($num_rows > 1)
				{
					echo 's';	
				}
				echo ':</b><br>';	

				foreach ($checkserialsresults as $checkserialsresult)
				{
					$serialrowcount == 1;
					if ($serialrowcount < 50)
						{

					$serial=$checkserialsresult['serial'];
					$terminalnum=$checkserialsresult['terminalnum'];
					if ($terminalnum != NULL)
					{
					echo 'Terminal # ' . $terminalnum . ': ';
					}
					echo $serial;
						echo '<br>';
					$serialrowcount ++;
						}
				}
						echo '</small>';
		echo '</td>';
		$numrowcount ++;
		if ($numrowcount == 8 || $numrowcount == 16 || $numrowcount == 24 || $numrowcount == 32)
		{
			echo '</tr><tr>';	
		}
	}
	if ($numrowcount > 0)
	{
		echo '</tr></table>';
	}
	
/* ////////////////////////////////////// */

	if ($countUnSerializedresults >= 1) { $calcwidth = 100 / $countUnSerializedresults; }
	$calcchar = 30;
	if ($countUnSerializedresults >= 3) { $calcchar = 28; }
	if ($countUnSerializedresults >= 4) { $calcchar = 28; }
	if ($countUnSerializedresults >= 5) { $calcchar = 28; }
	if ($countUnSerializedresults >= 6) { $calcchar = 28; }
	if ($countUnSerializedresults >= 7) { $calcchar = 28; }
	if ($countUnSerializedresults >= 8) { $calcchar = 24; }
	if ($countUnSerializedresults >= 8) { $calcwidth = 100 / 8; }
	$numrowcount = 0;
	$countrows2 = 0;
	foreach($unserializedshippingresults as $unserializedshippingresult)
	{
		if ($countrows == 0)
		{
			echo '<table class="page_body2" style="width:100%;">';
		}
		$totalready=$unserializedshippingresult['counted'];	
		$mfr=$unserializedshippingresult['name'];	
		$descrip=$unserializedshippingresult['descrip'];	
		$itemsmodel=$unserializedshippingresult['model'];
		$countrows ++;
		$countrows2 ++;
		if ($countrows == 1 || $countrows == 9 || $countrows == 17 || $countrows == 25 || $countrows == 33 || $countrows == 41 || $countrows == 49 || $countrows2 == 57)
		{ 
			echo '<tr style="width:100%;">'; 
			$rowon = 1;
		}
		echo '<td style="width:' . $calcwidth . '%;" class="center" valign="top">' . substr($descrip,0,$calcchar) . '<br><strong>Qty: ' . $totalready . '</strong>';
		echo '</td>';
		if ($countrows2 == 8 || $countrows2 == 16 || $countrows2 == 24 || $countrows2 == 32 || $countrows2 == 40 || $countrows2 == 48 || $countrows2 == 56)
		{ 
			echo '</tr>';  
			$rowon = 0;
		}
		
	}
	if ($rowon == 1)
	{
		echo '</tr>';
		$rowon = 0;
	}
	if ($countrows > 0)
	{
		echo '</table>';
	}
	
	/* ////////////////////////////////////// */
	
  if (!empty($order->notes))
  {
	?>  
				<table class="page_body5">
				<tr><td>
	<?php echo $order->notes; ?>
				</td></tr>
				</table>
	<?php
  }
?>
<table>
	<tr>
		<td>
			<table cellspacing="10">
				<tr rowspan="3"><td></td></tr>
				<tr>
					<td style="text-align: right">
						Signature: ____________________________
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>