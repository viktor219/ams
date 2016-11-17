<?php

use app\modules\Orders\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Item;
use app\models\Customer;
use app\models\ShipmentBoxDetail;
use app\models\Itemsordered;
use app\models\Ordertype;
use app\models\Shipment;
use app\models\ShipmentsItems;
use yii\widgets\ActiveForm;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use yii\data\ActiveDataProvider;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => 'Shipping', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Create Shipment', 'url' => ['createshipment', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render("_modals/_showdimensionsmodal"); ?>
<?= $this->render("_modals/_validate", ['ispallet' => $ispallet]); ?>
<style>
    thead td, tbody td {
        text-align: left; 
    }

    #shipping-details-gridview-parent .panel-info>.panel-heading {
        color: #31708f;
        background-color: #EEE;
        border-color: #FFF;
    }
    #shipping-details-gridview-parent .panel-info
    {
        border-color: #DDD;
        border-width: 1px;
        box-shadow: 0 0 3px #ccc;
    }
</style>
<?php 
$shipCount = ShipmentsItems::find()
                ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
                ->innerJoin('lv_shipments', 'lv_shipments.id = lv_shipments_items.shipmentid')
                ->where(['ordernumber'=>$model->id])
                ->andWhere('master_trackingnumber is NOT NULL')
                ->groupBy('lv_shipments_items.shipmentid')->count();
?>
<div class="inprogres-index">
    <!-- Sales Order Dashboard -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="pull-left">
                    <h4  style="margin-left: 15px;margin: 0 10px">
                        <span class="glyphicon glyphicon-equalizer"></span> Shipment #<?= ($shipCount + 1); ?>
                            <?php //echo \app\models\Shipment::find()->where(['orderid' => $model->id])->andWhere('id <='.$_shipment->id)->count(); ?>
                    </h4>
                </div>
                <div class="vcenter pull-right" style="margin-top: 15px; margin-right: 15px;">
                    
<!--                    <div class="pull-right" style="font-weight:bold;">
                        Order Type: <?php //echo Ordertype::findOne($model->ordertype)->name; ?> <span style="color: #508caa;">SO# : <?php //echo $model->number_generated; ?></span> Delivery Method :<?php //echo $_delivery_method; ?> <span style="color: #508caa;"><? $highstatus; ?></span>
                    </div>-->
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="panel-body">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><i class="fa fa-bars"></i> Order Details</h2>
                            <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>

                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>SO#</th>
                                            <th>Order Type</th>
                                            <th>Delivery Method</th>
                                            <th>Status</th>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: middle">
                                                <?= $model->number_generated; ?>
                                            </td>
                                            <td style="vertical-align: middle">
                                                <?= Ordertype::findOne($model->ordertype)->name; ?>
                                            </td>
                                            <td style="vertical-align: middle">
                                                <?php echo $_delivery_method; ?>
                                            </td>
                                            <td style="vertical-align: middle">
                                                <a href="javascript:void(0)" class="btn btn-default btn-sm"><span style="color: #508caa;"><?= $highstatus; ?></span></a>
                                            </td>
                                        </tr>
                                    </table>
                        </div>
                    </div>
            </div>
            
            <?php
            foreach ($itemsList as $key => $item):
                $_model = Models::findOne($item['model']);
                $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                $customer = Customer::findOne($item['customer']);
                $model_label = '<b>' . $_manufacturer->name . ' ' . $_model->descrip . '</b>';
                $condition = array('status' => array_search('Ready to ship', Item::$status), 'ordernumber' => $model->id);
                if ($ispallet) {
                    $condition['outgoingpalletnumber'] = $item['pallet_box_number'];
                } else {
                    $condition['outgoingboxnumber'] = $item['pallet_box_number'];
                }
                $condition['model'] = $_model->id;
                $query = Item::find()->where($condition);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,
                ]);
//                $box_pallet_number = ($ispallet) ? $item->outgoingpalletnumber : $item->outgoingboxnumber;
                $box_pallet_number = $item['pallet_box_number'];
                ?>
                <div class="panel-body">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2><i class="fa fa-bars"></i> <?php echo ($ispallet) ? 'Pallet #' . $box_pallet_number : 'Box #' . $box_pallet_number; ?></h2>
                            <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <?php 
                                $boxWeightModel = ShipmentBoxDetail::find()->where(['pallet_box_number' => $box_pallet_number, 'modelid' => $_model->id, 'shipmentid' => $_shipment->id])->one();
                            ?>
                            <a hred="javascript:void(0);" class="btn btn-sm <?= ($boxWeightModel == NULL)? 'btn-info':'btn-success'; ?> pull-right <?= 'weight_box_'.$_shipment->id.'_'.$_model->id.'_'.$box_pallet_number.''; ?>" onclick="openBoxConfigModal(<?php echo $_shipment->id; ?>, <?php echo $_model->id; ?>, <?php echo $box_pallet_number; ?>)"><span style="color: white;" class="glyphicon <?= ($boxWeightModel == NULL)? 'glyphicon-plus':'glyphicon-edit'; ?>"></span> Weights & Dimensions</a>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <h4>Model: <?php echo $model_label; ?></h4>
                            <?=
                            GridView::widget([
                                'dataProvider' => $dataProvider,
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                                'summary' => '',
                                'columns' => [
                                    [
                                        'attribute' => 'serial',
                                        'format' => 'raw',
                                        'value' => function ($model) {
                                            return '<div style="line-height:40px;"><b>' . $model->serial . '</b></div>';
                                        }
                                    ],
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => '',
                                        'template' => '{move} {boxpallet}',
                                        'contentOptions' => ['style' => 'width:180px;text-align:center;', 'class' => 'action-buttons'],
                                        'buttons' => [
                                            'move' => function ($url, $model, $key) use ($ispallet) {
                                                $isShipped = app\models\ShipmentsItems::find()->where(['itemid' => $model->id])->count();
                                                $options = [
                                                    'title' => 'Move',
                                                    'id' => $model->id,
                                                    'class' => ['btn btn-info move_create_ship']
                                                ];
                                                $url = 'javascript:;';
                                                return Html::a('<span class="glyphicon glyphicon-move"></span>', $url, $options);
                                            },
                                                    'boxpallet' => function($url, $model, $key) use ($ispallet, $itemsList) {
                                                $selectedNumber = ($ispallet) ? $model->outgoingpalletnumber : $model->outgoingboxnumber;
                                                $label = ($ispallet) ? '-Pallet-' : '-Box-';
                                                $returnString = '<ul class="nav nav-tabs print_pack_button pull-right" style="display: none">
                                            <li role="presentation" class="dropdown">
                                                <a href="#" class="btn btn-info dropdown-toggle print_pack_label" data-toggle="dropdown" role="button" aria-expanded="true">' . $label . '<span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu" style="top: 35px; min-width: 70px; left: -8px">';
                                                $boxNumbers = array();
                                                foreach($itemsList as $item){
                                                    $boxNumbers[$item['pallet_box_number']] = $item['pallet_box_number'];
                                                }
                                                foreach ($boxNumbers as $key => $number):
                                                    //$number = ($ispallet) ? $item->outgoingpalletnumber : $item->outgoingboxnumber;
//                                                    $number = $item['pallet_box_number'];
                                                    $selectedClass = ($selectedNumber == $number) ? 'active' : '';
                                                    //$url = Url::toRoute(['/shipping/printpackinglist', 'id'=>$model->id, 'shipment' => $key]);
                                                    $curr_number = ($selectedNumber == $number) ? '': $selectedNumber;
                                                    $returnString.= '<li class="list ' . $selectedClass . '">';
                                                    $returnString .= '<a class="select_boxpallet" href="' . Yii::$app->request->baseUrl . '/shipping/changeboxpallet?id=' . $model->id . '&ispallet=' . $ispallet . '&number=' . $number . '&model='.$model->model.'&sel_number='.$curr_number.'" style="padding: 8px;">' . $number . '</a></li>';
                                                endforeach;
                                                $returnString.='</ul></li></ul>';
                                                return $returnString;
                                            }
                                                ],
                                            ]
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php $form = ActiveForm::begin(['action' => Yii::$app->request->baseUrl.'/shipping/createnow?id='.$_shipment->id.'&order='.$model->id, 'options' => ['id'=>'create-ship-form', 'class'=>'form-group form-group-sm']]); ?>
                        <button type="submit" class="btn btn-success pull-right" id="create_shipment" validate-url="<?php echo Yii::$app->request->baseUrl; ?>/shipping/validateboxdim?<?php echo 'shipment='.$_shipment->id.'&order='.$model->id.'&ispallet='.$ispallet; ?>"><span class="glyphicon glyphicon-store"></span> Create</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

        </div>
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/shipping.js"></script>