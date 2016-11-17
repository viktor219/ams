<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Partnumbertype;
use app\models\Manufacturer;
use app\models\Department;
use app\models\Category;
use app\models\ModelsPicture;
use app\models\Customer;
use app\assets\AppAsset;
use app\models\ModelOption;
use app\models\Vendor;
use app\models\Medias;

/* @var $this yii\web\View */
/* @var $model app\models\Inventory */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
	.stepContainer{min-height:280px}
	.loaded-image-s
	{
	    max-width: 120px;
	    max-height: 80px;
	    border: 2px solid #BBB;
	    border-radius: 8px;
	    box-shadow: 0 0 10px #999;
	    padding: 5px;
	}
</style>

<?php if($model->isNewRecord) :?>
	<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/inventory_upload_create.js"></script>
<?php else :?>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#inventory-dropzone").dropzone({
				url: jsBaseUrl+"/ajaxrequest/uploadinventorypicture",
				addRemoveLinks: jsBaseUrl+"/inventory/removemodeluploaded",
				dictDefaultMessage:  '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&amp;Drop files here</h3> <span style="display:inline-block; margin: 6.5px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
				dictRemoveFile: "Delete",
				dictRemoveFileConfirmation: "Are you sure to delete this file ?",
				paramName: "files",
				init: function() {
					this.on('removedfile',function(file){
						var fileName = file.name;
						$.ajax({
							url: jsBaseUrl+"/inventory/removemodeluploaded?file="+fileName,
						});
					});
					//
					this.on('sending', function(file, xhr, formData){
						formData.append('_csrf', jsCrsf);
					});
					//prevent duplicates
					this.on("addedfile", function(file) {
					    if (this.files.length) {
					        var _i, _len;
					        for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
					        {
					            if(this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString())
					            {
					                this.removeFile(file);
					            }
					        }
					    }
					});					
				    var mock;
					<?php foreach ($files as $file): ?>
						mock = { 
							url: "<?php echo Yii::$app->request->baseUrl;?>/public/images/models/<?php echo Medias::findOne($file->mediaid)->filename;?>",
							name: "<?php echo Medias::findOne($file->mediaid)->filename;?>",
							size: <?php echo strlen(file_get_contents('public/images/models/' . Medias::findOne($file->mediaid)->filename));?>
						};					
				        this.files.push(mock);
				        this.emit('addedfile', mock);
				        this.createThumbnailFromUrl(mock, mock.url); 
				        this.emit('complete', mock);				    
				     <?php endforeach;?>
				}
			});
		});
	</script>
<?php endif;?>
<div class="modal fade" id="showConfigurations" tabindex="-1" role="dialog" aria-labelledby="showConfigurationsLabel">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-globe"></span> View All configurations</h4>
	  </div>
	  	<div id="showConfigurationsView" class="row row-margin">
			<?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>0])->all() as $option) :?>
				<div class="row row-margin">
						<a href="javascript:;" class="btn btn-warning" onClick="loadEditConfigurationsOption('<?php echo $option->id;?>', '');">Edit</a> <b><?php echo $option->name;?></b> :
						<?php $have_noncheckable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>0])->count();?>
						<?php $have_checkable_submodels = ModelOption::find()->where(['parent_id'=>$option->id, 'checkable'=>1])->count();?>
						<?php /*if($have_checkable_submodels) {?>
							<?php $submodels = ModelOption::find()->select('name')->where(['parent_id'=>$option->id, 'checkable'=>1])->asArray()->all(); ?>
							<?php  
								$_data = array();
								foreach ($submodels as $submodel) 
								{
									$_data[] = $submodel['name'];
								}
							?>
						<?php $types = implode(',', $_data);?>
						<div class="row row-margin">
							<a href="javascript:;" class="btn btn-warning" onClick="loadEditConfigurationsOption('<?php echo $option->id;?>', 'sub');">Edit</a> <?php echo $option->name;?> Type : <?php echo $types;?> 
						</div>
						<?php /* }else*/ if($have_checkable_submodels) { ?>
							<?php $submodels = ModelOption::find()->select('name')->where(['parent_id'=>$option->id, 'checkable'=>1])->asArray()->all(); ?>
							<?php  
								$_data = array();
								foreach ($submodels as $submodel)
								{
									$_data[] = $submodel['name'];
								}
							?>
							<?php  $types = implode(' ', $_data);?>
							<?php echo $types;?> 
						<?php }?>
						<?php /* foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$option->id])->all() as $optionlv_2) :?>
							<?php if($optionlv_2->checkable) :?>
					            <div class="row row-margin"><?php echo $optionlv_2->name;?></li>
					        <?php else :?>
					        	<?php $sub_models = ModelOption::find()->asArray()->all();?>
					             <h5><?php echo $option->name;?> Type : <?php echo $optionlv_2->name;?></h5>
					            <?php foreach (ModelOption::find()->where(['optiontype'=>2, 'parent_id'=>$optionlv_2->id])->all() as $optionlv_3) :?>
					           		<div class="row row-margin"><?php echo $optionlv_3->name;?></div>
					   			<?php endforeach;?>
					       <?php endif;?>
					   <?php endforeach;*/?>
					 </div>
			<?php endforeach;?>	 
			<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>
			<div id="configurations-options-manager" class="row row-margin"></div> 	
	  	</div>	 
		<div class="modal-footer">
			<button type="button" class="btn btn-warning" onClick="EditConfigurations();" style="display:none;">Edit</button>
			<button type="button" class="btn btn-primary" data-dismiss="modal" >Close</button>
		</div>
	</div>
  </div>
</div>	  
<?php /*$form = ActiveForm::begin(); ?>
<div class="col-md-12 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <div id="myTabContent" class="tab-content">
                <?php if(!$model->isNewRecord) :?>
	                <div class="row row-margin">
	               		<a href="javascript:;" class="btn btn-success" onClick='showConfigurationsBox();'>All Configurations Options</a>
	                	<a href="javascript:;" class="btn btn-warning" onClick="manageConfigurationsBox();" >Manage Configurations Options</a>
	                </div>
                <?php endif;?>
                    <div class="form-group">                
                            <div id="entry1" class="clonedInput">
                                <div class="row-margin">
                                	<h4>Basic Options</h4>
                                	<div class="well well-sm">
                                		<div class="row form-group">
                                			<div class="col-sm-2">
												<span class="btn btn-default btn-file">
													Upload Logo <input type="file" name="fileToUpload" id="fileToUpload">
												</span>                                			
                                			</div>
                                			<div class="col-sm-4">
                                				<input type="text" class="form-control" id="assetPartNumber" placeholder="Asset Part Number#" name="assetpartnumber" value="" >
                                			</div> 
                                			<div class="col-sm-4">
												<div class="input-group" style="margin-bottom: 5px;"> 
													<span class="input-group-btn">
														<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
													</span>
													<select class="manufacturer_select2_single form-control inputs" id="selectManufacturer" name="manufacturer">
														<option value="">Select A Manufacturer</option>
														<?php foreach(Manufacturer::find()->all() as $manufacturer) :?>
															<option <?php if($model->manufacturer===$manufacturer->id) : ?>selected="selected"<?php endif;?> value="<?php echo $manufacturer->id;?>"><?php echo $manufacturer->name;?></option>
														<?php endforeach;?>
													</select>
												</div>                                 			
                                			</div>     
                                			<div class="col-sm-4">
												<div class="input-group" style="margin-bottom: 5px;"> 
													<span class="input-group-btn">
														<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
													</span>
													<select class="category_select2_single form-control inputs" id="selectCategory" name="category">
														<option value="">Select A Manufacturer</option>
														<?php foreach(Category::find()->all() as $category) :?>
															<option <?php if($model->category_id===$category->id) : ?>selected="selected"<?php endif;?> value="<?php echo $category->id;?>"><?php echo $category->categoryname;?></option>
														<?php endforeach;?>
													</select>
												</div>                                 			
                                			</div>        
                                			<div class="col-sm-4">
												<div class="input-group" style="margin-bottom: 5px;"> 
													<span class="input-group-btn">
														<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
													</span>
													<select class="department_select2_single form-control inputs" id="selectDepartment" name="department">
														<option value="">Select A Department</option>
														<?php foreach(Department::find()->all() as $department) :?>
															<option <?php if($model->department===$department->id) : ?>selected="selected"<?php endif;?> value="<?php echo $department->id;?>"><?php echo $department->name;?></option>
														<?php endforeach;?>
													</select>
												</div>                                 			
                                			</div>                                			                         			                          			
                                		</div>
                                	</div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'aei')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-7">
                                        <?= $form->field($model, 'descrip')->textarea(['rows' =>3]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-8">
                                        <div class="input-group" id="upload_logo-group">
                                            <span class="input-group-btn">
                                                <span class="file-input btn btn-success btn-sm btn-file">
                                                    Upload Image&hellip; <input type="file" id="logo" multiple>
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Start Customer to Partnumber Assignment -->
                                <div class="col-md-12 col-sm-6 col-xs-12">
	                              <div class="x_panel">
		                             <div class="x_content">
                                        <div class="" role="tabpanel" data-example-id="togglable-tabs">
        					               <div id="myTabContent" class="tab-content">
        						              <div class="well well-sm">
        							             <div class="row">
        								            <div class="col-md-2">
        									           <button class="btn btn-success btn-xs" id="collapsingbutton" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><span class="glyphicon glyphicon-collapse-down"></span> Show Details</button>
        								            </div>   
        								            <div class="col-md-10">
        									           Specify an individual customer, select part number and assign.
        								            </div>                
        							             </div>
        							             <div class="collapse" id="collapseExample">
                                                        
            								         <div class="row">&nbsp;</div>
            					                           <div class="row">
                						                      <div class="form-group col-md-4">
                                    							<div class="input-group">
                                									<span class="input-group-btn">
                                										<button type="button" class="btn btn-success" data-toggle="modal" data-target="#addCustomer"><span class="glyphicon glyphicon-plus"></span></button>
                                									</span>
                                									<input type="text" id="selectCustomer" class="form-control" placeholder="Choose Customer">
                                								</div>	
            						                          </div>   
                                                              <div class="form-group col-md-4">
                                        							<div class="input-group">
                                    									<span class="input-group-btn">
                                    										<button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPartnumber"><span class="glyphicon glyphicon-plus"></span></button>
                                    									</span>
                                    									<input type="text" id="selectCustomer" class="form-control" placeholder="Choose Partnumber">
                                    								</div>	
                						                      </div>  								
            								            </div>  
                                                    <div class="row">
                            							<div class="col-sm-12">
                            								<div class="actions text-right">
                            									<button class="btn btn-success btn-xs" id="btnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                            									<button class="btn btn-success btn-xs" id="btnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                            								</div>
                            							</div>
                            						</div>  								
            							         </div>
        						              </div>
        						          </div>
        						      </div>                                      
                                    </div>
                                    </div>
                                </div>
                              <!-- End -->
                              
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'palletqtylimit')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'stripcharacters')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'checkit')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'frupartnum')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'manpartnum')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'serialized')->radioList(array('1'=>'Yes', '0'=>'No')); ?>
                                    </div>
                                </div>


                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" type="button"> Cancel</button>
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); */?>
<?php $form = ActiveForm::begin(['options' => ['id'=>'inventory-add-model-form', 'class'=>'form-group', 'enctype' => 'multipart/form-data']]); ?>
	<div class="col-md-12 col-sm-6 col-xs-12">
	    <div class="x_panel">
	        <div class="x_title">
	            <h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
	            <ul class="nav navbar-right panel_toolbox">
	                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
	            </ul>
	            <div class="clearfix"></div>
	        </div>
	        <div class="x_content">
	            <div class="col-lg-12 col-xs-12" role="tabpanel" data-example-id="togglable-tabs">
	                <div id="myTabContent" class="tab-content">    
							<div class="modal-body">
								<div class="container">
									<div id="wizard" class="form_wizard wizard_horizontal">
										<ul class="wizard_steps">
											<li>
												<a href="#step-1">
													<span class="step_no">1</span>
													<span class="step_descr">
											Basic Options<br />
											<small>All basic options</small>
										</span>
												</a>
											</li>
											<li>
												<a href="#step-2">
													<span class="step_no">2</span>
													<span class="step_descr">
											Customer Options<br />
											<small>All Customer Options</small>
										</span>
												</a>
											</li>											
											<li>
												<a href="#step-3">
													<span class="step_no">3</span>
													<span class="step_descr">
											Advanced Options<br />
											<small>All Advanced Options</small>
										</span>
												</a>
											</li>
											<li>
												<a href="#step-4">
													<span class="step_no">4</span>
													<span class="step_descr">
											Additional Options<br />
											<small>All Additional Options</small>
										</span>
												</a>
											</li>
										</ul>
										<div id="step-1">
										<div class="dropzone" id="inventory-dropzone"></div>
											<div class="row row-margin">
												<div class="form-group">								
													<div class="col-md-3">
														<div class="input-group" style="margin-bottom: 5px;"> 
															<span class="input-group-btn">
																<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
															</span>
															<select class="manufacturer_select2_single form-control inputs" id="selectManufacturer" name="manufacturer">
																<option value="">Select A Manufacturer</option>
																<?php foreach(Manufacturer::find()->all() as $manufacturer) :?>
																	<option <?php if($model->manufacturer===$manufacturer->id) : ?>selected="selected"<?php endif;?> value="<?php echo $manufacturer->id;?>"><?php echo $manufacturer->name;?></option>
																<?php endforeach;?>
															</select>
														</div>                                 			
													</div>     
													<div class="col-md-3">
														<div class="input-group" style="margin-bottom: 5px;"> 
															<span class="input-group-btn">
																<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
															</span>
															<select class="category_select2_single form-control inputs" id="selectCategory" name="category">
																<option value="">Select A Category</option>
																<?php foreach(Category::find()->all() as $category) :?>
																	<option <?php if($model->category_id===$category->id) : ?>selected="selected"<?php endif;?> value="<?php echo $category->id;?>"><?php echo $category->categoryname;?></option>
																<?php endforeach;?>
															</select>
														</div>                                 			
													</div>    
													<div class="col-md-3">
														<div class="input-group" style="margin-bottom: 5px;"> 
															<span class="input-group-btn">
																<button type="button" class="btn btn-success btn-md" onClick=""><span class="glyphicon glyphicon-plus"></span></button>
															</span>
															<select class="department_select2_single form-control inputs" id="selectDepartment" name="department">
																<option value="">Select A Department</option>
																<?php foreach(Department::find()->all() as $department) :?>
																	<option <?php if($model->department===$department->id) : ?>selected="selected"<?php endif;?> value="<?php echo $department->id;?>"><?php echo $department->name;?></option>
																<?php endforeach;?>
															</select>
														</div>                                 			
													</div> 
													<div class="col-md-3" id="r_aei-group">
														<input type="text" class="form-control" name="model_aei" placeholder="AEI# (Required)" id="autocomplete-aei" value="<?php echo $model->aei;?>">
													</div>																										    						
												</div>				
											</div>		
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-12" id="r_description-group">
														<label for="description">Description</label>
														<textarea class="form-control " name="descrip" style="min-height:40px;resize:none;" id="model_descrip" placeholder="Description (Required)"><?php echo $model->descrip;?></textarea>
													</div>
												</div>
											</div>
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-3" id="r_defaultpurchaseprice-group">
														<label for="defaultpurchaseprice">Default Purchase Price</label>
														<input type="text" class="form-control" name="purchasepricing" placeholder="Default Purchase Price" value="<?php echo $model->purchasepricing;?>">
													</div>
													<div class="col-md-3" id="r_defaultrepairprice-group">
														<label for="defaultrepairprice">Default Repair Price</label>
														<input type="text" class="form-control" name="repairpricing" placeholder="Default Repair Price" value="<?php echo $model->repairpricing;?>">
													</div>	
													<div class="col-md-3" id="r_purchaseprice2-group">
														<label for="">Purchase Price(Tier 2)</label>
														<input type="text" class="form-control" name="purchasepricing2" placeholder="Purchase Price (Tier 2) (Optional)" value="<?php echo $model->purchasepricingtier2;?>">
													</div>
													<div class="col-md-3" id="r_repairprice2-group">
														<label for="">Repair Price(Tier 2)</label>
														<input type="text" class="form-control" name="repairpricing2" placeholder="Repair Price (Tier 2) (Optional)" value="<?php echo $model->repairpricingtier2;?>">
													</div>											
													<!--<div class="col-md-4" id="r_serialized-group">
														<label for="serialized">Serialized</label>
														<select class="form-control" name="serialized">
															<option value="0">No</option>
															<option value="1">Yes</option>
														</select>						
													</div>-->						
												</div>					
											</div>	
                                            <div class="row row-margin">
                                                 <label for="serialized" class="checkbox-inline" style="padding-left: 11px;"> Is this item serialized?</label>																	
                                                 <input type="checkbox" data-on-text="Yes" data-off-text="No" id="serialized" name="serialized" <?php echo ($model->serialized==1) ? "checked" : '';?>>
                                            </div>															
										</div>
										<div id="step-2">
											<h2 class="StepTitle">Customer Options</h2>
											<?php if(empty($partnumber)) : ?>
												<div id="partEntry1" class="partClonedInput">
													<div class="row row-margin">
														<div class="form-group">
															<div class="col-md-4 customer-group">
																<input class="typeahead form-control input_cust" type="text" name="modelCustomer[]" id="modelCustomer_1" value="" placeholder="Select a customer" data-provide="typeahead" autocomplete="off" type="search" />
																<input class="form-control input_h" type="hidden" name="modelCustomerval[]" id="modelCustomerval_1" />				
															</div>	
															<div class="col-md-2 partnum-type-group">
																<select class="select2_partnumber_type form-control inputs" id="selectParttype" name="parttype">
																	<option value="">Select Type</option>
																	<?php foreach(Partnumbertype::find()->all() as $row) :?>
																		<option value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
																	<?php endforeach;?>
																</select>													
															</div>
															<div class="col-md-2 partid-group">
																<input class="form-control input_partid" id="inputSuccess4" placeholder="Part ID." type="text" name="partid[]" id="partid_1" />			
															</div>	
															<div class="col-md-4 partdesc-group">
																<input class="form-control input_partdesc" id="inputSuccess5" placeholder="Part Description." type="text" name="partdesc[]" id="partdesc_1" />
															</div>	
														</div>
													</div>
													<div class="row row-margin">
														<div class="form-group">
															<div class="col-md-3" id="r_pdefaultpurchaseprice-group">
																<label for="">Default Purchase Price</label>
																<input type="text" class="form-control" name="ppurchasepricing[]" placeholder="Default Purchase Price (Required)">
															</div>
															<div class="col-md-3" id="r_pdefaultrepairprice-group">
																<label for="">Default Repair Price</label>
																<input type="text" class="form-control" name="prepairpricing[]" placeholder="Default Repair Price (Required)">
															</div>	
															<div class="col-md-3" id="r_ppurchaseprice2-group">
																<label for="">Purchase Price(Tier 2)</label>
																<input type="text" class="form-control" name="ppurchasepricing2[]" placeholder="Purchase Price (Tier 2) (Optional)">
															</div>
															<div class="col-md-3" id="r_prepairprice2-group">
																<label for="">Repair Price(Tier 2)</label>
																<input type="text" class="form-control" name="prepairpricing2[]" placeholder="Repair Price (Tier 2) (Optional)">
															</div>	
														</div>
													</div>													
												</div>		
											<?php else :?>
												<?php $i=1;?>
												<?php foreach($partnumber as $key=>$part) :?>
													<div id="partEntry<?= $key+1 ?>" class="partClonedInput">
														<div class="row row-margin">
															<div class="form-group">
																<div class="col-md-4 customer-group">
																	<input class="typeahead form-control input_cust" type="text" name="modelCustomer[]" id="modelCustomer_1" value="<?php echo Customer::findOne($part->customer)->companyname;?>" placeholder="Select a customer" data-provide="typeahead" autocomplete="off" type="search" />
																	<input class="form-control input_h" type="hidden" name="modelCustomerval[]" id="modelCustomerval_1" value="<?php echo $part->customer;?>"/>				
																</div>	
																<div class="col-md-2 partnum-type-group">
																	<!-- <select class="select2_partnumber_type form-control inputs" id="selectParttype" name="parttype"> -->
																	<select class="form-control inputs" id="selectParttype" name="parttype">
																		<option value="">Select Type</option>
																		<?php foreach(Partnumbertype::find()->all() as $row) :?>
																			<option <?php if($part->type==$row->id) : ?>selected="selected"<?php endif;?> value="<?php echo $row->id;?>"><?php echo $row->name;?></option>
																		<?php endforeach;?>
																	</select>													
																</div>
																<div class="col-md-2 partid-group">
																	<input class="form-control input_partid" id="inputSuccess4" placeholder="Part ID." type="text" name="partid[]" id="partid_1" value="<?php echo $part->partid;?>"/>			
																</div>	
																<div class="col-md-4 partdesc-group">
																	<input class="form-control input_partdesc" id="inputSuccess5" placeholder="Part Description." type="text" name="partdesc[]" id="partdesc_1" value="<?php echo $part->partdescription;?>"/>
																</div>	
															</div>
														</div>
														<div class="row row-margin">
															<div class="form-group">
																<div class="col-md-3" id="r_pdefaultpurchaseprice-group">
																	<label for="">Default Purchase Price</label>
																	<input type="text" class="form-control" name="ppurchasepricing[]" placeholder="Default Purchase Price (Required)" value="<?php echo $part->purchasepricing;?>">
																</div>
																<div class="col-md-3" id="r_pdefaultrepairprice-group">
																	<label for="">Default Repair Price</label>
																	<input type="text" class="form-control" name="prepairpricing[]" placeholder="Default Repair Price (Required)" value="<?php echo $part->repairpricing;?>">
																</div>	
																<div class="col-md-3" id="r_ppurchaseprice2-group">
																	<label for="">Purchase Price(Tier 2)</label>
																	<input type="text" class="form-control" name="ppurchasepricing2[]" placeholder="Purchase Price (Tier 2) (Optional)" value="<?php echo $part->purchasepricingtier2;?>">
																</div>
																<div class="col-md-3" id="r_prepairprice2-group">
																	<label for="">Repair Price(Tier 2)</label>
																	<input type="text" class="form-control" name="prepairpricing2[]" placeholder="Repair Price (Tier 2) (Optional)" value="<?php echo $part->repairpricingtier2;?>">
																</div>	
															</div>
														</div>													
													</div>				
												<?php endforeach;?>
											<?php endif;?>
											<div class="row">
												<div class="col-sm-12">
													<div class="actions text-right">
														<button class="btn btn-success btn-xs" id="PartbtnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
														<button class="btn btn-success btn-xs" id="PartbtnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
													</div>
												</div>
											</div>											
										</div>
										<div id="step-3">
											<h2 class="StepTitle">Advanced Options</h2>
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-2" id="r_fru-group">
														<label for="frunumber">FRU Number</label>
														<input type="text" class="form-control" name="frupartnum" placeholder="FRU#" value="<?php echo $model->frupartnum;?>">
													</div>			
													<div class="col-md-2" id="r_man-group">
														<label for="manpartnumber">Man Part Number</label>
														<input type="text" class="form-control" name="manpartnum" placeholder="Man part number" value="<?php echo $model->manpartnum;?>">
													</div>	
													<div class="col-md-4" id="vendor-group">
														<label for="pvendor">Prefered Vendor</label>
														<select class="select2_vendor form-control" id="selectVendor" name="prefered_vendor">
															<option selected="selected" value="">Select A Vendor</option>
															<?php $vendors = Vendor::find()->all();?>
															<?php foreach($vendors as $vendor) :?>	
																<option <?php if($model->prefered_vendor===$vendor->id) : ?>selected="selected"<?php endif;?> value="<?php echo $vendor->id;?>"><?php echo $vendor->vendorname;?></option>											
															<?php endforeach;?>
														</select>
													</div>
													<div class="col-md-4" id="r_pvc-group">
														<label for="pvc">Preferred Vendor Cost</label></br>
														<input type="text" class="form-control" name="pvendorcost" placeholder="0.00" value="">
													</div>														
												</div>	
											</div>	
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-4" id="r_svendor-group">
														<label for="svendor">Secondary Vendor</label>
														<select class="select2_vendor form-control" id="selectVendor" name="secondary_vendor">
															<option selected="selected" value="">Select A Vendor</option>
															<?php $vendors = Vendor::find()->all();?>
															<?php foreach($vendors as $vendor) :?>	
																<option <?php if($model->secondary_vendor===$vendor->id) : ?>selected="selected"<?php endif;?> value="<?php echo $vendor->id;?>"><?php echo $vendor->vendorname;?></option>											
															<?php endforeach;?>
														</select>
													</div>
													<div class="col-md-4" id="r_fru-group">
														<label for="">Secondary Vendor Cost</label>
														<input type="text" class="form-control" name="secondaryvendorcost" placeholder="0.00" value="">
													</div>
													<div class="col-md-4" id="r_fru-group">
														<label for="">Last Cost</label>
														<input type="text" class="form-control" name="lastcost" placeholder="0.00" value="">
													</div>																																
												</div>						
											</div>						
										</div>
										<div id="step-4">
											<h2 class="StepTitle">Additional Options</h2>
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-6" id="r_fru-group">
														<label for="">Default quantity for reordering?</label>
														<input type="number" class="form-control" name="reorderqty" value="<?php echo $model->reorderqty;?>">
													</div>	
													<div class="col-md-6" id="r_fru-group">
														<label for="">How many will fit on a pallet?</label>
														<input type="number" class="form-control" name="palletqtylimit" value="<?php echo $model->palletqtylimit;?>">
													</div>															
												</div>	
											</div>	
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-md-4" id="r_fru-group">
														<label for="">Remove these characters from the manufacturers serial number barcode:</label>
														<input type="text" class="form-control" name="stripcharacters" value="<?php echo $model->stripcharacters;?>">
													</div>																						
													<div class="col-md-4" id="r_fru-group">
														<label for="">Check the manufacturers serial number barcode for these characters:</label>
														<input type="text" class="form-control" name="checkit" value="<?php echo $model->checkit;?>">
													</div>		
													<div class="col-md-4" id="r_fru-group">
														<label for="">How many characters should the serial number be?</label>
														<input type="number" name="charactercount" class="form-control" value="<?php echo $model->charactercount;?>">
													</div>														
												</div>
											</div>
											<div class="row row-margin">
												<div class="form-group">
													<div class="col-sm-4">					 
														<label for="" class="checkbox-inline" style="padding-left:0px;">Does this item qualify for photo conversion for serial number entry?</label>																	
														<input type="checkbox" data-on-text="Yes" data-off-text="No" id="photo_conversion" name="photo_conversion">
													</div>		
													<div class="col-sm-4">					 
														<label for="" class="checkbox-inline" style="padding-left:0px;">Are pre-owned/used items of this model sent through the cleaning department before the service labs?</label>																	
														<input type="checkbox" data-on-text="Yes" data-off-text="No" id="preowneduseditems" name="preowneduseditems" <?php echo ($model->preowneditems==1) ? "checked" : '';?>>
													</div>	
													<div class="col-sm-4">					 
														<label for="requiretesting" class="checkbox-inline" style="padding-left:0px;">Does this model require testing before refurbishing is completed?</label>																	
														<input type="checkbox" data-on-text="Yes" data-off-text="No" id="requiretesting" name="requiretesting" <?php echo ($model->requiretestingreferb==1) ? "checked" : '';?>>
													</div>														
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-success">Save</button>
							</div>
							<input type="hidden" id="entryRow" />
					</div>
				</div>
			</div>
		</div>
	</div>
<?php ActiveForm::end(); ?>
<script>
	$("[name='photo_conversion']").bootstrapSwitch("size", "small");
	$("[name='preowneduseditems']").bootstrapSwitch("size", "small");
	$("[name='requiretesting']").bootstrapSwitch("size", "small");
	$("[name='serialized']").bootstrapSwitch("size", "small");
</script>
<?php //LOAD CUSTOMER ADD FORM --->?>
<?= $this->render("_modals/_addcustomer");?>
<?php //LOAD LOCATION ADD FORM --->?>
<?= $this->render("_modals/_addpartnumber");?>
<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/inventory_form.js"></script>