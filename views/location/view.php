<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\models\Location */

$this->title = 'View Location';
$this->params['breadcrumbs'][] = (isset($order)) ? ['label' => 'Orders', 'url' => ['/orders/index']] : ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-view">

<!--
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
-->
<p>
	<?= Html::a('Back', ['/orders/view', 'id' => $order], ['class' => 'btn btn-danger']) ?>
</p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
				'label' => 'Customer',
				'value' => Customer::findOne($model->customer_id)->companyname,
			],
            'storename',
            'storenum',
            'address',
            'address2',
            'country',
            'city',
            'state',
            'zipcode',
            'phone',
            'email:email',
            'created_at',
        ],
    ]) ?>

</div>
