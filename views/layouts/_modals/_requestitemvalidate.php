<?php 
	use app\models\Manufacturer;
	use app\models\Category;
	use app\models\Department;
	use yii\widgets\ActiveForm;
?>
<!-- Modal -->
<div class="modal fade" id="requestItemValidate" tabindex="-1" role="dialog" aria-labelledby="requestItemValidateLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="requestItemValidateModalLabel"> <span class="glyphicon glyphicon-list-alt"></span></h4>
	  </div>
		<div class="alert alert-success fade in" id="itemrequestvalidate-msg" style="display:none;"></div>
		<?php $form = ActiveForm::begin(["action"=>Yii::$app->getUrlManager()->createUrl('itemrequest/validate'), 'options' => ['enctype' => 'multipart/form-data', 'class'=>'form-group form-group-sm', 'id'=>'request-vitem-form']]); ?>
			<div class="modal-body">
				<div class="container">
					<div class="row">
					<div class="col-md-14" id="rv_description-group">
						<label for="description">Description</label>
						<textarea class="form-control" id="v_description" name="rv_description" rows="3" style="min-height:120px;resize:none;" placeholder="Description (Required)"></textarea>
					</div>
					</div>
					<div class="row row-margin"></div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback" id="rv_manpart-group">
							<label for="manpart">Manufacturer Part Number</label>
							<input type="text" class="form-control" id="v_manpart" name="rv_manpart" placeholder="Manufacturer Part Number (Required)">
							<span class="fa fa-keyboard-o form-control-feedback right" aria-hidden="true"></span>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback" id="rv_frupart-group">
							<label for="manpart">Fru part number</label>
							<input type="text" class="form-control" id="v_frupart" name="rv_frupart" placeholder="Fru Part Number (Optional)">
							<span class="fa fa-keyboard-o form-control-feedback right" aria-hidden="true"></span>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6 text-center">
							<div class="input-group">
								<span class="btn btn-default btn-file">
									<label for="manpart">Model Picture</label> <input type="file" accept="image/*" name="file" title="Click to add Images"/>
								</span>					
							</div>	                                      
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12 form-group" id="rv_manufacturer-group">
							<label for="manpart">Manufacturer</label>
							<select class="form-control" id="v_manufacturer" name="rv_manufacturer" style="width:270px;line-height: 32px;">
								<option selected="selected" disabled="disabled" value="">Select A Manufacturer</option>
								<?php foreach(Manufacturer::find()->all() as $man) :?>
									<?php //if(!empty($man->name)):?>
										<option value="<?php echo $man->id;?>"><?php echo $man->name;?></option>
									<?php //endif;?>
								<?php endforeach;?>
							</select>
						</div>
					</div>
					<div class="row row-margin"></div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12 form-group" id="rv_category-group">
							<select class="form-control" id="v_category" name="rv_category" style="width:270px;line-height: 32px;">
								<option selected="selected" disabled="disabled">Select A Category</option>
								<?php foreach(Category::find()->all() as $cat) :?>
									<?php //if(!empty($man->name)):?>
										<option value="<?php echo $cat->id;?>"><?php echo $cat->categoryname;?></option>
									<?php //endif;?>
								<?php endforeach;?>
							</select>
						</div>	
						<div class="col-md-6 col-sm-6 col-xs-12 form-group" id="rv_departement-group">
							<select class="form-control" id="v_departement" name="rv_departement" style="width:270px;line-height: 32px;">
							<option selected="selected" disabled="disabled">Select A Departement</option>
							<?php foreach(Department::find()->all() as $dep) :?>
								<?php //if(!empty($man->name)):?>
									<option value="<?php echo $dep->id;?>"><?php echo $dep->name;?></option>
								<?php //endif;?>
							<?php endforeach;?>
							</select>
						</div>		
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-success" value="Save" >
			</div>
			<input type="hidden" name="_requestid" />
		<?php ActiveForm::end(); ?>
	</div>
  </div>
</div>