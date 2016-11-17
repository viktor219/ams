<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UserLogLocationTracking */

$this->title = 'Create User Log Location Tracking';
$this->params['breadcrumbs'][] = ['label' => 'User Log Location Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-location-tracking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
