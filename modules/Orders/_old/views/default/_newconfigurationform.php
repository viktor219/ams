<?php 
	use app\models\ModelOption;
?>
<div class="row row-margin">
	Add Or Edit <b><?php echo $manufacturer->name . ' ' . $model->descrip;?></b> Configuration Options:
</div>
<div class="model-option-form">
	<div class="form-group">
		<select class="select2_config_option form-control inputs" id="selectCOption" name="coption">
			<option value="">Select An Existing Configuration</option>
			<?php foreach(ModelOption::findAll(['parent_id'=>0, 'optiontype'=>2, 'idmodel'=>$model->id]) as $_option) :?>
				<option value="<?php echo $_option->id;?>"><?php echo $_option->name;?></option>
			<?php endforeach;?>
		</select>		
	</div>
	<div id="loaded-option-form-content">
		<?php if(empty($option)) :?>
			<div class="form-group">
				<div id="modeloption-group">
					<label> Name :</label> <input id="modeloption-name" class="form-control" name="optionparentname" maxlength="100" type="text">
				</div>
			</div>
			<div class="form-group">
			    <div class="row row-margin">
			    	<div id="centry1" class="CclonedInput" style="padding:0 25px 0 25px;">
			    		<div class="form-group option-row-group">
			    			<label> Option :</label> <input type="text" class="form-control config_option" name="options[]" />
			    		</div>
			    	</div>
			    	<input type="hidden" name="idmodel" id="option_model_id" value="<?php echo $model->id;?>" />
			    	<input type="hidden" id="triggerRow" value="<?php echo $row;?>" />
				</div>
			</div>
		<?php else :?>
			<div class="form-group">
				<div id="modeloption-group">
					<label> Name :</label> <input id="modeloption-name" class="form-control" name="optionparentname" maxlength="100" type="text" value="<?php echo $option->name;?>">
				</div>
			</div>	
			<div class="form-group">
			    <div class="row row-margin">
			    	<?php foreach(ModelOption::findAll(['parent_id'=>$option->id, 'optiontype'=>2, 'idmodel'=>$model->id]) as $key=>$_option) :?>
				    	<div id="centry<?php echo ++$key;?>" class="CclonedInput" style="padding:0 25px 0 25px;">
				    		<div class="form-group option-row-group">
				    			<label> Option :</label> <input type="text" class="form-control config_option" name="options[]" value="<?php echo $_option->name;?>"/>
				    		</div>
				    	</div>
			    	<?php endforeach;?>
			    	<input type="hidden" name="idmodel" id="option_model_id" value="<?php echo $model->id;?>" />
			    	<input type="hidden" id="triggerRow" value="<?php echo $row;?>" />
				</div>
			</div>				
		<?php endif;?>
	</div>
   		<div class="row form-group">
			<div class="col-sm-10">
				<button class="btn btn-success btn-xs" id="btnAddCOption" type="button"><span class="glyphicon glyphicon-plus"></span></button>
				<button class="btn btn-success btn-xs" id="btnDelCOption" type="button"><span class="glyphicon glyphicon-minus"></span></button>
			</div>
			<div class="col-sm-2">
				<button type="button" class="btn btn-warning btn-xs" id="new-option-button"><span class="glyphicon glyphicon-refresh"></span> New</button>
			</div>
		</div>
</div>	