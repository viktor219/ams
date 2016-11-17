<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = Yii::t('app', 'Locations of ').$customerName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
      
     <?= Yii::$app->session->getFlash('error'); ?>
     <?= Yii::$app->session->getFlash('success'); ?>
    <h1>

        <?= Html::encode($this->title) ?>

        <span class="pull-right">
            <?php if(isset($_SERVER["HTTP_REFERER"])){?>
            <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'javascript://'?>" class="btn btn-primary createNewLocation">Go Back</a>
            <?php  } ?>
            <a href="javascript://" presentCustomerId="<?php if(isset($_GET['customer'])) echo $_GET['customer'];?>" class="btn btn-success createNewLocation">Add New Location</a>
        </span>         

    </h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'customer_id	',
                'label' => Yii::t('app', 'Customer'),
                'value' => function($model) {
                    return $model->customer_id.' ('.$model->customerName.')';
                }
            ],
            ['attribute' => 'storenum',
                'label' => Yii::t('app', 'Store'),
                'value' => function($model) {
                    return $model->storenum;
                }
            ],
            ['attribute' => 'country',
                'label' => Yii::t('app', 'Country'),
                'value' => function($model) {
                    return $model->country;
                }
            ],
            ['attribute' => 'zipcode',
                'label' => Yii::t('app', 'Zip Code'),
                'value' => function($model) {
                    return $model->zipcode;
                }
            ],      
            ['class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View Location'),
                                    'class' => 'glyphicon glyphicon-eye-open viewLocation','lid'=>$model->id]);
                    },
                            'update' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'Manage Location'),
                                    'class' => 'glyphicon glyphicon-edit updateLocation','lid'=>$model->id]);
                    },
                            'delete' => function ($url, $model, $key) {
                        
                        return Html::a('', "javascript://", ['title' => Yii::t('app', 'Delete Location'),
                                    'class' => 'glyphicon glyphicon-trash deleteLocation','cid'=>$_GET["customer"],'lid'=>$model->id]);
                       
                    
                    },
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>

 </div>

<div class="modal fade" id='locationDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Location Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfLocation">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>




<div class="modal fade" id='locationCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Location'); ?><span id="req_id"></span></h4>
    </div>
    <form class="form-group form-group-sm" action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/locationcreate" method="post" id="locationRegisterForm" novalidate="novalidate">

        <div class="modal-body">

            <div id="locationCreationForm" >

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



<div class="modal fade" id='locationUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Update Location'); ?><span id="req_id"></span></h4>
    </div>
     <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/locationupdate" method="post" id="locationUpdateRegisterForm" novalidate="novalidate">
        <div class="modal-body">

            <div id="locationUpdateForm">

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
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/location.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
