<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Log Location Trackings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-location-tracking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Log Location Tracking', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'continent_code',
            'contry_code',
            'country_code_3',
            'country_name',
            // 'region',
            // 'region_name',
            // 'city',
            // 'postal_code',
            // 'latitude',
            // 'longitude',
            // 'area_code',
            // 'dma_code',
            // 'currency_code',
            // 'currency_symbol',
            // 'timezone',
            // 'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
