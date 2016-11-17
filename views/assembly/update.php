<?php

use yii\helpers\Html;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\models\ModelAssembly */

$this->title = 'Update Model Assembly: ' . ' ' . $model->descrip;
$this->params['breadcrumbs'][] = ['label' => 'Model Assemblies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$customer = Customer::findOne($assembly->customerid);
?>
<div class="model-assembly-update">

    <?= $this->render('_form', [
        'model' => $model,
    	'customer' => $customer, 
    	'assemblies' => $assemblies
    ]) ?>

</div>
