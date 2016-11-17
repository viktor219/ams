
<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Location;
use app\models\Customer;
$this->title = Yii::t('app', 'Shipments of ').$customerName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shipments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
      
     <?= Yii::$app->session->getFlash('error'); ?>
     <?= Yii::$app->session->getFlash('success'); ?>
    <h1>

        <?= Html::encode($this->title) ?>

        <span class="pull-right">
   
            <a href="javascript://" presentCustomerId="<?php if(isset($_GET['customer'])) echo $_GET['customer'];?>" class="btn btn-success createShipment">Add New Shipment</a>
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
                    $customerName= Customer::findOne($model->customer_id);
                    
                    if(isset($customerName->firstname)){
                        
                        return $customerName = $customerName->firstname." ".$customerName->lastname;
                    }
                    return "";
                }
            ],
            
            ['attribute' => 'type	',
                'label' => Yii::t('app', 'Type'),
                'value' => function($model) {
                    if($model->type == 1){
                        
                        return "Primary";
                        
                    }else{
                        
                        return "Secondary";
                    }

                }
            ],  
              
            ['attribute' => 'location_id',
                'label' => Yii::t('app', 'Shipping To'),
                'value' => function($model) {
                    $_extra = "";
                    $_dlocationOne = Location::findOne($model->location_id);
                    if (isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)) {
                        $_extra = $_extra . " Store#: " . $_dlocationOne->storenum . $_dlocationOne->address;
                    } else if (isset($_dlocationOne->address) && !empty($_dlocationOne->address)) {
                        $_extra = $_extra . " " . $_dlocationOne->address;
                    }
                    return $_extra;
                }
            ],             
            ['class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'template' => '{view} {update} {delete}{pdfreport}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View Shipment'),
                                    'class' => 'glyphicon glyphicon-eye-open viewShipment','sid'=>$model->id]);
                    },
                            'update' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'Manage Shipment'),
                                    'class' => 'glyphicon glyphicon-edit updateShipment','lid'=>$model->id]);
                    },
                            'delete' => function ($url, $model, $key) {
                        
                        return Html::a('', "javascript://", ['title' => Yii::t('app', 'Delete Shipment'),
                                    'class' => 'glyphicon glyphicon-trash deleteShipment','cid'=>$_GET["customer"],'sid'=>$model->id]);
                       
                    
                    },
                            
                    'pdfreport' => function ($url, $model, $key) {

                                $url = 'javascript://';
                                $options = [
                                    'title' => Yii::t('app', 'PDF Report'),
                                    'id' => $model->id,
                                    'class' => 'btn btn-info btn-xs btnShowAllProject',
                                    'style' => 'margin-top:15px;display:block;width:100px;'
                                ];
                                 return Html::a('PDF Report', $url, $options);
                         }   
                            
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>

 </div>


<div class="modal fade" id='shipmentCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Shipment'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/shipping/default/create" method="post" id="shipmentRegisterForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="shipmentCreationForm" >

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



<div class="modal fade" id='shipmentDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Shipment Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfshipment">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>



<div class="modal fade" id='shipmentAdd'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Add Project'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/shipping/default/addproject" method="post" id="shipmentAddRegister" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="shipmentAddForm" >

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



<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/shipment.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>

