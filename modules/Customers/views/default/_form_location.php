<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="user-view">

    <div class="row">
        <div class="col-sm-6">
            <label for="storenum" class="sr-only"></label>
            <input value="<?php if(isset($location->storenum)) echo $location->storenum;?>" type="text" placeholder="Store or Tech Number (Optional)" name="storenum" id="storenum" class="form-control">
        </div>
        <div class="col-sm-6">
            <label for="storename" class="sr-only"></label>
            <input value="<?php if(isset($location->storename)) echo $location->storename;?>" type="text" placeholder="Store or Tech Name (Optional)" name="storename" id="storename" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div id="location_address-group" class="col-sm-6">
            <label for="address" class="sr-only"></label>
            <input value="<?php if(isset($location->address)) echo $location->address;?>" type="text" placeholder="Address (Required)" name="location_address" id="location_address" class="form-control">
            <span aria-hidden="true" class="fa fa-home form-control-feedback right"></span>
        </div>
        <div id="location_country-group" class="col-sm-6">
            <label for="country" class="sr-only"></label>
            <input value="<?php if(isset($location->country)) echo $location->country;?>" type="text" placeholder="Country (Required)" name="location_country" id="location_country" class="form-control">
            <span aria-hidden="true" class="fa fa-globe form-control-feedback right"></span>
        </div>
    </div>
    <br>
    <div class="row">
        <div id="location_city-group" class="col-sm-6">
            <label for="city" class="sr-only"></label>
            <input value="<?php if(isset($location->city)) echo $location->city;?>" type="text" placeholder="City (Required)" name="location_city" id="location_city" class="form-control ">
            <span aria-hidden="true" class="fa fa-globe form-control-feedback right"></span>
        </div>
        <div id="location_state-group" class="col-sm-3">
            <label for="state" class="sr-only"></label>
            <input value="<?php if(isset($location->state)) echo $location->state;?>" type="text" placeholder="State (Required)" name="location_state" id="location_state" class="form-control">
        </div>
        <div id="location_zip-group" class="col-sm-3">
            <label for="zip" class="sr-only"></label>
            <input value="<?php if(isset($location->zipcode)) echo $location->zipcode;?>" type="text" placeholder="Zip (Required)" name="location_zip" id="location_zip" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div id="location_email-group" class="col-sm-6">
            <label for="email" class="sr-only"></label>
            <input value="<?php if(isset($location->email)) echo $location->email;?>" type="email" placeholder="Email (Optional)" name="location_email" id="location_email" class="form-control">
        </div>
        <div class="col-sm-6">
            <label for="phone" class="sr-only"></label>
            <input value="<?php if(isset($location->phone)) echo $location->phone;?>" type="tel" placeholder="Phone (Optional)" name="location_phone" id="location_phone" class="form-control">
        </div>
    </div>
     <input type="hidden" name="customerId" id="customerId" value="<?php if(isset($location->customer_id)) echo $location->customer_id;else if(isset($_customer_id)) echo $_customer_id;?>" />
     <input type="hidden" name="locationId" id="locationId" value="<?php if(isset($location->id)) echo $location->id;?>" />
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />

</div>



