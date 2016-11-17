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
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use yii\data\ActiveDataProvider;
use app\models\SalesorderWs;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

$this->title = Yii::t('app', 'Shipping Details');
$this->params['breadcrumbs'][] = ['label' => 'Shipping', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Create Shipment', 'url' => ['createshipment', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = ['label' => 'Create', 'url' => ['createship', 'id' => $model->id, 'shipment' => $_shipment->id]];
$this->params['breadcrumbs'][] = $this->title;

$hasservice = SalesorderWs::find()->where(['warehouse_id' => $model->id])->one();
?>
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
    .label_img {
        margin-top: 150px;
        max-width: 800px;
        max-height: 400px;
        -ms-transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
        transform: rotate(90deg);
    }
</style>
<?= $this->render("@app/modules/Orders/views/default/_modals/_relatedservice"); ?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_returnlabel"); ?>
<?php
$shipItems = ShipmentsItems::find()
        ->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')
        ->innerJoin('lv_shipments', 'lv_shipments.id = lv_shipments_items.shipmentid')
        ->where(['ordernumber' => $model->id])
        ->andWhere('master_trackingnumber is NOT NULL')
        ->groupBy('lv_shipments_items.shipmentid')
        ->orderBy('lv_shipments_items.id')
        ->all();
$shipCount = 1;
foreach ($shipItems as $shipItem) {
    if ($shipItem->shipmentid == $_shipment->id) {
        break;
    } else {
        $shipCount++;
    }
}
?>

<?= $this->render("_modals/_shippinglabel", ['shipmentBoxDetails' => $shipmentBoxDetails, 'id' => $id, 'hasservice' => $hasservice, 'model' => $model]); ?>
<?= $this->render("_modals/_viewdimensions"); ?>
<div class="inprogres-index">
    <!-- Sales Order Dashboard -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="pull-left">
                    <h4 style="margin: 0 10px;">
                        <span class="glyphicon glyphicon-equalizer"></span> Shipment #<?= $shipCount; ?>
                        <?php //echo \app\models\Shipment::find()->where(['orderid' => $model->id])->andWhere('id <='.$_shipment->id)->count();  ?>
                    </h4>
                </div>
                <div class="vcenter pull-right" style="margin-top: 15px; margin-right: 15px;">
                    <div class="pull-right" style="font-weight:bold;">
                        <!--Order Type: <?php //echo Ordertype::findOne($model->ordertype)->name;   ?> <span style="color: #508caa;">SO# : <?php echo $model->number_generated; ?></span> Delivery Method :<?php //echo $_delivery_method;   ?> <span style="color: #508caa;"><? $highstatus; ?></span>-->
                    </div>
                </div>

            </div>
        </div>
        <div class="panel-body">
            <div class="pull-right" style="margin-right: 10px; margin-bottom: 10px;">
                <a href="javascript:void(0);" class="btn btn-success" id="previewLabel">Preview</a>
                <a href="<?= Url::to(['/shipping/allprintlabel', 'id' => $id]) ?>" target="_blank" class="btn btn-success">Print All</a> 
                <?php if ($hasservice !== null) : ?>
                    <a href="javascript:;" class="btn btn-success" onClick="OpenRelatedServiceModal(<?= $model->id; ?>);">Return Label</a>           	
                <?php endif; ?>
                <a href="<?= Url::to(['/shipping/generatelabel', 'id' => $id]) ?>" target="_blank" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download</a>                
                <a href="<?php echo $trackingLink; ?>" class="btn btn-primary" target="_blank">Tracking</a>
            </div>
            <div class="panel-body">
                <div class="x_panel" style="margin: 0;padding-bottom: 0;">
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
                        <table class="table table-bordered table-striped stacktable" id="myOrderDet">
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
                $condition = array('ordernumber' => $model->id);
                if ($ispallet) {
                    $condition['outgoingpalletnumber'] = $item['pallet_box_number'];
                } else {
                    $condition['outgoingboxnumber'] = $item['pallet_box_number'];
                }
                $condition['model'] = $_model->id;
                $query = Item::find()->innerJoin('`lv_shipments_items`', '`lv_shipments_items`.`itemid` = `lv_items`.`id`')->where($condition);
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
                            <a hred="javascript:void(0);" class="btn btn-sm btn-info pull-right" onclick="viewBoxConfig(<?php echo $_shipment->id; ?>, <?php echo $_model->id; ?>, <?php echo $box_pallet_number; ?>)"><span style="color: white;" class="glyphicon glyphicon-eye-open"></span> View Weights & Dimensions</a>
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
                                        'template' => '{move} {boxpallet}',
                                        'header' => '',
                                        'contentOptions' => ['style' => 'width:180px;text-align:center;', 'class' => 'action-buttons'],
                                        'buttons' => [
                                            'move' => function ($url, $model, $key) use ($ispallet) {
                                                $isShipped = app\models\ShipmentsItems::find()->where(['itemid' => $model->id])->count();
                                                $classes = ($isShipped) ? ['btn btn-success'] : ['btn btn-danger'];
                                                $glyph = ($isShipped) ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-remove-sign';
                                                $options = [
                                                    'title' => 'Move',
                                                    'id' => $model->id,
                                                    'class' => $classes
                                                ];
                                                $url = 'javascript:;';
                                                return Html::a('<span class="' . $glyph . '"></span>', $url, $options);
                                            },
                                                ],
                                            ]
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <a class="btn btn-info pull-right" href="<?php echo Yii::$app->request->baseUrl; ?>/shipping/createshipment?id=<?php echo $model->id; ?>"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
                </div>
            </div>
        </div>
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/shipping.js"></script>