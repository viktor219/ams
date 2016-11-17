<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\models\Customer;
use app\models\UserHasCustomer;
use app\models\Department;
$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
      
     <?= Yii::$app->session->getFlash('error'); ?>
     <?= Yii::$app->session->getFlash('success'); ?>
     <?= Yii::$app->session->getFlash('successDepartment'); ?>
    <h1>

        <?= Html::encode($this->title) ?>

        <span class="pull-right">
            <a href="javascript://" class="btn btn-success showDepartments">Departments</a>
            <a href="javascript://" class="btn btn-success createUser">Add New User</a>
        </span>         

    </h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
                'value' => function($model) {
                    return $model->email;
                }
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'User Type'),
                'value' => function($model) {
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
                    return $_user_type;
                }
            ],
            ['attribute' => 'usertype',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model) {
                   $_customer_name = 'All Customers';
                   $id = $model->id;
                   $_my_customer = UserHasCustomer::find()->where(['userid' => $id])->all();
                   if(isset($_my_customer[0]['customerid'])){
                       $_check = $_my_customer[0]['customerid'];
                       $_my_customer_details = Customer::findOne($_check);
                       if(isset($_my_customer_details->companyname)){
                           
                           $_customer_name = $_my_customer_details->companyname;
                       }
                   }
                   return $_customer_name;
                }
            ],
            ['attribute' => 'department',
                'label' => Yii::t('app', 'Department'),
                'value' => function($model) {
                   $department = 'All Departments';
                   if($model->department > 0){
                        $_my_department = Department::findOne($model->department);
                        if(isset($_my_department->name)){
                            $department = $_my_department->name;
                        }
                   }
                   return $department;
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View User'),
                                    'class' => 'glyphicon glyphicon-eye-open viewUser','uid'=>$model->id]);
                    },
                            'update' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'Manage User'),
                                    'class' => 'glyphicon glyphicon-edit updateUser','uid'=>$model->id]);
                    },
                            'delete' => function ($url, $model, $key) {
                        
                        return Html::a('', "javascript://", ['title' => Yii::t('app', 'Delete User'),
                                    'class' => 'glyphicon glyphicon-trash deleteUser','uid'=>$model->id]);
                       
                    },
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>

        </div>

<div class="modal fade" id='depatmentCreationsPopsUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'update Department'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/editdepartment" method="post" id="departmentCreationFormUpdate" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="departmentCreationFormViewUpdate" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>


<div class="modal fade" id='depatmentCreationsPops'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Department'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/createdepartment" method="post" id="departmentCreationForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="departmentCreationFormView" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id='departmentsPops'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'All Departments'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="departmentsPopsView">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>



<div class="modal fade" id='userDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'User Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfUser">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='userCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create User'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/create" method="post" id="userRegisterForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="userCreationForm" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='userUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Update User'); ?><span id="req_id"></span></h4>
    </div>
     <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/update" method="post" id="userUpdateRegisterForm" novalidate="novalidate" enctype="multipart/form-data">
        <div class="modal-body">

            <div id="userUpdateForm">

            </div>

        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Update'); ?>" class="btn btn-primary">
        </div>
     </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/user.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<script>
    var popupHit = 0;
    popupHit = '<?php if(Yii::$app->session->getFlash('successDepartment')){ echo "1";}else echo "0";?>';

    
 
    $(document).ready(function(){
        
        if(popupHit==1)
        {
            
            $('.showDepartments').trigger('click');
        }
    });
</script>