<?php 
	use yii\helpers\Html;
	use app\models\ModelOption;
?>
<?php foreach ($models as $option) :?>
	<div class="row row-margin">
		<?php $have_noncheckable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>0])->count();?>
		<?php $have_checkable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>1])->count();?>
		<?php if($have_checkable_submodels) { ?>
			<?php $submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>1])->all(); ?>
			<?php echo $option->name;?> :
			<?php   
				$_data = array();
				if(!empty($ordertype) && $ordertype==1)
				{
					?><select class="select2_option form-control" name="config_option[<?php echo $entry_no;?>][]"><?php
					foreach ($submodels as $submodel)
					{
					?>
						<option value="<?php echo $submodel->id;?>" <?php echo json_encode($customer);?><?php if(!empty($_findlastorderoptions) && in_array($submodel->id, $_findlastorderoptions)) :?> selected <?php endif;?>> <?php echo $submodel->name;?></option>
					<?php 
					}
					?></select><?php
				} else {
					foreach ($submodels as $submodel)
					{
						?>
						<input class="config_option" type="checkbox" value="<?php echo $submodel->id;?>" name="config_option[<?php echo $entry_no;?>][]"> <?php echo $submodel->name;?>
						<?php 
					}				
				}
			?>			
		<?php }?>
	</div>
<?php endforeach;?>	