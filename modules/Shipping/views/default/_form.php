<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-view">

    <div class="row">

        <div class="col-sm-12">

            <label class="sr-only" for="locationId"></label>
            <select class="form-control" id="locationId" name="locationId">
                <option  value=''>Select Location:</option>
                <?php
                if (isset($locations) && count($locations) > 0 && !empty($locations)) {

                    for ($k = 0; $k < count($locations); $k++) {
                        ?>

                        <option  value="<?php echo $locations[$k]['id']; ?>"><?php echo $locations[$k]['address']; ?></option>

                    <?php
                    }
                }
                ?>
            </select>

        </div>
    </div>
    <div class="row-margin"></div>


    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
    <input type="hidden" id='customerId' name="customerId" value="<?php if (isset($_customer_id)) echo $_customer_id ?>" />
</div>


