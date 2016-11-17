<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendors';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['/purchasing/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-index">

                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-7 vcenter">
                                    <h4>
                                        <span class="glyphicon glyphicon-user"></span>
                                        <?= Html::encode($this->title) ?>
                                    </h4>
                                </div>
								<div class="col-md-5 vcenter text-right"> 
									    <?php $form = ActiveForm::begin([
										        'action' => ['index'],
										        'method' => 'get',
										    ]); ?>
										<div id="searchvendor-group" class="pull-right top_search">
											<div class="input-group <?php echo (Yii::$app->user->identity->usertype==User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER)?"search-representative":"";?>">
												<span class="input-group-btn">
													<button class="btn btn-success" id="searchVendorBtn" type="button"><b style="color:#FFF;">?</b></button> 
												</span>
												<input type="text" placeholder="Search" id="searchVendor" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
				                                <?php if(Yii::$app->user->identity->usertype==User::TYPE_ADMIN ||
												        Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN || 
														Yii::$app->user->identity->usertype===User::TYPE_SALES || 
														Yii::$app->user->identity->usertype===User::TYPE_SHIPPING ||
														Yii::$app->user->identity->usertype===User::TYPE_BILLING): ?>
												<span class="input-group-btn">
													<?= Html::a('<span class="glyphicon glyphicon-plus"></span> New Vendor', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>								
												</span>
				                                <?php endif; ?>
											</div>						
										</div>
									<?php ActiveForm::end(); ?>
								</div>                                 
                            </div>
                        </div>
                    <div class="panel-body">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="vendor-home-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="" style="display:none;" id="vendor-search-tab"><a href="#vendorsearch" id="vendor-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="vendor-results-count"><b>0</b></span>) </a>
                                            </li>                                         
                                            <li role="presentation" class="active"><a href="#vendorhome" id="vendor-tab-1" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>                                                                                                                                                    
                                        </ul>
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="vendorhome" aria-labelledby="home-tab">                 
												<div id="vendor-loaded-content">                    
													<?= $this->render('_vendor', ['dataProvider' => $dataProvider])?>
												</div>
											</div>
                                        	<div role="tabpanel" class="tab-pane fade in" id="vendorsearch" aria-labelledby="home-tab">                 
												<div id="vendor-loaded-content-search"></div>
											</div>											
										</div>
									</div>
                    </div>
                </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/vendor.js"></script>