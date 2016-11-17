<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-view">
    <div class="row">
        <div id="companyname-group" class="col-sm-6">
            

                <label class="sr-only" for="projectId"></label>
                <select class="form-control" id="projectId" name="projectId">
                    <option value="" disabled="" selected="">Select Project:</option>
                    <?php
                    if (isset($projects) && count($projects) > 0 && !empty($projects)) {

                        for ($k = 0; $k < count($projects); $k++) {
                            ?>

                            <option  value="<?php echo $projects[$k]['id']; ?>"><?php echo $projects[$k]['companyname']; ?></option>

                        <?php
                        }
                    }
                    ?>
                </select>

                
        </div>
        <div id="contactname-group" class="col-sm-6">
            

                <label class="sr-only" for="userId"></label>
                <select class="form-control" id="userId" name="userId">
                    <option value="" disabled="" selected="">Select User:</option>
                    <?php
                    if (isset($users) && count($users) > 0 && !empty($users)) {

                        for ($k = 0; $k < count($users); $k++) {
                            ?>

                            <option value="<?php echo $users[$k]['id']; ?>"><?php echo $users[$k]['firstname']." ".$users[$k]['lastname']; ?></option>

                        <?php
                        }
                    }
                    ?>
                </select>

                 
        </div>

    </div>

    <div class="row-margin"></div>

    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />

</div>



