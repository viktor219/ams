<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UserLogLocationTracking */

$this->title = 'Country : ' . $model->country_name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-location-tracking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'continent_code',
            'contry_code',
            'country_code_3',
            'country_name',
            'region',
            'region_name',
            'city',
            'postal_code',
            'latitude',
            'longitude',
            'area_code',
            'dma_code',
            'currency_code',
			[
				'format' => 'raw',
				'label' => 'Currency Symbol',
				'value' => $model->currency_symbol
			],
            'timezone',
            'created_at',
        ],
    ]) ?>

</div>
