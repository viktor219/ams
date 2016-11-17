<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Location;
use app\models\LocationClassment;
use app\models\LocationParent;

/* @var $this yii\web\View */
/* @var $model app\models\Location */
/* @var $form yii\widgets\ActiveForm */

?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet" />
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<div class="location-form">
	<div class="x_panel" style="padding: 10px 10px;">
		<div class="x_title">
			<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
			<ul class="nav navbar-right panel_toolbox">
			</ul>
			<div class="clearfix"></div>
		</div>
		<div class="x_content" style="padding:0;margin-top:0;">
			<div class="" role="tabpanel" data-example-id="togglable-tabs">
				<div id="myTabContent" class="tab-content">	
				<?php /*
					<div class="row">				
				    	<?= $form->field($model, 'storename')->textInput(['maxlength' => true]) ?>
					</div>
					
				    <?= $form->field($model, 'storenum')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'zipcode')->textInput(['maxlength' => true]) ?>
				
				    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
			*/ ?>
			<div class="" role="tabpanel" data-example-id="togglable-tabs">
				<div id="myTabContent" class="tab-content">	
					<div class="x_panel">
						<div class="x_title">
							<h2><i class="fa fa-level-down"></i><small> Step #1 : Folder Managment</small></h2>
							<ul class="nav navbar-right panel_toolbox">
								<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
							</ul>
							<div class="clearfix"></div>
						</div>
						<div class="x_content" style="margin:0;">
							<div class="" role="tabpanel" data-example-id="togglable-tabs">
								<div id="myTabContent" class="tab-content">		
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="location-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
                                            <li role="presentation" class="active"><a href="#locationexistingfolder" id="inventory-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Add / Use Existing Folder</a>
                                            </li>												
                                        </ul>
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="locationexistingfolder" aria-labelledby="home-tab">
                                        		<?php $form = ActiveForm::begin(['action'=>['/location/createfolder'], 'options' => ['id'=>'add-location-folder-form']]); ?>
	                                        		<div class="row row-margin"> 	
		                                        		<div class="col-md-4 form-group">
			                                        		<label>Parent Folder :</label>
				  											<select name="e_parent_folder" id="selectCustomerLocations" class="default_select2_single form-control">
				  												<option value=""></option>
																<?php foreach($parent_locations as $l_parent) :?>
																	<optgroup label="=><?php echo $l_parent->parent_name;?>">
																		<?php $childs = LocationParent::find()->where(['parent_parent_id'=>$l_parent->id])->orderBy('parent_name')->all();?>
																		<?php if(count($childs)==0) :?>
																			<option value="<?php echo $l_parent->id;?>"><?php echo $l_parent->parent_name;?></option>
																		<?php endif;?>
																		<?php foreach($childs as $child) :?>
																			<option value="<?php echo $child->id;?>"><?php echo $child->parent_name;?></option>
																		<?php endforeach;?>
																	</optgroup>
																<?php endforeach;?>
															</select>  
														</div> 
														<div class="col-md-4 form-group">
															<label>Division Code :</label>
															<input type="text" class="form-control" name="e_folder_code"/>
														</div> 														
														<div class="col-md-4 form-group">
															<label>Division Name :</label>
															<input type="text" class="form-control" name="e_folder_name"/>
														</div>  
													</div>  
												    <div class="row row-margin">
														<div class="col-md-12 text-right">
															<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-primary']) ?>
															<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create', ['class' => 'btn btn-success']) ?>
														</div>
												    </div>			
												 <?php ActiveForm::end(); ?>		
											</div>												
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>		
			<?php $form = ActiveForm::begin(['options' => ['id'=>'add-location-form']]); ?>
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
						<div class="x_panel">
							<div class="x_title">
								<h2><i class="fa fa-level-down"></i><small> Step #2 : Location Managment</small></h2>
								<ul class="nav navbar-right panel_toolbox">
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
								</ul>
								<div class="clearfix"></div>
							</div>
							<div class="x_content" style="margin:0;">
								<div class="" role="tabpanel" data-example-id="togglable-tabs">
									<div id="myTabContent" class="tab-content">											
										<div class="row row-margin">
											<div class="col-sm-4">
												<select name="parent_location" class="selectCustomerLocations" class="form-control">
													<option value="">Choose a parent location (Optional)</option>
												</select>	
											</div>										
											<div class="col-sm-4">
												<label class="sr-only" for="storenum"></label>
												<input type="text" class="form-control" id="storenum" name="storenum" placeholder="Store or Tech Number (Optional)" value="<?php echo $model->storenum;?>">
											</div>
											<div class="col-sm-4">
												<label class="sr-only" for="storename"></label>
												<input type="text" class="form-control" id="storename" name="storename" placeholder="Store or Tech Name (Optional)" value="<?php echo $model->storename;?>">
											</div>
										</div>
										<div class="row row-margin">
											<div class="col-sm-4" id="location_address-group">
												<label class="sr-only" for="address"></label>
												<input type="text" class="form-control" id="location_address" name="address" placeholder="Address (Required)" value="<?php echo $model->address;?>">
												<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
											</div>
											<div class="col-sm-4" id="location_secondaddress-group">
												<label class="sr-only" for="address"></label>
												<input type="text" class="form-control" id="location_secondaddress" name="address2" placeholder="Second Address (Opt)" value="<?php echo $model->address2;?>">
												<span class="fa fa-home form-control-feedback right" aria-hidden="true"></span>
											</div>	
											<div class="col-sm-4" id="location_zip-group">
												<label class="sr-only" for="zip"></label>
												<input type="text" class="form-control location_zip" id="location_zip" name="zipcode" placeholder="Zip (Required)" value="<?php echo $model->zipcode;?>">
											</div>																				
										</div>
										<div class="row row-margin">					
											<div class="col-sm-4" id="location_country-group">
												<label class="sr-only" for="country"></label>
												<input type="text" class="form-control location_country" id="location_country" name="country" placeholder="Country (Required)" value="<?php echo $model->country;?>">
												<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
											</div>					
											<div class="col-sm-4" id="location_city-group">
												<label class="sr-only" for="city"></label>
												<input type="text" class="form-control location_city" id="location_city" name="city" placeholder="City (Required)" value="<?php echo $model->city;?>">
												<span class="fa fa-globe form-control-feedback right" aria-hidden="true"></span>
											</div>
											<div class="col-sm-4" id="location_state-group">
												<label class="sr-only" for="state"></label>
												<select name="state" class="form-control location_state state_location_select2_single" id="location_state">
													<option value="">Choose a State (Required)</option>
													<?php foreach (\app\models\State::find()->all() as $sate) :?>
														<option value="<?=$sate->code?>" <?php if($model->state==$sate->code) :?>selected<?php endif;?>><?=$sate->code?> - <?=$sate->state?></option>
													<?php endforeach;?>
												</select>	
											</div>					
										</div>
										<div class="row row-margin">
											<div class="col-sm-6" id="location_email-group">
												<label class="sr-only" for="email"></label>
												<input type="email" class="form-control" id="location_email" name="email" placeholder="Email (Optional)" value="<?php echo $model->email;?>">
											</div>
											<div class="col-sm-6">
												<label class="sr-only" for="phone"></label>
												<input type="tel" class="form-control" id="location_phone" name="phone" placeholder="Phone (Optional)" value="<?php echo $model->phone;?>">
											</div>
										</div>							
									    <div class="row row-margin">
											<div class="col-md-12 text-right">
												<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-primary']) ?>
												<?= Html::submitButton($model->isNewRecord ? '<span class="glyphicon glyphicon-save"></span> Create' : '<span class="glyphicon glyphicon-edit"></span> Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
											</div>
									    </div>
				    				</div>
				  				</div>
				  			</div>
				  		</div>
				  	</div>
				  </div>
				 <?php ActiveForm::end(); ?>
				<?php $form = ActiveForm::begin(['action'=>['/location/manage'], 'options' => ['id'=>'add-location-form']]); ?>
					<div class="" role="tabpanel" data-example-id="togglable-tabs">
						<div id="myTabContent" class="tab-content">	
							<div class="x_panel">
								<div class="x_title">
									<h2><i class="fa fa-level-down"></i><small> Step #3 : Folder - Location Managment</small></h2>
									<ul class="nav navbar-right panel_toolbox">
										<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
									</ul>
									<div class="clearfix"></div>
								</div>
								<div class="x_content" style="margin:0;">
									<div class="" role="tabpanel" data-example-id="togglable-tabs">
										<div id="myTabContent" class="tab-content">	
											<div class="row row-margin">
												<div class="col-md-6">
													<select name="parent_location" class="selectCustomerLocations" class="form-control">
														<option value="">Choose a parent location (Optional)</option>
													</select>								
												</div>
												<div class="col-md-6">
													<select name="locations[]" id="selectLocations" multiple="multiple">
														<?php if(count($uncategorized_locations) > 0) :?>
															<optgroup label="Uncategorized">
																<?php foreach($uncategorized_locations as $location) :?>
																	<?php 
																		$output = "";
																		if(!empty($location['storenum']))
																			$output .= "Store#: " . $location['storenum'] . " - ";
																		if(!empty($location['storename']))
																			$output .= $location['storename']  . ' - '; 
																		//
																		$output .= $location['address'] . " " . $location['address2'] . " " . $location['city'] . " " . $location['state'] . " " . $location['zipcode'];									
																	?>
																	<option value="<?php echo $location['id'];?>"><?php echo $output;?></option>
																<?php endforeach;?>
															</optgroup>
														<?php endif;?>
														<?php foreach(LocationClassment::find()->select('parent_id')->where(['customer_id'=>$customers])->andWhere(['not', ['parent_id'=>null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all() as $l_classment) :?>
															<optgroup label="<?php echo LocationParent::findOne($l_classment->parent_id)->parent_name;?>">
																<?php foreach(Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>$customers, 'parent_id'=>$l_classment->parent_id])->groupBy('location_id')->all() as $location) :?>
																	<?php 
																		$output = "";
																		if(!empty($location->storenum))
																			$output .= "Store#: " . $location->storenum . " - ";
																		if(!empty($location->storename))
																			$output .= $location->storename  . ' - '; 
																		//
																		$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
																	?>
																	<option value="<?php echo $location->id;?>"><?php echo $output;?></option>
																<?php endforeach;?>
															</optgroup>
														<?php endforeach;?>
													</select>	
												</div>												
											</div>
											<div class="row row-margin">
												<div class="col-md-12 text-right">
													<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-primary', 'onClick'=>'redirectmLocations();']) ?>
													<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Create',[ 'name'=>'saveOrder', 'class' => 'btn btn-success']) ?>
												</div>
											</div>											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/admin_location_create.js"></script>