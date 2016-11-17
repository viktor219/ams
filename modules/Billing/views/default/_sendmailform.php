<?php 
	use yii\helpers\Html;
	use app\models\Location;
	use app\models\QsalesorderMail;
	use app\models\SalesorderMail;
	use app\models\Medias;
	
	$location = Location::findOne($model->location_id);
	//$_mail_content = "";	
	$_subject = "Your Invoice: $invoiceModel->invoicename";
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
		<textarea class="form-control" name="body" style="min-height:120px;resize:none;" id="model_descrip" placeholder="Description (Required)"><?php // echo Html::encode($_mail_content);?>Your invoice has been generated! Please find the attached invoice. Don’t hesitate to call us at 1-864-331-8678 if you have any questions or concerns. We’re here to help!</textarea>
	</div>
</div>
<div class="form-group">
	<label for=""><?php if($type==1) :?>Sales Order:<?php else : ?>Invoice<?php endif;?></label>
	<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo $current_file;?></button>
	<?php if(!empty($model->orderfile)) :?>
	<div style="margin-top: 10px;">
		<label for="">Customer File:</label>
		<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo Medias::findOne($model->orderfile)->filename;?></button>
	</div>
	<?php endif;?>
</div>
<input type="hidden" value="<?php echo $type;?>" name="type"/>
<input type="hidden" value="<?php echo $invoiceModel->id;?>" name="invoiceId"/>