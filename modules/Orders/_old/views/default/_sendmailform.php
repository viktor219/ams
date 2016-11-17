<?php 
	use yii\helpers\Html;
	use app\models\Location;
	use app\models\QsalesorderMail;
	use app\models\SalesorderMail;
	use app\models\Medias;
	
	$location = Location::findOne($model->location_id);
	
	$output = "";
	if(!empty($model->number_generated))
		$output = $model->number_generated;  
	else 
	{
		if(!empty($location->storenum))
			$output = "Store#" . $location->storenum . " - ";
		else if(!empty($location->storename))
			$output = $location->storename  . ' - '; 
	}
	
	//$_mail_content = "";
	
	$_subject = "Your Order: $output";
	
	if($type == 1)
		$mailreports = SalesorderMail::find()->where(['orderid'=>$model->id])->groupBy('email')->all();
	else 
		$mailreports = QsalesorderMail::find()->where(['orderid'=>$model->id])->groupBy('email')->all();
?>

<div class="form-group">
	<div class="" id="m_to-group">
		<label for="">To :</label>
		<input type="text" class="form-control" name="to" value="<?php echo $customer->email;?>">
	</div>						
</div>	
<div class="form-group">
	<div class="" id="m_to-group">
		<label for="">CC  :</label> (<small>Separate each by "<b>;</b>"</small>)
		<input type="text" class="form-control" name="cc">
	</div>						
</div>
<div class="form-group">
	<div class="" id="m_subject-group">
		<label for="">Subject :</label>
		<input type="text" class="form-control" name="subject" value="<?php echo $_subject;?>" readonly="readonly">
	</div>						
</div>			
<div class="form-group">
	<div class="" id="m_content-group">
		<label for="description">Message :</label>
		<textarea class="form-control" name="body" style="min-height:120px;resize:none;" id="model_descrip" placeholder="Description (Required)"><?php // echo Html::encode($_mail_content);?>We’ve received your order! Our team will process it as quickly as possible and confirm when your order has left our facility. Don’t hesitate to call us at 1-864-331-8678 if you have any questions or concerns. We’re here to help!</textarea>
	</div>
</div>
<div class="form-group">
	<label for=""><?php if($type==1) :?>Sales Order:<?php else : ?>Quote Sales Order<?php endif;?></label>
	<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo $current_file;?></button>
	<?php if(!empty($model->orderfile)) :?>
	<div style="margin-top: 10px;">
		<label for="">Customer File:</label>
		<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo Medias::findOne($model->orderfile)->filename;?></button>
	</div>
	<?php endif;?>
</div>
<?php if(count($mailreports) > 0) :?>
	<h4>Mail Reports :</h4>
	<?php foreach($mailreports as $mailreport) :?>
	<?php 
		if($type==1)
			$dates = SalesorderMail::find()->where(['orderid'=>$model->id, 'email'=>$mailreport->email])->groupBy('date(date_sent)')->all();
		else 
			$dates = QsalesorderMail::find()->where(['orderid'=>$model->id, 'email'=>$mailreport->email])->groupBy('date(date_sent)')->all();
		$sender_dates = array();
	?>
		<?php 
			foreach($dates as $date)
			{
				$sender_dates[] = date('m/d/Y', strtotime($date->date_sent)); 
			}
			//var_dump($dates);
		?>
		<i>This order was emailed to <b><?=$mailreport->email;?></b> on <b><?= implode(', ', $sender_dates);?></b></i><br/>
	<?php endforeach;?>
<?php endif;?>
<input type="hidden" value="<?php echo $model->id;?>" name="<?php if($type==1) :?>orderId<?php else : ?>qorderId<?php endif;?>"/>
<input type="hidden" value="<?php echo $type;?>" name="type"/>