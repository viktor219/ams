<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'modelname') ?>

    <?= $form->field($model, 'aeino') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'imagepath') ?>

    <?php // echo $form->field($model, 'manufacturer') ?>

    <?php // echo $form->field($model, 'department') ?>

    <?php // echo $form->field($model, 'category') ?>

    <?php // echo $form->field($model, 'palletqtylimit') ?>

    <?php // echo $form->field($model, 'stripcharacters') ?>

    <?php // echo $form->field($model, 'checkserial') ?>

    <?php // echo $form->field($model, 'frupartnum') ?>

    <?php // echo $form->field($model, 'manufacturerpartnum') ?>

    <?php // echo $form->field($model, 'istrackserial') ?>

    <?php // echo $form->field($model, 'isstorespecific') ?>

    <?php // echo $form->field($model, 'quote') ?>

    <?php // echo $form->field($model, 'datecreated') ?>

    <?php // echo $form->field($model, 'datemodified') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
