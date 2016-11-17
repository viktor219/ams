<?php

use yii\helpers\Html;
?>


<div class="col-lg-12 well bs-component">

    <?=
    $this->render('_form', [
        'locations' => $locations,
        '_customer_id' => $_customer_id,
    ])
    ?>

</div>



