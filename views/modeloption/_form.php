<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ModelOption */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="model-option-form">

	<?php if($model->isNewRecord) :?>	
		<?php $form = ActiveForm::begin(); ?>
			<label> Name :</label> <input id="modeloption-name" class="form-control" name="name" maxlength="100" type="text">
		    <div class="row row-margin">
		    	<div id="entry1" class="clonedInput1" style="padding:0 25px 0 25px;">
		    		<div class="form-group">
		    			<label> Option :</label> <input type="text" class="form-control configoption" name="options[]" class="form-control" />
		    		</div>
		    	</div>
				<button class="btn btn-success btn-xs" class="btnAddCOption" type="button"><span class="glyphicon glyphicon-plus"></span></button>
				<button class="btn btn-success btn-xs" class="btnDelCOption" type="button"><span class="glyphicon glyphicon-minus"></span></button>
			</div>
		<?php ActiveForm::end(); ?>	
	<?php else :?>	
		<?php $form = ActiveForm::begin(); ?>
			<label> Name :</label> <input id="modeloption-name" class="form-control" name="name" value="<?php echo $model->name;?>" maxlength="100" type="text">
		    <div class="row row-margin">
		    	<div id="entry1" class="clonedInput1" style="padding:0 25px 0 25px;">
		    		<div class="form-group">
			    		<?php if(count($models)) :?>
							<?php foreach ($models as $optionlv_2) :?>
				    			<label> Option :</label> <input type="text" class="form-control configoption" value="<?php echo $optionlv_2->name;?>" name="options[]" class="form-control" />
				    		<?php endforeach;?>
				    	<?php else :?>
				    			<label> Option :</label> <input type="text" class="form-control configoption" name="options[]" class="form-control" />
				    	<?php endif;?>
		    		</div>
		    	</div>
				<button class="btn btn-success btn-xs" class="btnAddCOption" type="button"><span class="glyphicon glyphicon-plus"></span></button>
				<button class="btn btn-success btn-xs" class="btnDelCOption" type="button"><span class="glyphicon glyphicon-minus"></span></button>
			</div>
		<?php ActiveForm::end(); ?>
	<?php endif;?>
	
</div>

<script>
		$(document).ajaxComplete(function(){
			$('.btnAddCOption').click(function () {
				//alert('clicked');
				var num     = $('.clonedInput1').length, // how many "duplicatable" input fields we currently have
					newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
					newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
				// Title - select
				newElem.find('.configoption').attr('id', 'ID' + newNum + '_title').attr('name', 'options[]').val('');
	
			// insert the new element after the last "duplicatable" input field
				$('#entry' + num).after(newElem);
				$('#ID' + newNum + '_title').focus();
		 
			// enable the "remove" button
				$('.btnDelCOption').attr('disabled', false);
			// right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
				if (newNum == 10)
					$('.btnAddCOption').attr('disabled', true).prop('value', "You've reached the limit");
			});
			//
			$('.btnDelCOption').click(function () {
				// confirmation
					var num = $('.clonedInput1').length;
				// how many "duplicatable" input fields we currently have
					$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
				// if only one element remains, disable the "remove" button
					if (num -1 === 1)
						$('.btnDelCOption').attr('disabled', true);
				// enable the "add" button
					$('.btnAddCOption').attr('disabled', false).prop('value', "add section");}); 
				// enable the "add" button
					$('.btnAddCOption').attr('disabled', false);
					return false;
				});
			 
				$('.btnDelCOption').attr('disabled', true);	
		});
</script>