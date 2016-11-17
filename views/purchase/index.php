<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchase-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Purchase', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_id',
            'vendor_id',
            'shipping_company',
            'location_id',
            // 'number_generated',
            // 'number_radius',
            // 'number_bb',
            // 'notes:ntext',
            // 'status',
            // 'ordertype',
            // 'orderfile',
            // 'type',
            // 'returned',
            // 'returneddate',
            // 'estimated_time',
            // 'trackingnumber',
            // 'trackinglink',
            // 'dateshipped',
            // 'shipby',
            // 'requestedby',
            // 'trucknum',
            // 'sealnum',
            // 'dateonsite',
            // 'created_at',
            // 'modified_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
