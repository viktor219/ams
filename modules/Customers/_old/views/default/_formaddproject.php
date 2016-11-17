<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-view">
    <div class="row">
        <div id="companyname-group" class="col-sm-12">
            <label for="companyname" class="sr-only"></label>
            <input value="<?php if (isset($customer->companyname)) echo $customer->companyname; ?>" type="text" name="companyname" placeholder="Company Name (Required)" id="companyname" class="form-control">
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-sm-6">
            <span class="btn btn-default btn-file">
                Upload Logo <input type="file" name="fileToUpload" id="fileToUpload">
            </span>

        </div>

    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-sm-6">

            <input type="checkbox" id="requireordernumber" name="requireordernumber" <?php if (isset($customer->requireordernumber)) {
    if ($customer->requireordernumber == 1) echo "checked";
    else echo '';
} else echo 'checked'; ?>>
            <label for="requireordernumber" style="padding-left:0px;" class="checkbox-inline">Require Order Number</label>
        </div>
        <div class="col-sm-6">

            <input type="checkbox" id="requireserialnumber" name="requireserialnumber" <?php if (isset($customer->trackserials)) {
    if ($customer->trackserials == 1) echo "checked";
    else echo '';
} else echo 'checked'; ?>>
            <label for="requireserialnumber" class="checkbox-inline" style="padding-left:0px;">Require Serial Numbers</label>

        </div>
    </div>
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
    <input type="hidden" name="customerId" value="<?php if (isset($present_id)) echo $present_id; ?>" />
</div>

<script>
    $("[name='requireordernumber']").bootstrapSwitch();
    $("[name='requireserialnumber']").bootstrapSwitch();
</script>


