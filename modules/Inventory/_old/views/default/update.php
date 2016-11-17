<?php

use yii\helpers\Html;
use app\models\Manufacturer;

/* @var $this yii\web\View */
/* @var $model app\models\Inventory */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Inventory',
]) . ' ' . Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="inventory-update">

    <?= $this->render('_form', [
        'model' => $model,
    	'partnumber' => $partnumber,
    	'files' => $files
    ]) ?>

</div>
