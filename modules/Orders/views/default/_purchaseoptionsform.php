<?php 
	use app\models\ModelOption;
	use app\models\ItemHasOption;
	
	$cleaningoption = ItemHasOption::find()->innerJoin('lv_model_options', '`lv_model_options`.`id` = `lv_item_has_option`.`optionid`')
	->where(['orderid'=>$order->id])
	->andWhere(['itemid'=>$model->id])
	->andWhere(['optiontype'=>1, 'parent_id'=>0])
	->one();
	
	$testingoption = ItemHasOption::find()->innerJoin('lv_model_options', '`lv_model_options`.`id` = `lv_item_has_option`.`optionid`')
	->where(['orderid'=>$order->id])
	->andWhere(['itemid'=>$model->id])
	->andWhere(['optiontype'=>3, 'parent_id'=>0])
	->one();
?>
<style>
#cleaning-options-group .active,
#testing-options-group .active{color: rgb(255, 255, 255); box-shadow: rgb(187, 187, 187) 0px 0px 5px inset; background-color: rgb(38, 185, 154);}
</style>
<h3>Cleaning Opions :</h3>
<div class="form-group" id="cleaning-options-group">
	<div class="btn-group btn-group-justified" id="switch-cleaning-options" role="group" aria-label="Cleaning Options">
		<?php foreach (ModelOption::find()->where(['optiontype'=>1, 'level'=>1])->all() as $option) :?>
				<div class="btn-group" role="group">
			     	<button type="button" data-switch-set="cleaningoptiontype" data-switch-value="<?php echo $option->id;?>" class="btn btn-default bt-switch-btn <?php echo ($cleaningoption->optionid===$option->id) ? 'active' : '';?>"><?php echo $option->name;?></button>
				</div>           
		<?php endforeach;?>
	</div>	
</div>
<input type="hidden" value="" name="cleaning_option">
<h3>Testing Opions :</h3>
<div class="form-group" id="testing-options-group">
	<div class="btn-group btn-group-justified" id="switch-testing-options" role="group" aria-label="Testing Options">
		<?php foreach (ModelOption::find()->where(['optiontype'=>3, 'level'=>1])->all() as $option) :?>
               <div class="btn-group" role="group">
			   		<button type="button" data-switch-set="testingoptiontype" data-switch-value="<?php echo $option->id;?>" class="btn btn-default bt-switch-btn <?php echo ($testingoption->optionid===$option->id) ? 'active' : '';?>"><?php echo $option->name;?></button>		
				</div>
		<?php endforeach;?>
	</div>		
</div>
<input type="hidden" value="" name="testing_option">
<input type="hidden" name="orderId" value="<?php echo $order->id;?>"/>
<input type="hidden" name="modelId" value="<?php echo $model->id;?>"/>