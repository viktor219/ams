<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Purchase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'customer_id',
            'vendor_id',
            'shipping_company',
            'location_id',
            'number_generated',
            'number_radius',
            'number_bb',
            'notes:ntext',
            'status',
            'ordertype',
            'orderfile',
            'type',
            'returned',
            'returneddate',
            'estimated_time',
            'trackingnumber',
            'trackinglink',
            'dateshipped',
            'shipby',
            'requestedby',
            'trucknum',
            'sealnum',
            'dateonsite',
            'created_at',
            'modified_at',
        ],
    ]) ?>

</div>
