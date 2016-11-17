<?php

use yii\helpers\Html;
?>


<div class="col-lg-12 well bs-component">

    <?=
    $this->render('_form', [
        'customer' => $customer,
        'locationShipping' => $locationShipping,
        'locationBilling' => $locationBilling
    ])
    ?>

</div>



