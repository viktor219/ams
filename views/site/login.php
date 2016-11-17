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
		        'options' => ['class' => 'form-horizontal'],
		        'fieldConfig' => [ 
		            'template' => "{label}\n{input}\n<div class=\"col-lg-12\">{error}</div>", 
		            'labelOptions' => ['class' => 'col-lg-1 control-label'],   
		        ],   
		    ]); ?> 
		     
			<div class="logo text-center">
				<img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/layout/assetlogo-trans.png" alt="Asset Enterprises, Inc." style="max-width: 100%"/> 
			</div>   
			<p>
				<?php if(isset($session['error'])) : ?>
					<?php 
						echo '<div class="alert alert-danger">' . $session['error'] . '</div>';
						unset($session['error']);
					?>
				<?php endif;?>
                            <?php if(Yii::$app->session->getFlash('success')) : ?>
					<?php 
						echo '<div class="alert alert-success">' . Yii::$app->session->getFlash('success') . '</div>';
					?>
				<?php endif;?>
			</p>
			<div class="row text-center">
	        	<?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder'=>"Enter your username or email.", "class" => "bootstrap-frm"])->label(false) ?>
	 
	        	<?= $form->field($model, 'password')->passwordInput(['placeholder'=>'Enter your password.',"class" => "bootstrap-frm"])->label(false) ?> 
	        </div> 
			<div class="checkbox" style="margin-left: 10%;">
		        <?= $form->field($model, 'termsAgreement')->checkbox([
		            'template' => "<div>I agree to these <a href=\"tos.php\" target=\"_blank\">Terms of Service</a> {input} </div>\n<div class=\"col-lg-12\">{error}</div>",
		        ]) ?>
		    </div>
                        <?php
                        $errors = $model->getErrors();
                        ?>
		    
<!--			<div class="olvido">
				<div class="col"><a href="#" title="Registration" class="btn-in">Register</a></div>
				<div class="col"><a href="#" title="Password Recovery" class="btn-in">Forgot Password?</a></div>
			</div>-->
	
                <div class="form-group text-center">
	            <?= Html::submitButton('Login', ['class' => 'btn btn-default btn-primary', 'name' => 'login-button']) ?>
                    <?php if(isset($errors['password'])): ?>
                    <div class="forgot-pwd">
                                <a href="<?php echo Yii::$app->request->baseUrl; ?>/site/resetpwd" class="">Reset Password?</a>
                            </div>
                    <?php endif;?>
	        </div>
			<div class="row-margin-bottom"></div>
	    <?php ActiveForm::end(); ?>
	</section>