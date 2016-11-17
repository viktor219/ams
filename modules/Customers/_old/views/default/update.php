<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'Update Customer #' . $customer->companyname;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="customer-update">
    <?=
    $this->render('_form', [
        'customer' => $customer,
    	'customer_settings' => $customer_settings,
        'locationShipping' => $locationShipping,
        'locationBilling' => $locationBilling,
        'projects' => $findProjects,
    ])
    ?>
</div>



