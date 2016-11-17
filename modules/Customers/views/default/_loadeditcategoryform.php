<?php 
	use app\models\Category;
	use app\models\Department;
?>
<div class="row row-margin">
	<div class="form-group">
		<label for="category">Category</label>
		<select class="form-control" name="category">
			<?php foreach($categories as $cat) :?>
				<option <?php if($cat->id==$model->category_id) :?> selected<?php endif;?> value="<?php echo $cat->id;?>"><?php echo $cat->categoryname;?></option>
			<?php endforeach;?>
		</select>	
	</div>
</div>
<div class="row row-margin">
	<div class="form-group">
		<label for="department">Department</label>
		<select class="form-control" name="department">
			<?php foreach($departments as $dep) :?>
				<option <?php if($dep->id==$model->department) :?> selected<?php endif;?> value="<?php echo $dep->id;?>"><?php echo $dep->name;?></option>
			<?php endforeach;?>
		</select>	
	</div>
</div>
<input type="hidden" id="modelId" name="modelId" value="<?php echo $model->id;?>" />