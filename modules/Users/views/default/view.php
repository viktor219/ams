<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\UserCreate;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\Department;
?>
<div class="user-view">
    <?php 
    $_user_type = "Customer";
    if (isset($model->usertype) && $model->usertype == 1) {
        $_user_type = "Customer";
    } else if (isset($model->usertype) && $model->usertype == 2) {
        $_user_type = "Receiving";
    } else if (isset($model->usertype) && $model->usertype == 3) {
        $_user_type = "Technician";
    } else if (isset($model->usertype) && $model->usertype == 4) {
        $_user_type = "Shipping";
    } else if (isset($model->usertype) && $model->usertype == 5) {
        $_user_type = "Billing";
    } else if (isset($model->usertype) && $model->usertype == 6) {
        $_user_type = "Sales";
    } else if (isset($model->usertype) && $model->usertype == 7) {
        $_user_type = "Admin";
    } else if (isset($model->usertype) && $model->usertype == 8) {
        $_user_type = "Purchasing";
    }
    
    $department = 'All Departments';
    if ($model->department > 0) {
        $_my_department = Department::findOne($model->department);
        if (isset($_my_department->name)) {
            $department = $_my_department->name;
        }
    }
    
    $_customer_name = 'All Customers';
    $id = $model->id;
    $_my_customer = UserHasCustomer::find()->where(['userid' => $id])->all();
    if (isset($_my_customer[0]['customerid'])) {
        $_check = $_my_customer[0]['customerid'];
        $_my_customer_details = Customer::findOne($_check);
        if (isset($_my_customer_details->companyname)) {

            $_customer_name = $_my_customer_details->companyname;
        }
    }
?>
   <div style="text-align:center; margin-bottom: 10px;">
       <?= Html::img($profile_image, ['alt'=>'logo', 'height'=>'250px', 'width'=>'250px']); ?>
   </div>    
    
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'firstname',
                'label' => Yii::t('app', 'First Name'),
            ],
            ['attribute' => 'lastname',
                'label' => Yii::t('app', 'Last Name'),
            ],
            ['attribute' => 'username',
                'label' => Yii::t('app', 'User Name'),
            ],
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'usertype'),
                'value' => $_user_type
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'Department'),
                'value' => $department
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'Customer'),
                'value' => $_customer_name
            ],
        ],
    ])
    ?>


</div>
