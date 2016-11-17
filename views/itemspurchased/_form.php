<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Itemspurchased */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="itemspurchased-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ordernumber')->textInput() ?>

    <?= $form->field($model, 'model')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
