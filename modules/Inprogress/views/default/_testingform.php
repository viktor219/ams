<?php 
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use app\vendor\PHelper;
	use app\models\Medias;
	use app\models\Models;
	
	//$color = PHelper::GenerateRandomColor();
?>
<div class="row row-margin">
	<div id="issue_select_text" style="display:none;text-align:right;"><i><span id="nbr_issue_selected">0</span> issues selected</i></div>
	<!-- <div id="issue_bar_color_legend" style="display:none;">
		Issue Bar Color : <span id="append_issue_color"></span>
	</div> -->
</div>
<?php $form = ActiveForm::begin(['action'=>['/inprogress/turntotesting'], 'options' => ['id'=>'new-testing-form']]); ?>
	<div id="existing-issue" style="border: 1px solid #ddd;padding: 10px;padding-top: 0;">	 
		<div class="row row-margin">
			<div class="row">
				<div class="col-md-11">
					<h2 style="text-align: left; padding:0;"><i class="fa fa-bars"></i> Existing Issues</h2>
				</div>
				<div class="col-md-1">
					<?= Html::submitButton('<span class="glyphicon glyphicon-wrench"></span>', ['class' => 'btn btn-sm btn-success', 'style'=>'display:none;', 'id'=>'testing-button']) ?>
				</div>
			</div>
			<hr style="margin: 0 0 5px 0"/>
			<div class="form-group" id="load-testing-form-content">
				<?php if(count($_partitemstesting) != 0 || count($_withoutpartitemstesting) != 0) :?>
					<?=$this->render('_testingform-issues', ['_partitemstesting'=>$_partitemstesting, '_withoutpartitemstesting'=>$_withoutpartitemstesting]) ?>
				<?php else:?>
					<div style="text-align: center;font-weight: bold;"><i>No existing issues for <?=$manufacturer->name;?> <?=$_model->descrip;?></i></div>
				<?php endif;?>
			</div>
		</div>
	</div>	
	<input type="hidden" id="itemId" name="itemId" value="<?=$model->id;?>">
<?php ActiveForm::end(); ?>
<div id="add-new-issue" style="margin-top:15px;border: 1px solid #ddd;padding: 10px;padding-top: 0;">
	<?php $form = ActiveForm::begin(['action'=>['/inprogress/addnewissue'], 'options' => ['id'=>'add-issue-form']]); ?> 
		<div class="row row-margin">
			<h2 style="text-align: left; padding:0;"><i class="fa fa-bars"></i> Add A New Issue</h2>
			<hr style="margin: 0"/>
			<div class="form-group" id="r_problem-group">
				<label for="description">Problem <!-- <span class="small">(tape for use existing problem or add new)</span> --></label>
				<input type="text" id="autocomplete-problem" class="form-control" name="problem" placeholder="Find problem..." autocomplete="off" autofocus>	
			</div>
			<div class="form-group" id="r_description-group">
				<label for="description">Resolution</label>
				<textarea class="form-control " id="r_resolution" name="resolution" rows="1" style="resize:none;" placeholder="Resolution (Required)"></textarea>
			</div>
			<div class="form-group" id="r_part-group">
				<label for="rpart_used">Part used</label>
				<input class="typeahead form-control input_fn" type="text" name="description" id="autocompleteitem" placeholder="Select an Item" data-provide="typeahead" autocomplete="off">
				<input class="form-control input_h" type="hidden" name="modelid" id="autocompletevalitem" />	
			</div>
		</div>
		<div class="row row-margin">
			<div class="col-md-12 text-right">
				<?= Html::submitButton('<span class="glyphicon glyphicon-share-alt"></span>',['class' => 'btn btn-sm btn-success']) ?>
			</div>
		</div>
		<input type="hidden" id="itemId" name="itemId" value="<?=$model->id;?>">
	<?php ActiveForm::end(); ?>
</div>