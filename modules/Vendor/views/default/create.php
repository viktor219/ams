<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Vendor */

$this->title = 'Create Vendor';
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>