<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="user-view">

    <div class="row">
        <div class="col-sm-12">
            <label for="departmentName" class="sr-only"></label>
            <input value="<?php if(isset($department->name)){echo $department->name;}?>" type="text" placeholder="Deparment Name (Required)" name="departmentName" id="departmentName" class="form-control">
        
        </div>
        <input value="<?php if(isset($department->id)){echo $department->id;}?>" type="hidden" placeholder="" name="departmentId" id="departmentId" class="form-control">
    </div>
    
    <br>
    
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />

</div>



