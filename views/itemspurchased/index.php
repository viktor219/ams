<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Itemspurchaseds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="itemspurchased-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Itemspurchased', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ordernumber',
            'model',
            'qty',
            'price',
            // 'status',
            // 'created_at',
            // 'modified_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
