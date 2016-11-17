<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PurchaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'shipping_company') ?>

    <?= $form->field($model, 'location_id') ?>

    <?php // echo $form->field($model, 'number_generated') ?>

    <?php // echo $form->field($model, 'number_radius') ?>

    <?php // echo $form->field($model, 'number_bb') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'ordertype') ?>

    <?php // echo $form->field($model, 'orderfile') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'returned') ?>

    <?php // echo $form->field($model, 'returneddate') ?>

    <?php // echo $form->field($model, 'estimated_time') ?>

    <?php // echo $form->field($model, 'trackingnumber') ?>

    <?php // echo $form->field($model, 'trackinglink') ?>

    <?php // echo $form->field($model, 'dateshipped') ?>

    <?php // echo $form->field($model, 'shipby') ?>

    <?php // echo $form->field($model, 'requestedby') ?>

    <?php // echo $form->field($model, 'trucknum') ?>

    <?php // echo $form->field($model, 'sealnum') ?>

    <?php // echo $form->field($model, 'dateonsite') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'modified_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
