<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Location;
use app\models\LocationClassment;
use app\models\LocationParent;

	$this->title = 'Manage Locations';
	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['ownstockpage', 'id'=> $customer->id]];;
	$this->params['breadcrumbs'][] = $this->title;	
?>

<div class="row row-margin">			
	<?php $form = ActiveForm::begin(['action' => ['msavelocations', 'id'=>$customer->id], 'options' => ['action'=>['id'=>'add-replocation-form']]]); ?>
		<div class="col-lg-12 col-xs-12">
			<div class="x_panel" style="padding: 10px 10px;">
				<div class="x_title">
					<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content" style="padding:0;margin-top:0;">
					<div class="" role="tabpanel" data-example-id="togglable-tabs">
						<div id="myTabContent" class="tab-content">	
							<div class="row row-margin">
								<div class="col-md-6">
									<input type="text" class="form-control" id="parentName" placeholder="Unamed folder" name="parentname" />
								</div>
								<div class="col-md-6">
								<?php 
								//var_dump(Location::find()->where(['customer_id'=>$customer->id])->andWhere(['not', ['id'=>$location_ids]]));
								//var_dump($uncategorized_locations); 
								?>
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
													<option <?php /*if($model->location_id===$location->id) : ?>selected="selected"<?php endif;*/?> value="<?php echo $location['id'];?>"><?php echo $output;?></option>
												<?php endforeach;?>
											</optgroup>
										<?php endif;?>
										<?php foreach(LocationClassment::find()->select('parent_id')->where(['customer_id'=>$customer->id])->andWhere(['not', ['parent_id'=>null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all() as $l_classment) :?>
											<optgroup label="<?php echo LocationParent::findOne($l_classment->parent_id)->parent_name;?>">
												<?php foreach(Location::find()->innerJoin('lv_locations_classments', '`lv_locations_classments`.`location_id` = `lv_locations`.`id`')->where(['customer_id'=>$customer->id, 'parent_id'=>$l_classment->parent_id])->groupBy('location_id')->all() as $location) :?>
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
									<?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> Save',[ 'name'=>'saveOrder', 'class' => 'btn btn-success']) ?>
								</div>
							</div>								
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php ActiveForm::end(); ?>
</div>