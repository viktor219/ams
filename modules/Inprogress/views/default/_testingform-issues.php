<?php 
use yii\helpers\Html;
use app\models\Medias;
use app\models\Models;
?>
<div class="col-sm-6">
	<?php foreach ($_partitemstesting as $_partitemtesting) :?>
		<?php $_model = Models::findOne($_partitemtesting->partid);?>
		<?php $_media = Medias::findOne($_model->image_id);?>
		<div class="testing-box" id="testing-box_<?=$_partitemtesting->id?>" onClick="useExistingIssue(<?=$_withoutpartitemtesting->itemid?>);" data-background="#AFABDA" style="background-color: #DDD;border-radius:5px;cursor:pointer;font-size:12px;margin-bottom: 10px;padding:5px;color:#333">
			<img src="<?= Yii::getAlias('@web').'/public/images/models/'. $_media->filename?>" onClick="ModelsViewer(<?=$_partitemtesting->itemid?>);" height="33px" />
			<?= $_partitemtesting->problem;?>
		</div>
	<?php endforeach;?>
</div>
<div class="col-sm-6">
	<?php foreach ($_withoutpartitemstesting as $_withoutpartitemtesting) :?>
		<div class="testing-box" id="testing-box_<?=$_withoutpartitemtesting->id?>" onClick="useExistingIssue(<?=$_withoutpartitemtesting->itemid?>);" data-background="#AFABDA" style="background-color: #DDD;border-radius:5px;cursor:pointer;font-size:12px;margin-bottom: 10px;padding:5px;color:#333">
			<i><?= $_withoutpartitemtesting->problem;?></i>
		</div>
	<?php endforeach;?>
</div>