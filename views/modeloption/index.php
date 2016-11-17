<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Model Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Model Option', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idmodel',
            'name',
            'optiontype',
            'level',
            // 'parent_id',
            // 'checkable',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
