<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Vendor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendor-form">

    <?php $form = ActiveForm::begin(); ?>

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
						<div class="row">
							<div class="x_panel">
								<div class="x_title">
									<h2><i class="fa fa-level-down"></i><small> Location</small></h2>
									<ul class="nav navbar-right panel_toolbox">
										<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
									</ul>
									<div class="clearfix"></div>
								</div>
								<div class="x_content" style="margin:0;">
									<div class="" role="tabpanel" data-example-id="togglable-tabs">
										<div id="myTabContent" class="tab-content">	
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'address_line_1')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													<?= $form->field($model, 'address_line_2')->textInput(['maxlength' => true]) ?>
												</div>
											</div>													
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													 <?= $form->field($model, 'zip')->textInput(['maxlength' => true]) ?>
												</div>
											</div>												
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>	
												</div>
												<div class="col-md-6">
												</div>
											</div>											    										
										</div>
									</div>
								</div>
							</div>							
							<div class="x_panel">
								<div class="x_title">
									<h2><i class="fa fa-level-down"></i><small> Contact</small></h2>
									<ul class="nav navbar-right panel_toolbox">
										<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
									</ul>
									<div class="clearfix"></div>
								</div>
								<div class="x_content" style="margin:0;">
									<div class="" role="tabpanel" data-example-id="togglable-tabs">
										<div id="myTabContent" class="tab-content">				
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													 <?= $form->field($model, 'telephone_1')->textInput(['maxlength' => true]) ?>
												</div>
											</div>										
											<div class="row">		
												<div class="col-md-6">
													 <?= $form->field($model, 'telephone_2')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													 <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>
												</div>
											</div>											    
											<div class="row">		
												<div class="col-md-6">
													 <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													 <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
												</div>
											</div>																				    								
										</div>
									</div>
								</div>
							</div>		
							<div class="x_panel">
								<div class="x_title">
									<h2><i class="fa fa-level-down"></i><small> Details</small></h2>
									<ul class="nav navbar-right panel_toolbox">
										<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
									</ul>
									<div class="clearfix"></div>
								</div>
								<div class="x_content" style="margin:0;">
									<div class="" role="tabpanel" data-example-id="togglable-tabs">
										<div id="myTabContent" class="tab-content">	
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'vendorid')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													<?= $form->field($model, 'vendorname')->textInput(['maxlength' => true]) ?>
												</div>
											</div>	
											<div class="row">		
												<div class="col-md-6">
													<label class="control-label" for="vendor-1099_type">1099 Type</label>
													<?= Html::activeDropDownList($model, '1099_type', [0,1], ['class'=>'form-control']) ?>
												</div>
												<div class="col-md-6">
													<?= $form->field($model, 'taxidno')->textInput() ?>
												</div>
											</div>											
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'terms')->textInput(['maxlength' => true]) ?>
												</div>
												<div class="col-md-6">
													<label class="control-label" for="vendor-active">Active</label>
													<?= Html::activeDropDownList($model, 'active', [0=>'No', 1=>'Yes'], ['class'=>'form-control']) ?>
												</div>
											</div>											    
											<div class="row">		
												<div class="col-md-6">
													<label class="control-label" for="vendor-usebillpay">Use Bill Pay</label>
													<?= Html::activeDropDownList($model, 'usebillpay', [0,1], ['class'=>'form-control']) ?>
												</div>
												<div class="col-md-6">
													<?= $form->field($model, 'accountno')->textInput(['maxlength' => true]) ?>
												</div>
											</div>											
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'expense_account_id')->textInput() ?>
												</div>
												<div class="col-md-6">
													<?= $form->field($model, 'last_inv_amt')->textInput(['maxlength' => true]) ?>
												</div>
											</div>											    
											<div class="row">		
												<div class="col-md-6">
													<?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
												</div>
												<div class="col-md-6">
												</div>
											</div>																				
										</div>
									</div>
								</div>
							</div>												    		
						</div>
						<div class="row-margin"></div>
						<div class="row">
							<div class="col-md-12 text-right">
					    		<?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancel', 'javascript:;', ['class'=>'btn btn-danger']) ?>					    	
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
</div>
