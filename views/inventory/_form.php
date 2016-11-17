<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Manufacturer;
use app\models\Department;
use app\models\Category;
use app\assets\AppAsset;


/* @var $this yii\web\View */
/* @var $model app\models\Inventory */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

/*
<div class="inventory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'modelname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'aeino')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'imagepath')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'manufacturer')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'palletqtylimit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stripcharacters')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'checkserial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'frupartnum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'manufacturerpartnum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'istrackserial')->textInput() ?>

    <?= $form->field($model, 'isstorespecific')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'quote')->textInput() ?>

    <?= $form->field($model, 'datecreated')->textInput() ?>

    <?= $form->field($model, 'datemodified')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
*/
?>

<?php $form = ActiveForm::begin(); ?>
<div class="col-md-12 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-bars"></i> </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                <div id="myTabContent" class="tab-content">
                    <!-- Customer -->
                    <!--<div class="col-md-4">
                        <div class="well well-sm" style="background:#FFF;">
                            <label for="selectCustomer"><i class="fa fa-level-down"></i> Step #1 : Customer</label>
                            <div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-success" data-toggle="modal" data-target="#addCustomer"><span class="glyphicon glyphicon-plus"></span></button>
									</span>
                                <input type="text" id="selectCustomer" class="form-control" placeholder="Choose Customer">
                            </div>
                        </div>
                    </div>-->
                    <!-- end Customer -->
                    <!-- Purchase -->
                    <!--<div class="col-md-4">
                        <div class="well well-sm" style="background:#FFF;">
                            <label for="selectCustomer"><i class="fa fa-level-down"></i> Step #2 : Purchase</label>
                            <div class="form-group">
                                <select class="select2_single form-control" tabindex="-1" style="line-height: 32px;z-index:100">
                                    <option>Purchase (New In Box)</option>
                                    <option>Purchase (Refurbished)</option>
                                    <option>Purchase (As Is)</option>
                                    <option>Customer Repair</option>
                                    <option>Customer Integration</option>
                                </select>
                            </div>
                        </div>
                    </div>-->
                    <!-- End Purchase -->
                    <!-- Shipment -->
                    <!--<div class="col-md-4">
                        <div class="well well-sm" style="background:#FFF;">
                            <label for="selectCustomer"><i class="fa fa-level-down"></i> Step #3 : Shipment</label>
                            <div class="input-group">
                                <input type="text" id="selectShipment" class="form-control" placeholder="Select A Shipment Type"/>
                            </div>
                        </div>
                    </div>-->
                    <!-- End Shipment -->

                    <div class="form-group">
                        <div class="well well-sm">
                            <div id="entry1" class="clonedInput">
                                <div class="row form-group">
                                    <div class="col-sm-8">
                                        <?= $form->field($model, 'modelname')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'aeino')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-7">
                                        <?= $form->field($model, 'description')->textarea(['rows' =>3]) ?>
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
                                <div class="row-margin"></div>
                                <div class="row form-group">                                    
                                        <div class="col-md-6">                               
                                        <?= $form->field($model, 'manufacturer')->dropDownList(
                                            ArrayHelper::map(Manufacturer::find()->all(), 'id', 'name'),
                                            ['prompt' => ' -- Select Manufacturer -- ']
                                        ) ?>
                                        
                                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span>Add Manufacturer', Yii::$app->request->baseUrl . '/manufacturers/create', array('target'=>'_blank', 'class' => 'btn btn-success')); ?>
                                        </div>   
                                       
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'department')->dropDownList(
                                            ArrayHelper::map(Department::find()->all(), 'id', 'name'),
                                            ['prompt' => ' -- Select Department -- ']
                                        ) ?>
                                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span>Add Department', Yii::$app->request->baseUrl . '/departments/create', array('target'=>'_blank', 'class' => 'btn btn-success')); ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'category')->dropDownList(
                                            ArrayHelper::map(Category::find()->all(), 'id', 'categoryname'),
                                            ['prompt' => ' -- Select Category -- ']
                                        ) ?>
                                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span>Add Category', Yii::$app->request->baseUrl . '/categories/create', array('target'=>'_blank', 'class' => 'btn btn-success')); ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                
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
                                        <?= $form->field($model, 'checkserial')->textInput(['maxlength' => true]) ?>
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
                                        <?= $form->field($model, 'manufacturerpartnum')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </div>
                                <div class="row-margin"></div>
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'istrackserial')->radioList(array('1'=>'Yes', '0'=>'No')); ?>
                                    </div>
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
    <?php ActiveForm::end(); ?>

<?php //LOAD CUSTOMER ADD FORM --->?>
<?= $this->render("_modals/_addcustomer");?>
<?php //LOAD LOCATION ADD FORM --->?>
<?= $this->render("_modals/_addpartnumber");?>