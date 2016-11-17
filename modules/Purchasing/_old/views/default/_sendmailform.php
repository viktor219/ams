<?php 
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Users;

$_itempurchased = Itemspurchased::find()->where(['ordernumber'=>$model->id])->one();
$_model = Models::findOne($_itempurchased->model);
$_man = Manufacturer::findOne($_model->manufacturer);
$_user = Users::findOne($model->user_id);

$_mail_content = "
  I have attached our PO for (".$_itempurchased->qty.") ". $_man->name ." ". $_model->descrip .". Please reference PO#". $model->number_generated ." on all packages for this order. Have a great day!

	Thanks,
	" . $_user->firstname ." " . $_user->lastname ."
	" . $_user->email ."	
";
?>

<div class="form-group">
	<div class="" id="m_to-group">
		<label for="">To :</label>
		<input type="text" class="form-control" name="to" value="<?php echo $vendor->email;?>" readonly="readonly">
	</div>						
</div>	
<div class="form-group">
	<div class="" id="m_subject-group">
		<label for="">Subject :</label>
		<input type="text" class="form-control" name="subject" value="Purchase Order - PO#<?php echo $model->number_generated;?>" readonly="readonly">
	</div>						
</div>			
<div class="form-group">
	<div class="" id="m_content-group">
		<label for="description">Additional Content</label>
		<textarea class="form-control" name="body" style="min-height:120px;resize:none;" id="model_descrip" placeholder="Description (Required)" required><?php echo $_mail_content?></textarea>
	</div>
</div>
<div class="form-group">
	<label for="">Attached :</label>
	<button type="button" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <?php echo $current_file;?></button>
</div>