<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'AMS - Reset Password';

?>
	<section class="login" style="background:#FFF;"> 
		<div class="titulo" style="background:#FFF;color:#333;">Customer Reset Password</div>	
		    <?php $form = ActiveForm::begin([
		        //'id' => 'login-form',  
		        'options' => ['class' => 'form-horizontal'],
		        'fieldConfig' => [ 
		            'template' => "{label}\n{input}\n<div class=\"col-lg-12\">{error}</div>", 
		            'labelOptions' => ['class' => 'col-lg-1 control-label'],   
		        ],   
		    ]); ?> 
		     
			<div class="logo">
				<img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/layout/assetlogo-trans.png" alt="Asset Enterprises, Inc." style="width: 250px;"/> 
			</div>   
			
			<div class="row">	 
				<label><b>Password</b></label>
	        	<?= $form->field($model, 'password')->passwordInput(['placeholder'=>'**************', 'value'=>''])->label(false) ?> 
	        </div> 
			
			<div class="row">	 
				<label><b>Password Confirmation</b></label>
	        	<?= $form->field($model, 'password_confirmation')->passwordInput(['placeholder'=>'**************', 'value'=>''])->label(false) ?> 
	        </div> 
	
	        <div class="form-group">
	            <?= Html::submitButton('Change password', ['class' => 'enviar', 'name' => 'login-button']) ?>
	        </div>
			<div class="row-margin-bottom"></div>
	    <?php ActiveForm::end(); ?>
	</section>