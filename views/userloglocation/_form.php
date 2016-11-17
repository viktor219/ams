<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserLogLocationTracking */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-log-location-tracking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'continent_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contry_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country_code_3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'area_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dma_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currency_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
