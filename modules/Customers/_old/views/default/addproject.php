<?php

use yii\helpers\Html;
?>


<div class="col-lg-12 well bs-component">

    <?=
    $this->render('_formaddproject', [
        'customer' => $customer,
        'present_id'=>$present_id
    ])
    ?>

</div>



