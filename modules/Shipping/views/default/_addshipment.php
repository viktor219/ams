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
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

$this->title = "Create Shipment";

$this->params['breadcrumbs'][] = ['label' => 'Shipping', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$_shipment = Shipment::find()->where(['orderid' => $model->id])->one();
$shipmethod = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);
$_company = ShippingCompany::findOne($shipmethod->shipping_company_id);
if ($shipmethod->shipping_company_id === 1) {
    $ups = new \Ups\Entity\Service;
    $ups->setCode($shipmethod->_value);
    $__shipping_method = $ups->getName();
} else if ($shipmethod->shipping_company_id === 3) { //Waiting DHL issues solved
} else {
    $__shipping_method = $shipmethod->_value;
}
$_delivery_method = $_company->name . ' ' . $__shipping_method;

//$number_items = Itemsordered::find()->where(['ordernumber' => $model->id])->sum('qty');
//$numbers_items_readytoship = Item::find()->where(['status' => array_keys(Item::$shippingstatus), 'ordernumber' => $model->id])->count();
//$readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
//$readypercentage = round($readypercentage, 2);
?>	
<?= $this->render("_modals/_showdimensionsmodal"); ?>
<style>
    thead td, tbody td {
        text-align: left; 
    }

    #shippingitems .panel-heading {
        color: #31708f;
        background-color: #EEE;
        border-color: #FFF;
    }
    #shippingitems{
        border-color: #DDD;
        border-width: 1px;
        box-shadow: 0 0 3px #ccc;
    }
    #shipments-main-gridview li.dropdown li{
        float: none;
    }
    #shipping-details-gridview-parent .panel-info{
        border: none;
    }
    ul.print_pack_button{
        border: none;
        margin-right: 10px;
    }
    ul.print_pack_button>li{
        background: white;
        border: 1px solid #ccc;
    }
    #selShipments:after{
        right: 60px;
    }
    .ready_to_ship{
        margin-top: 10px;
    }
    .ready_to_ship span{
        color: white;
    }
</style>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/public/css/stack_index.css" rel="stylesheet">
<div class="inprogres-index">
    <!-- Sales Order Dashboard -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="col-md-8 vcenter">
                    <h4 style="margin: 0">
                        <span class="glyphicon glyphicon-equalizer"></span> <?= Html::encode($this->title) ?> 
                    </h4>
                </div>
                <div class="col-md-4 vcenter text-right">
                    <?php
                    $options_top_1 = ['class' => 'btn btn-primary pull-right'];
                    if (Item::find()->where(['status' => array_search('Ready to ship', Item::$status), 'ordernumber' => $model->id])->count() == 0) {
                        $options_top_2 = ['class' => 'btn btn-sm btn-success pull-right', 'disabled' => true, 'id' => 'createShipment'];
                    } else {
                        $options_top_2 = ['class' => 'btn btn-sm btn-success pull-right'];
                    }
                    ?>
                    <?php /* 	<?= Html::a('<span class="glyphicon glyphicon-print"></span> Print Packing List', ['/shipping/printpackinglist', 'id'=>$model->id], $options_top_1) ?> */ ?>
                    <?php /* <?= Html::dropDownList('print_packaging', '', $printLists, ['class' => 'print_packaging', 'prompt' => '-Select Shipments-', 'order' => $model->id]); ?> */ ?>
                    <?php
                    $totalReadyShip = Item::find()->where(['status' => [array_search('In Shipping', Item::$status), array_search('Ready to ship', Item::$status)], 'ordernumber' => $model->id])->count();
                    if ($totalReadyShip > 0):
                        ?>
                        <?= Html::a('<span class="glyphicon glyphicon-export"></span> Create Shipment', Url::toRoute(['/shipping/createship', 'id' => $model->id]), $options_top_2) ?>
                    <?php endif; ?>
                    <!--                    <div class="pull-right" style="margin-right: 10px">
                    <?php //yii\bootstrap\Html::dropDownList('print_packing_list', '', $printLists, ['class' => 'form-control', 'order' => $model->id,'id' => 'printPackingLabel','prompt'=>'Print Packing List']);  ?>
                                        </div>-->
                    <!--                    <ul class="nav nav-tabs print_pack_button pull-right">
                                            <li role="presentation" class="dropdown">
                                                <a href="#" class="btn btn-info dropdown-toggle print_pack_label" data-toggle="dropdown" role="button" aria-expanded="true">Print Packing List <span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu" style="left: -18px; top: 35px;">
                    <?php //foreach($printLists as $key => $printList):  ?>
                    <?php //$url = Url::toRoute(['/shipping/printpackinglist', 'id'=>$model->id, 'shipment' => $key]); ?>
                                                    <li class="list">
                                                        <a href="<?php //echo $url;  ?>" style="padding: 8px;"><?php //echo $printList;  ?></a>
                                                    </li>
                    <?php //endforeach;  ?>
                                                </ul>                                            
                                            </li>
                                        </ul>-->
                </div>
            </div>
        </div>
        <div class="panel-body" id="shipping-details-gridview-parent">
            <div class="panel panel-info">
                <div class="" role="tabpanel" data-example-id="togglable-tabs" id="shipments-main-gridview">                                        
                    <ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
                        <li role="presentation" class="active col-sx-3">
                            <a href="#shippingitems" id="order-tab-1" role="tab" data-toggle="tab" aria-expanded="true">(<?= $itemsCount; ?>) Items</a>
                        </li>
                        <li role="presentation" class="dropdown">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Print Packing List <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <?php foreach ($printLists as $index => $printList) : ?>
                                    <li><a href="<?= Yii::$app->request->baseUrl; ?>/shipping/printpackinglist?id=<?= $model->id; ?>&shipment=<?= $printList->id; ?>" target= "_NEW" class="">Shipment <?php echo ($index + 1); ?></a></li>
                                <?php endforeach; ?>
                            </ul>                                           
                        </li>
                        <li role="presentation" class="dropdown">
                            <?php $class = count($shipmentLists) ? "" : "disabled" ?>
                            <a href="#" class="btn btn-default dropdown-toggle <?= $class; ?>" data-toggle="dropdown" role="button" aria-expanded="false">Shipments <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu" id="selShipments">
                                <?php foreach ($shipmentLists as $index => $shipmentList) : ?>
                                    <li><a href="<?= Yii::$app->request->baseUrl; ?>/shipping/shipmentdetails?id=<?= $shipmentList->id; ?>" target= "_NEW" class="">Shipment <?php echo ($index + 1); ?></a></li>
                                <?php endforeach; ?>								  	
                            </ul>                                           
                        </li>
                        <!--                            <li role="presentation" class="col-sx-3">
                                                        <a href="#printpackinglists" id="order-tab-2" role="tab" data-toggle="tab" aria-expanded="true">Print Packing List</a>
                                                    </li>-->
                        <!--                            <li role="presentation" class="col-sx-3">
                                                        <a href="#shipments" id="order-tab-3" role="tab" data-toggle="tab" aria-expanded="true">Shipments</a>
                                                    </li>-->
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="shippingitems" aria-labelledby="shipping-items-tab" style="box-shadow: none;">  
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
                            <!--<div class="x_panel">-->

                                <!--<div class="x_title">-->                                                            
                                    <!--                        <div class="col-md-12 vcenter" style="font-weight:bold;">
                                                                Order Type: <?php //echo Ordertype::findOne($model->ordertype)->name;  ?> <span style="color: #508caa;">SO# : <?php //echo $model->number_generated;  ?></span> Delivery Method :<?php //echo $_delivery_method;  ?> <span style="color: #508caa;"><? $highstatus; ?></span>
                                                            </div>-->
                                    <!--<div class="clearfix"></div>-->
                                    <!--<div class="col-md-6 vcenter text-right"></div>-->
                                <!--</div>-->
                                <!--<div class="x_content">-->
                                    <?=
                                    GridView::widget([
                                        'dataProvider' => $dataProvider,
                                        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
                                        'summary' => '',
                                        'showHeader' => false,
                                        'columns' => [
                                            [
                                                'attribute' => 'model',
                                                'format' => 'raw',
                                                'contentOptions' => ['style' => "background: white; padding: 0;"],
                                                'value' => function ($model) {
                                                    $_model = Models::findOne($model['model']);
                                                    $_manufacturer = Manufacturer::findOne($_model['manufacturer']);
                                                    $customer = Customer::findOne($model['customer']);
                                                    $nb_ship_model = Item::find()->where(['ordernumber' => $model['ordernumber'], 'model' => $model['model'], 'status' => array_keys(Item::$shippingallstatus)])->count();
                                                    $nb_inshipping_model = Item::find()->where(['ordernumber' => $model['ordernumber'], 'model' => $model['model'], 'status' => array_keys(Item::$shippingstatus)])->count();
                                                    $nb_ship_model_printed = Item::find()->where(['ordernumber' => $model['ordernumber'], 'model' => $model['model'], 'status' => array_keys(Item::$shippingstatus), 'labelprinted' => 1])->count();
                                                    $_nb_ship_model_printed = Item::find()->where(['ordernumber' => $model['ordernumber'], 'model' => $model['model'], 'status' => array_keys(Item::$shippingallstatus), 'labelprinted' => 1])->count();
                                                    $_print_label_output = ($customer->requirelabelbox) ? Html::a('<span class="glyphicon glyphicon-print"></span> Print Labels', ['printalllabel', 'order' => $model['ordernumber'], 'model' => $_model->id], ['class' => ($nb_ship_model !== $_nb_ship_model_printed) ? 'btn btn-xs btn-info' : 'btn btn-xs btn-success']) : '';
//									$_box_config_button = (strpos(strtolower($__shipping_method), 'freight') === false) ? Html::a(($has_box_configuration) ? '<span class="glyphicon glyphicon-edit"></span> Weight & Dimensions' : '<span class="glyphicon glyphicon-plus"></span> Weight & Dimensions', 'javascript:;', ['class' => ($has_box_configuration) ? 'btn btn-xs btn-warning' : 'btn btn-xs btn-info', 'onClick'=>(!$has_box_configuration) ? 'openBoxConfigModal('. $model->ordernumber .', '. $_model->id .', 1)' : 'openBoxConfigModal('. $model->ordernumber .', '. $_model->id .', 2)']) : '';
                                                    $_readytoship_button = ($nb_inshipping_model == 0) ? Html::a('<span class="glyphicon glyphicon-ok-sign"></span> Ready To Ship', 'javascript:;', ['class' => 'btn btn-xs btn-success ready_to_ship']) : Html::a('<span class="glyphicon glyphicon-ok-sign"></span> Ready To Ship', ['readytoshipmodel', 'orderid' => $model['ordernumber'], 'modelid' => $model['model']], ['class' => 'btn btn-xs btn-info ready_to_shipment']);
                                                    return '<div class="x_panel" style="border: none"><div class="x_title"><div style="padding: 0;line-height:40px;font-size: 18px;" class="col-md-6 vcenter"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $_model->id . '">(' . $nb_ship_model . ') <b>' . $_manufacturer->name . ' ' . $_model->descrip . '</b></a></div><div class="col-md-6 vcenter text-right">' . $_print_label_output . ' ' . $_box_config_button . ' ' . $_readytoship_button .'</div><div class="clearfix"></div></div>
									<div class="x_content"><div class="model-loaded-content panel-collapse collapse out" mid="' . $_model->id . '" oid="' . $model['ordernumber'] . '" id="collapse' . $_model->id . '">
										<div class="panel-body" style="padding: 0"></div>
									</div></div></div></div>';
                                                }
                                                    ]
                                                ],
                                            ]);
                                            ?>
                                        <!--</div>-->
                                    <!--</div>-->

                                </div>
                                <!--                        <div role="tabpanel" class="tab-pane fade" id="printpackinglists" aria-labelledby="printpacking-lists-tab">  
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="shipments" aria-labelledby="shipments-tab">  
                                                        </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/shipping.js"></script>