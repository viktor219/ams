<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'AMS - Reset Password';

?>
	<section class="login" style="background:#FFF;"> 
		<div class="titulo" style="background:#FFF;color:#333;border: none; width: 100%">Please Reset Your Password</div>	
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
	        	<?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder'=>"Enter new password.", "class" => "bootstrap-frm"])->label(false) ?>
	 
	        	<?= $form->field($model, 'password_confirmation')->passwordInput(['placeholder'=>'Re-type password.',"class" => "bootstrap-frm"])->label(false) ?> 
	        </div> 
                        <?php
                        $errors = $model->getErrors();
                        ?>
		    
<!--			<div class="olvido">
				<div class="col"><a href="#" title="Registration" class="btn-in">Register</a></div>
				<div class="col"><a href="#" title="Password Recovery" class="btn-in">Forgot Password?</a></div>
			</div>-->
	
                <div class="form-group text-center">
	            <?= Html::submitButton('Update Password', ['class' => 'btn btn-default btn-primary', 'name' => 'login-button']) ?>
                    <?php if(isset($errors['password'])): ?>
                    <div class="forgot-pwd">
                                <a href="<?php echo Yii::$app->request->baseUrl; ?>/site/resetpwd" class="">Reset Password?</a>
                            </div>
                    <?php endif;?>
	        </div>
			<div class="row-margin-bottom"></div>
	    <?php ActiveForm::end(); ?>
	</section>