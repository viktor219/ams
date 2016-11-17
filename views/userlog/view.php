<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UserLogTracking */

$this->title = 'Tracking' . '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Log Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-tracking-view">

    <h1><?= Html::encode($this->title) ?></h1>
	
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'userid',
            'location_id',
            'mac_address',
            'ip_address',
            'real_ip_address',
            'browser:ntext',
            'using_proxy',
            'device_type',
            'status',
            'created_at',
        ],
    ]) ?>

</div>
