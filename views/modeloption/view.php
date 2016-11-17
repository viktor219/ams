<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\ModelOption;

?>
<div class="model-option-view" style="padding : 10px;">
	<div class="row row-margin">
		<?php foreach ($models as $option) :?>
			<h4><b><?php echo $option->name;?></b></h4>
			<?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$option->id])->all() as $optionlv_2) :?>
				<?php if($optionlv_2->checkable) :?>
		            <div class="row row-margin"><li> <?php echo $optionlv_2->name;?></li></div>
		        <?php else :?>
		             <h5>- <?php echo $optionlv_2->name;?></h5>
		            <?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$optionlv_2->id])->all() as $optionlv_3) :?>
		           		<div class="row row-margin"><li> <?php echo $optionlv_3->name;?></li></div>
		   			<?php endforeach;?>
		       <?php endif;?>
		   <?php endforeach;?>
		<?php endforeach;?>
	</div>
<?php /*
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idmodel',
            'name',
            'optiontype',
            'level',
            'parent_id',
            'checkable',
        ],
    ]) ?>
*/
?>
</div>