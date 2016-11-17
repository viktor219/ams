<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchasingitemrequests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchasingitemrequest-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Purchasingitemrequest', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'requestby',
            'qty',
            'item',
            'ordernumber',
            // 'created_at',
            // 'modified_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
