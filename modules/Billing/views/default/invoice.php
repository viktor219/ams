<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Invoice Section';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'label' => 'Billing'];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render("_modals/_sendmail");?>
<div class="row">
    <div class="col-md-12 col-sm-6 col-xs-12">
        <div class="row row-margin">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row vertical-align">
                        <div class="col-md-9 vcenter">
                            <h4>
                                <span class="glyphicon glyphicon-send"></span>
                                Billing Section: 
                            </h4>
                        </div>
                    </div>		
                </div>
                <div class="panel-body">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Invoices</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#">Settings 1</a>
                                            </li>
                                            <li><a href="#">Settings 2</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="billing-invoice-gridview">
                                <?=
                                GridView::widget([
                                    'dataProvider' => $invoiceDataProvider,
                                    'summary' => false,
                                    'columns' => [
                                        [
                                            'attribute' => 'id',
                                            'label' => '#',
                                        ],
                                        [
                                            'attribute' => 'invoicename',
                                        ],
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{generated} {view} {download} {email}',
                                            'contentOptions' => [ 'class' => 'action-buttons'],
                                            'buttons' => [
                                                'generated' => function ($url, $model, $key) {
                                                    //$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
                                                    $isDisabledClass = ($model->generated)?" disabled":"";
                                                    $url = 'javascript:void(0);';
                                                    if(!$isDisabledClass){
                                                        $url = Yii::$app->request->baseUrl.'/billing/generatepdf?id='.$model->id;
                                                    }
                                                    $options = [
                                                        'title' => Yii::t('app', 'Generate Pdf'),
                                                        //'id' => 'receive-btn-' . $model['id'],
                                                        //'onClick' => 'receiveModels(' . $model['id'] . ')',
                                                        'class' => 'btn btn-sm btn-success glyphicon glyphicon-list-alt'.$isDisabledClass,
                                                    ];
                                                    return Html::a('', $url, $options);
                                                },
                                                'view' => function ($url, $model, $key) {
                                                    //$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
                                                    $url = 'javascript:;';
                                                    $isDisabledClass = (!$model->generated)?" disabled":"";
                                                    if($model->generated){
                                                        $url = Yii::$app->request->baseUrl.'/public/temp/pdf/invoice/'.$model->invoicename.'.pdf';
                                                    }
                                                    $options = [
                                                        'title' => Yii::t('app', 'View Invoice'),
                                                        'target' => '_NEW',
                                                        //'id' => 'receive-btn-' . $model['id'],
                                                        //'onClick' => 'receiveModels(' . $model['id'] . ')',
                                                        'class' => 'btn btn-sm btn-info glyphicon glyphicon-eye-open'. $isDisabledClass,
                                                    ];
                                                    return Html::a('', $url, $options);
                                                },
                                                        'download' => function ($url, $model, $key) {
                                                    //$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
                                                    $url = 'javascript:;';
                                                    $isDisabledClass = (!$model->generated)?" disabled":"";
                                                    if($model->generated){
                                                        $url = Yii::$app->request->baseUrl.'/public/temp/pdf/invoice/'.$model->invoicename.'.pdf';
                                                    }
                                                    $options = [
                                                        'title' => Yii::t('app', 'Download Invoice'),
                                                        'download' => true,
                                                        //'id' => 'receive-btn-' . $model['id'],
                                                        //'onClick' => 'receiveModels(' . $model['id'] . ')',
                                                        'class' => 'btn btn-sm btn-default glyphicon glyphicon-download'. $isDisabledClass,
                                                    ];
                                                    return Html::a('', $url, $options);
                                                },
                                                'email' => function ($url, $model, $key) {
                                                    //$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
                                                    $url = 'javascript:;';
                                                    $isDisabledClass = (!$model->generated)?" disabled":"";
                                                    $options = [
                                                        'title' => Yii::t('app', 'Email Invoice'),
                                                        'onClick' => 'openMailer('.$model->id.', 4)',
                                                        //'id' => 'receive-btn-' . $model['id'],
                                                        //'onClick' => 'receiveModels(' . $model['id'] . ')',
                                                        'class' => 'btn btn-sm btn-primary glyphicon glyphicon-envelope email_invoice'. $isDisabledClass,
                                                    ];
                                                    return Html::a('', $url, $options);
                                                }
                                                    ]
                                                ],
                                            ],
                                        ]);
                                        ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Not Invoiced</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#">Settings 1</a>
                                            </li>
                                            <li><a href="#">Settings 2</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <?php if($countNotInvoiced): ?>
                                    <div class="pull-right">
                                        <a class="btn btn-success" href="<?php echo Yii::$app->request->baseUrl?>/billing/createinvoice?id=<?php echo $order_id; ?>"><span class="glyphicon glyphicon-plus" style="color: white"></span> Create</a>
                                    </div>
                                <?php endif; ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="billing-notinvoice-gridview">
                                <?=
                                GridView::widget([
                                    'dataProvider' => $notInvoicedDataProvider,
                                    'summary' => false,
                                    'columns' => [
//                                        [
//                                            'attribute' => 'id',
//                                            'label' => '#',
//                                        ],
                                        [
                                            'attribute' => 'invoicename',
                                            'label' => 'Shipment#',
                                            'format' => 'raw',
                                            'value' => function($model, $index, $widget, $grid){
                                                $order = \app\models\Item::findOne($model->itemid);
                                                return \app\models\Shipment::find()->where(['orderid' => $order->ordernumber])->andWhere('id <='.$model->shipmentid)->count();  
                                            }
                                        ],
                                        [
                                            'attribute' => 'quantity',
                                            'label' => 'Quantity',
                                            'format' => 'raw',
                                            'value' => function($model, $index, $widget, $grid){
                                                $quantity = \app\models\ShipmentsItems::find()->where(['shipmentid' => $model->shipmentid])->count();
                                                return '<a tabindex="0" href="javascript:void(0);" class="btn btn-default popup-marker" data-title="Quantity('.$quantity.')" id="qty-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getinvoicedetails?id='.$model->shipmentid.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" rel="popover" >'.$quantity.'</a>';
                                            }
                                        ],
                                        [
                                            'attribute' => 'cost',
                                            'label' => 'Cost',
                                            'format' => 'raw',
                                            'value' => function($model, $index, $widget, $grid){
                                                $shipment = \app\models\Shipment::findOne($model->shipmentid);
                                                $shipment_cost = (!$shipment->shipping_cost)?"00.00":number_format($shipment->shipping_cost,2);
                                                return "<b>$".$shipment_cost."</b>";
                                            }
                                        ],
                                        [
                                            'class' => 'yii\grid\ActionColumn',
                                            'template' => '{view}',
                                            'buttons' => [
                                                'view' => function ($url, $model, $key) {
                                                    $url = \yii\helpers\Url::toRoute(['/billing/viewshipment', 'id'=>$model->shipmentid]);
                                                    $options = [
                                                        'title' => Yii::t('app', 'View Shipment'),
                                                        'target' => '_BLANK',
                                                        //'id' => 'receive-btn-' . $model['id'],
                                                        //'onClick' => 'receiveModels(' . $model['id'] . ')',
                                                        'class' => 'btn btn-info glyphicon glyphicon-eye-open',
                                                    ];
                                                    return Html::a('', $url, $options);
                                                },
                                                    ]
                                                ],
                                            ],
                                        ]);
                                        ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/billing.js"></script>