<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-view">
    <div class="row">
        <div id="companyname-group" class="col-sm-4">
            <label for="companyname" class="sr-only"></label>
            <input value="<?php if(isset($customer->companyname)) echo $customer->companyname;?>" type="text" name="companyname" placeholder="Company Name (Required)" id="companyname" class="form-control">
        </div>
        <div id="contactname-group" class="col-sm-4">
            <label for="contactname1" class="sr-only"></label>
            <input value="<?php if(isset($customer->firstname)) echo $customer->firstname;?>" type="text" name="contactname1" placeholder="First Name (Optional)" id="contactname1" class="form-control">
        </div>
        <div id="contactname-group" class="col-sm-4">
            <label for="contactname2" class="sr-only"></label>
            <input value="<?php if(isset($customer->lastname)) echo $customer->lastname;?>" type="text" name="contactname2" placeholder="Last Name (Optional)" id="contactname2" class="form-control">
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div id="email-group" class="col-sm-6">
            <label for="email" class="sr-only"></label>
            <input value="<?php if(isset($customer->email)) echo $customer->email;?>" type="email" name="email" placeholder="Email (Required)" id="email" class="form-control">
        </div>
        <div id="phone-group" class="col-sm-6">
            <label for="phone" class="sr-only"></label>
            <input value="<?php if(isset($customer->phone)) echo $customer->phone;?>" type="tel" name="phone" placeholder="Phone (Optional)" id="phone" class="form-control">
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-sm-12">
            <h6><strong>Default Shipping Address:</strong></h6>
        </div>
    </div>
    <div class="row">
        <div id="shipping_address-group" class="col-sm-6">
            <label for="address" class="sr-only"></label>
            <input value="<?php if(isset($locationShipping->address)) echo $locationShipping->address;?>" type="text" placeholder="Address (Required)" name="shipping_address" id="shipping_address" class="form-control">
        </div>
        <div id="shipping_country-group" class="col-sm-6">
            <label for="country" class="sr-only"></label>
            <input value="<?php if(isset($locationShipping->country)) echo $locationShipping->country;?>" type="text" placeholder="Country (Required)" name="shipping_country" id="shipping_country" class="form-control">
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div id="shipping_city-group" class="col-sm-6">
            <label for="city" class="sr-only"></label>
            <input value="<?php if(isset($locationShipping->city)) echo $locationShipping->city;?>" type="text" placeholder="City (Required)" name="shipping_city" id="shipping_city" class="form-control ">
        </div>
        <div id="shipping_state-group" class="col-sm-3">
            <label for="state" class="sr-only"></label>
            <input value="<?php if(isset($locationShipping->state)) echo $locationShipping->state;?>" type="text" placeholder="State (Required)" name="shipping_state" id="shipping_state" class="form-control">
        </div>
        <div id="shipping_zip-group" class="col-sm-3">
            <label for="zip" class="sr-only"></label>
            <input value="<?php if(isset($locationShipping->zipcode)) echo $locationShipping->zipcode;?>" type="text" placeholder="Zip (Required)" name="shipping_zip" id="shipping_zip" class="form-control">
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-md-12">
            <?php if(isset($customer->companyname)){?>
                <button aria-controls="collapseExample" aria-expanded="false" data-target=".collapseExample2" data-toggle="collapse" type="button" id="collapsingbutton-billing" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-collapse-down"></span> Add a different billing address.</button>
            <?php } else { ?>
                <button aria-controls="collapseExample" aria-expanded="false" data-target="#collapseExample2" data-toggle="collapse" type="button" id="collapsingbutton-billing" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-collapse-down"></span> Add a different billing address.</button>
            <?php } ?>
        </div>   
    </div>
    <input type="hidden" value="0" id="billing_required">
    <div id="collapseExample2" class="collapse collapseExample2">
        <div class="row">
            <div class="col-sm-12">
                <h6><strong>Default Billing Address:</strong> <small>(If different from shipping address.)</small></h6>
            </div>
        </div>
        <div class="row">
            <div id="billing_address-group" class="col-sm-6">
                <label for="address" class="sr-only"></label>
                <input value="<?php if(isset($locationBilling->address)) echo $locationBilling->address;?>" type="text" placeholder="Address (Required)" name="billing_address" id="billing_address" class="form-control">
            </div>
            <div id="billing_country-group" class="col-sm-6">
                <label for="country" class="sr-only"></label>
                <input value="<?php if(isset($locationBilling->country)) echo $locationBilling->country;?>" type="text" placeholder="Country (Required)" name="billing_country" id="billing_country" class="form-control">
            </div>
        </div>
        <div class="row-margin"></div>
        <div class="row">
            <div id="billing_city-group" class="col-sm-6">
                <label for="city" class="sr-only"></label>
                <input value="<?php if(isset($locationBilling->city)) echo $locationBilling->city;?>" type="text" placeholder="City (Required)" name="billing_city" id="billing_city" class="form-control ">
            </div>
            <div id="billing_state-group" class="col-sm-3">
                <label for="state" class="sr-only"></label>
                <input value="<?php if(isset($locationBilling->state)) echo $locationBilling->state;?>" type="text" placeholder="State (Required)" name="billing_state" id="billing_state" class="form-control">
            </div>
            <div id="billing_zip-group" class="col-sm-3">
                <label for="zip" class="sr-only"></label>
                <input type="text" value="<?php if(isset($locationBilling->zipcode)) echo $locationBilling->zipcode;?>" placeholder="Zip (Required)" name="billing_zip" id="billing_zip" class="form-control">
            </div>
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-sm-12">
            <h6><strong>Custom Settings:</strong></h6>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <span class="btn btn-default btn-file">
                Upload Logo <input type="file" name="fileToUpload" id="fileToUpload">
            </span>

        </div>
        <div class="col-sm-6">

            <input type="checkbox" id="requireordernumber" name="requireordernumber" <?php if(isset($customer->requireordernumber)){if($customer->requireordernumber==1)echo "checked";else echo '';} else echo 'checked';?>>
            <label for="requireordernumber" style="padding-left:0px;" class="checkbox-inline">Require Order Number</label>
        </div>
    </div>
    <div class="row-margin"></div>
    <div class="row">
        <div class="col-sm-6">
            <label for="defaultreceivinglocation" class="sr-only"></label>
            <input type="email" placeholder="Default Receiving Location (Optional)" name="defaultreceivinglocation" id="defaultreceivinglocation" class="form-control">
        </div>
        <div class="col-sm-6">
         
            <input type="checkbox" id="requireserialnumber" name="requireserialnumber" <?php if(isset($customer->trackserials)){if($customer->trackserials==1)echo "checked";else echo '';} else echo 'checked';?>>
            <label for="requireserialnumber" class="checkbox-inline" style="padding-left:0px;">Require Serial Numbers</label>
            
        </div>
    </div>
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <input type="hidden" name="customerId" value="<?php if(isset($customer->id)) echo $customer->id;?>" />
    <input type="hidden" name="dshippingId" value="<?php if(isset($locationShipping->id)) echo $locationShipping->id;?>" />
    <input type="hidden" name="dbillingId" value="<?php if(isset($locationBilling->id)) echo $locationBilling->id;?>" />
</div>

<script>
    $("[name='requireordernumber']").bootstrapSwitch();
    $("[name='requireserialnumber']").bootstrapSwitch();
</script>


