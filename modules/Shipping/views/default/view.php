<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\UserCreate;
use app\models\Customer;
use app\models\Location;
use app\models\UserHasCustomer;
use app\models\Department;
?>
<div class="user-view">
    <?php 
    //print_r($model);
    $shippingType = "";
    if ($model->type == 1) {

        $shippingType = "Primary";
    } else {

        $shippingType =  "Secondary";
    }
    
    $_customer_name = '';
    $id = $model->customer_id;
   
    $_my_customer_details = Customer::findOne($id);
    if (isset($_my_customer_details->companyname)) {

        $_customer_name = $_my_customer_details->companyname;
    }
    
    
    $_extra = "";
    $_dlocationOne = Location::findOne($model->location_id);
    if (isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)) {
        $_extra = $_extra . " Store#: " . $_dlocationOne->storenum . $_dlocationOne->address;
    } else if (isset($_dlocationOne->address) && !empty($_dlocationOne->address)) {
        $_extra = $_extra . " " . $_dlocationOne->address;
    }
   
    ?>
    
    
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'customer_id',
                'label' => Yii::t('app', 'Customer'),
                'value' =>$_customer_name
            ],
            ['attribute' => 'location_id',
                'label' => Yii::t('app', 'Shipping To'),
                'value' =>$_extra
            ],
            ['attribute' => 'type',
                'label' => Yii::t('app', 'Type'),
                'value' =>$shippingType
            ],
            ['attribute' => 'created_at',
                'label' => Yii::t('app', 'Created At'),
            ],
            
        ],
    ])
    ?>


</div>
