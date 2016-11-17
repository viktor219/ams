<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\Session;
$this->title = 'Login - Asset Management System';
$session = new Session;
$session->open();
?>

	<section class="login" style="background:#FFF;"> 
		<!--<div class="titulo" style="background:#FFF;color:#333;">Customer Login</div>-->	
		    <?php $form = ActiveForm::begin([
		        //'id' => 'login-form',  
		        'options' => ['class' => 'form-horizontal', 'autocomplete' => "off"],
		        'fieldConfig' => [ 
		            'template' => "{label}\n{input}\n<div class=\"col-lg-12\">{error}</div>", 
		            'labelOptions' => ['class' => 'col-lg-1 control-label'],   
		        ],   
		    ]); ?> 
		     
			<div class="logo text-center">
				<img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/layout/assetlogo-trans.png" alt="Asset Enterprises, Inc." style="max-width: 100%"/> 
			</div>   
			<p>
				<?php if(Yii::$app->session->getFlash('error')) : ?>
					<?php 
						echo '<div class="alert alert-danger">' . Yii::$app->session->getFlash('error') . '</div>';
					?>
				<?php endif;?>			
			</p>
                        <?php if(empty($token)): ?>
                            <div class="row text-center">
                                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder'=>"Enter your username or email.", "class" => "bootstrap-frm"])->label(false) ?>
                            </div>
                        <?php else: ?>
                        <div class="row text-center">
                            <?= Html::passwordInput('new-pwd','',['autocomplete' => 'off', 'placeholder'=>"Enter new password.", "class" => "bootstrap-frm"]); ?>
                            <?= Html::passwordInput('reset-pwd','',['autocomplete' => 'off', 'placeholder'=>"Retype password.", "class" => "bootstrap-frm", "style" => "margin-top: 10px; margin-bottom: 10px"]); ?>
                            <?= Html::hiddenInput('isreset', ''); ?>
                        </div>
                        <?php endif; ?>
                        <?php
                        //$errors = $model->getErrors();
                        ?>
		    
<!--			<div class="olvido">
				<div class="col"><a href="#" title="Registration" class="btn-in">Register</a></div>
				<div class="col"><a href="#" title="Password Recovery" class="btn-in">Forgot Password?</a></div>
			</div>-->
	
                <div class="form-group text-center">
	            <?= Html::submitButton('<span class="glyphicon glyphicon-refresh"></span> Reset', ['class' => 'btn btn-default btn-warning', 'name' => 'login-button']) ?>
	        </div>
			<div class="row-margin-bottom"></div>
	    <?php ActiveForm::end(); ?>
	</section>