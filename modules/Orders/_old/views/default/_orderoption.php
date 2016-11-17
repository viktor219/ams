<?php 
	use yii\helpers\Html;
	use app\models\ModelOption;
?>
<?php foreach ($models as $option) :?>
	<div class="row row-margin">
		<a href="javascript:;" class="btn btn-warning" onClick="loadEditConfigurationsOption('<?php echo $option->id;?>', '');">Edit</a> <b><?php echo $option->name;?></b> :
		<?php $have_noncheckable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>0])->count();?>
		<?php $have_checkable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>1])->count();?>
		<?php if($have_checkable_submodels) { ?>
			<?php $submodels = ModelOption::find()->select('name')->where(['parent_id'=>$option->id, 'checkable'=>1])->asArray()->all(); ?>
			<?php  
				$_data = array();
				foreach ($submodels as $submodel)
				{
					//$_data[] = $submodel['name'];
					?>
					<input class="config_option" type="checkbox" value="<?php echo $optionlv_2->id;?>" name="config_option[<?php echo $entry_no;?>][]"> <?php echo $optionlv_2->name;?>
					<?php 
				}
			?>
			<?php  //$types = implode(' ', $_data);?>
			<?php //echo $types;?> 
			
		<?php }?>
	</div>
<?php endforeach;?>	