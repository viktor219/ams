<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserLogLocationTracking */

$this->title = 'Update User Log Location Tracking: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Log Location Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-log-location-tracking-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
