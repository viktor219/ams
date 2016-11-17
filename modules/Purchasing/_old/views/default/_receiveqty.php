<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="row row-margin">			
	<?php $form = ActiveForm::begin(['options' => ['action'=>['/orders/create'], 'id'=>'add-receive-qty-form']]); ?>
		<input type="text" name="qty" class="form-control"/>
	<?php ActiveForm::end(); ?>
</div>