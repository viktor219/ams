<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'New Purchase Order (PO#: ' . $ordernumber . ')';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <?= $this->render('_form', [
        'model' => $model,
		'ordernumber' => $ordernumber,
    	'items_requested' => $items_requested,
    	'item_requested' => $item_requested,
    	'count_itemsrequested' => $count_itemsrequested
    ]) ?>

</div>
