<?php

use \app\models\Item;
use \yii\widgets\ActiveForm;
?>
<?php
$index = 1;
$form = ActiveForm::begin(['method' => 'post', 'action' => ['/inventory/transferloc'], 'id' => 'transfer-inventory-form']);
echo yii\helpers\Html::hiddenInput('model', $model->id);
foreach ($items as $item):
    $items = Item::find()->where(['location' => $item->location, 'status' => array_search('In Stock', Item::$status), 'model' => $model->id])->groupBy('customer');
    $loc = \app\models\Location::findOne($item->location);
    $loc_label = '';
    if (!empty($loc->storenum))
        $loc_label = $loc->storenum;
    if (!empty($loc->storename))
        $loc_label .= $loc->storename.' ';
    $loc_label .= $loc->address . " " . $loc->address2 . " " . $loc->city . " " . $loc->state . " " . $loc->zipcode;
    ?> 
    <div class="x_panel">
        <div class="x_title">
            <h2><?= $loc_label; ?></h2>
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
        <div class="x_content">
            <?php
            foreach ($items->all() as $item):
                $customer = app\models\Customer::findOne($item->customer);
                $qty = Item::find()->where(['model' => $model->id, 'status' => array_search('In Stock', Item::$status), 'location' => $item->location, 'customer' => $item->customer])->count();
                ?>
            <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <?= yii\bootstrap\Html::dropDownList('location[]', $item->location, $locationList, ['class' => 'select_loc item_select2_single form-control inputs', 'prompt' => '-Select Location-']); ?>
                        </div>
                        <div class="col-md-6">
                            <?= yii\bootstrap\Html::hiddenInput('customer[]', $customer->id, ['class' => 'transfer_customer_id']); ?>
                            <?= yii\bootstrap\Html::textInput('customer_company[]', $customer->companyname, ['class' => 'select_cust item_select2_single form-control inputs transfer_customer_name', 'prompt' => '-Select Customer-']); ?>
                        </div>
                        <div class="col-md-2">
                            <?= yii\helpers\Html::textInput('quantity[]', $qty, ['class' => 'inputs form-control transfer_quantity', 'min' => 1, 'max' => $quantity, 'type' => 'number', 'id' => 'transfer-qty']); ?>
                        </div>
                    </div>
                </div>
                <!--                <div class="col-md-12" style="margin-top: 10px; margin-bottom: 5px;">
                                    <a class="btn btn-success pull-right invent_transfer"><span class="glyphicon glyphicon-transfer"></span> Transfer</a>
                                </div>-->
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
<?php ActiveForm::end(); ?>