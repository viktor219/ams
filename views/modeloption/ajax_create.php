<?php 
	use app\models\ModelOption;
?>
<b>Configuration Options :</b>
<div class="btn-group" data-toggle="buttons">
	<?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0])->all() as $option) :?>
		<h4><b><?php echo $option->name;?></b></h4>
		<?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$option->id])->all() as $optionlv_2) :?>
			<?php if($optionlv_2->checkable) :?>
                                                        <input class="config_option" type="checkbox" value="<?php echo $optionlv_2->id;?>" name="config_option[1][]"> <?php echo $optionlv_2->name;?>
                                                    <?php else :?>
                                                    	<h5>- <?php echo $optionlv_2->name;?></h5>
                                                    	<?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$optionlv_2->id])->all() as $optionlv_3) :?>
                                                        	<input class="config_option" type="checkbox" value="<?php echo $optionlv_3->id;?>" name="config_option[1][]"> <?php echo $optionlv_3->name;?>
                                                    	<?php endforeach;?>
                                                    <?php endif;?>
                                                    <?php endforeach;?>
	<?php endforeach;?>
</div>