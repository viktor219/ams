<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Purchase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'vendor_id')->textInput() ?>

    <?= $form->field($model, 'shipping_company')->textInput() ?>

    <?= $form->field($model, 'location_id')->textInput() ?>

    <?= $form->field($model, 'number_generated')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_radius')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number_bb')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'ordertype')->textInput() ?>

    <?= $form->field($model, 'orderfile')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'returned')->textInput() ?>

    <?= $form->field($model, 'returneddate')->textInput() ?>

    <?= $form->field($model, 'estimated_time')->textInput() ?>

    <?= $form->field($model, 'trackingnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trackinglink')->textInput() ?>

    <?= $form->field($model, 'dateshipped')->textInput() ?>

    <?= $form->field($model, 'shipby')->textInput() ?>

    <?= $form->field($model, 'requestedby')->textInput() ?>

    <?= $form->field($model, 'trucknum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sealnum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dateonsite')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'modified_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
