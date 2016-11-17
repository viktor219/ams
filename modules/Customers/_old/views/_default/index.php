<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Location;
use app\models\Medias;
$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
      
     <?= Yii::$app->session->getFlash('error'); ?>
     <?= Yii::$app->session->getFlash('success'); ?>
    <h1>

        <?= Html::encode($this->title) ?>

        <span class="pull-right">
            <a href="javascript://" class="btn btn-success assignUser">Assign Users</a>
            <a href="javascript://" class="btn btn-success createCustomer">Add New Customer</a>
        </span>         

    </h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'companyname',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model) {
                    return $model->companyname;
                }
            ],
            ['attribute' => 'totalLocation',
                'label' => Yii::t('app', 'Locations'),
                'format'=>'html',
                'value' => function($model) {
                    $count = Location::find()->where(['customer_id' => $model->id])->count();
                    $_btn_location = '&nbsp;<a class="btn-xs btn-primary pull-right viewAllLocations" href="'.Yii::$app->request->baseUrl.'/customers/default/locations/?customer='.$model->id.'">View ('.$count.')</a>';
                    
                    $_extra = '';
                    if(($model->defaultshippinglocation == $model->defaultbillinglocation) && $model->defaultshippinglocation  > 0){
                        
                        $_dlocationOne = Location::findOne($model->defaultshippinglocation);
                        if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
                            $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address;
                        }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
                            $_extra = $_extra." ".$_dlocationOne->address;
                        }
                        
                    }else{
                        
                        if($model->defaultshippinglocation  > 0){
                            
                            $_dlocationOne = Location::findOne($model->defaultshippinglocation);
                            if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
                                $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address;
                            }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
                                $_extra = $_extra." ".$_dlocationOne->address;
                            }
                        }
                        if($model->defaultbillinglocation  > 0){
                            
                            $_dlocationOne = Location::findOne($model->defaultbillinglocation);
                            if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
                                $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address;
                            }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
                                $_extra = $_extra." ".$_dlocationOne->address;
                            }
                        }
                    }
                    return $_extra."<br>".Yii::$app->formatter->asHtml($_btn_location);
                }
            ],
            ['attribute' => 'picture_id',
                'label' => Yii::t('app', 'Picture'),
                'format'=>'html',
                'value' => function($model) {
            
                    $_my_media = Medias::findOne($model->picture_id);
                    
                    if(isset($_my_media->filename) && !empty($_my_media->filename)){
                        $target_file = Yii::getAlias('@webroot').'/'.$_my_media->path.'/'.$_my_media->filename;
                        if (file_exists($target_file)) {
                            
                             $_my_image = '<img src="'.Yii::$app->request->baseUrl.'/'.$_my_media->path.'/'.$_my_media->filename.'" alt="picture" style="width:124px;height:128px;">';
                   
                        }else{
                           return "No Picture"; 
                        }
                        
                    }else {
                        
                        return "No Picture";
                    }
                    return Yii::$app->formatter->asHtml($_my_image);
                }
            ],
            ['attribute' => 'firstname',
                'label' => Yii::t('app', 'First Name'),
                'value' => function($model) {
                
                    return $model->firstname;
                }
            ],
            ['attribute' => 'lastname',
                'label' => Yii::t('app', 'Last Name'),
                'value' => function($model) {
                
                    return $model->lastname;
                }
            ],        
            ['attribute' => 'phone',
                'label' => Yii::t('app', 'Phone'),
                'value' => function($model) {
                    return $model->phone;
                }
            ],
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
                'value' => function($model) {
                    return $model->email;
                }
            ],
            ['attribute' => 'trackserials',
                'label' => Yii::t('app', 'Serials'),
                'value' => function($model) {
                    if($model->trackserials==1)
                    return 'Yes';
                    else
                    return 'No';
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'template' => '{view} {update} {delete} {addprojct}{showallproject}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View Customer'),
                                    'class' => 'glyphicon glyphicon-eye-open viewCustomer','cid'=>$model->id]);
                    },
                            'update' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'Manage Customer'),
                                    'class' => 'glyphicon glyphicon-edit updateCustomer','cid'=>$model->id]);
                    },
                            'delete' => function ($url, $model, $key) {
                        
                        return Html::a('', "javascript://", ['title' => Yii::t('app', 'Delete Customer'),
                                    'class' => 'glyphicon glyphicon-trash deleteCustomer','cid'=>$model->id]);
                       
                    },
                            
                            'addprojct' => function ($url, $model, $key) {

                                    $url = "javascript://";
                                    $options = [
                                        'title' => Yii::t('app', 'Add Project'),
                                        'id' => $model->id,
                                        'class' => 'btn btn-info btn-xs btnAddProject'
                                    ];
                                   return Html::a('Add Project', $url, $options);
                             },
                             'showallproject' => function ($url, $model, $key) {

                                    $url = "javascript://";
                                    $options = [
                                        'title' => Yii::t('app', 'Show All Project'),
                                        'id' => $model->id,
                                        'class' => 'btn btn-info btn-xs btnShowAllProject',
                                        'style' => 'margin-top:10px;'
                                    ];
                                     return Html::a('Show All Project', $url, $options);
                             }            
                                     
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>
 
        </div>


<div class="modal fade" id='projectAdd'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Add Project'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/addproject" method="post" id="projectAddRegister" novalidate="novalidate" enctype="multipart/form-data">
        <div class="modal-body">
            <div id="projectAddForm" ></div>
        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>




<div class="modal fade" id='showAllProjects'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'All Projects'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfshowAllProjects">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>



<div class="modal fade" id='customerDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Customer Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfCustomer">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='customerCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Customer'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/create" method="post" id="customerRegisterForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="customerCreationForm" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit" value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='customerUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Update Customer'); ?><span id="req_id"></span></h4>
    </div>
     <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/update" method="post" id="customerUpdateRegisterForm" novalidate="novalidate" enctype="multipart/form-data">
        <div class="modal-body">

            <div id="customerUpdateForm">

            </div>

        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit" style="margin-bottom: 5px;" value="<?php echo Yii::t('app', 'Update'); ?>" class="btn btn-primary">
        </div>
     </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/customer.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
