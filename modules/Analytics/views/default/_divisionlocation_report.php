<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\Medias;
use app\models\Item;
use app\models\Location;
use app\models\LocationParent;
use yii\helpers\ArrayHelper;

$div_store_locations = ArrayHelper::getColumn(Location::find()->where(['customer_id' => $customer->id, 'storenum' => 'DIV'])->asArray()->all(), 'id');
?>
<?php if($isDivisionOnly): ?>
<?php
    $download_excel = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download Spreadsheet</div><div class="hide-desktop"><i class="fa fa-file-excel-o" aria-hidden="true"></i></div>';
    $download_pdf = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download PDF</div><div class="hide-desktop"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>';
?>
    <div class="col-md-5 vcenter col-xs-8" style="line-height: 30px; font-size: 15px;">
        <b><?php echo (!empty($location)) ? LocationParent::findOne($location->parent_id)->parent_name : 'Uncategorized'; ?></b>
    </div>
    <div class="col-md-7 vcenter text-right col-xs-4" style="margin-top:3px;"> 
        <?php $myLocId = (!empty($location)) ? $location->parent_id : ''; ?>
        <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=division&parentid=' . $myLocId, ['class' => 'btn btn-success download-pdf-link', 'style' => 'padding: 0px 5px; font-size: 12px; line-height: 21px; margin: 0px 0px;', 'target' => '_BLANK']); ?>
        <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=division&parentid=' . $myLocId, ['class' => 'btn btn-success download-excel-link', 'style' => 'padding:0px 5px; font-size: 12px; line-height: 21px; margin: 0px 0px;']); ?>
    </div>
<?php endif; ?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'rowOptions' => function ($model, $index, $widget, $grid) {
        return ['id' => 'row-models-' . $model['id']];
    },
            'columns' => [
                [
                    'attribute' => 'imagepath',
                    'label' => 'Thumbnail',
                    'format' => 'raw',
                    'value' => function($model) {
                        $picture = Medias::findOne($model['image_id']);
                        if ($picture !== null)
                            return Html::img(Yii::getAlias('@web') . '/public/images/models/' . $picture->filename, ['alt' => 'logo', 'onClick' => 'ModelsViewer(' . $model['id'] . ');', 'style' => 'max-width: 90px;max-height: 35px;']);
                    }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Description',
                            'value' => function($model) use ($customer) {
                                //return '<div style="line-height: 40px;font-weight:bold;">' . Html::a($model['description'], ['/customers/statuslog', 'model' => $model['id'], 'customer' => $customer->id], []) . '</div>';
                                return '<div style="line-height: 40px">' . $model['name'] . ' ' . $model['descrip'] . '</div>';
                            }
                        ],
                        [
                            'format' => 'raw',
                            'contentOptions' => ['style' => "text-align: center,"],
                            'label' => 'Qty At Division',
                            'value' => function($model) use ($customer, $div_store_locations) {
                        //$_output = $model['instock_qty'];
                        $_output = $model['qty_division'];
                        $_low_stock_style = 'color: #333';
                        $_in_stock_style = 'font-weight: bold; color: #08c';
                        $_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;
                        $_url = Url::to(['/customers/statusdetails', 'status' => array_search('In Stock', Item::$status), 'customer' => $customer->id, 'model' => $model['id']]);
                        return '<a href="' . $_url . '" tabindex="0" class="btn btn-default" style="' . $_button_style . '">' . $_output . '</a>';
                    }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Qty On Location',
                            'contentOptions' => ['style' => "text-align: center,"],
                            'value' => function($model) use ($customer, $div_store_locations) {
                        //$_output = $model['inprogress_qty'];
                        $_output = $model['qty_location'];
                        $_low_stock_style = 'color: #333';
                        $_in_stock_style = 'font-weight: bold; color: #08c';
                        $_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;
                        $_url = Url::to(['/customers/statusdetails', 'status' => array_search('In Progress', Item::$status), 'customer' => $customer->id, 'model' => $model['id']]);
                        return '<a href="' . $_url . '" tabindex="0" class="btn btn-default" style="' . $_button_style . '">' . $_output . '</a>';
                    }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Confirmed Qty',
                            'contentOptions' => ['style' => "text-align: center,"],
                            'value' => function($model) use ($customer) {
                        $_output = $model['qty_confirmed'];
                        $_low_stock_style = 'color: #333';
                        $_in_stock_style = 'font-weight: bold; color: #08c';
                        $_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;
                        $_url = Url::to(['/customers/statusdetails', 'model' => $model['id'], 'customer' => $customer->id, 'status' => array_search('Shipped', Item::$status)]);
                        return '<a <a href="' . $_url . '" tabindex="0" class="btn btn-default" style="' . $_button_style . '">' . $_output . '</a>';
                    }
                        ],
                        [
                            'format' => 'raw',
                            'label' => 'Total',
                            'contentOptions' => ['style' => "text-align: center,"],
                            'value' => function($model) use ($customer, $div_store_locations) {
//                                                    $_output = $model['total'];
                        $instockqty = $model['qty_division'];
                        $inprogress_qty = $model['qty_location'];
                        $shipped_qty = $model['qty_confirmed'];
                        $total = $instockqty + $inprogress_qty + $shipped_qty;
                        $_output = $total;
                        $_low_stock_style = 'color: #333';
                        $_in_stock_style = 'font-weight: bold; color: #08c';
                        $_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;
                        return '<a tabindex="0" class="btn btn-default" style="' . $_button_style . '">' . $_output . '</a>';
                    }
                        ],
//                                                    [
//                                                        'format' => 'raw',
//                                                        'label' => 'Department',
//                                                        'contentOptions' => ['style' => "text-align: center;"],
//                                                        'value' => function($model) {
//                                                    $url = 'javascript:;';
//                                                    $options = [
//                                                        'title' => Yii::t('app', 'Edit Category'),
//                                                        'onClick' => 'editCategory(' . $model['id'] . ')',
//                                                        'class' => 'glyphicon glyphicon-edit',
//                                                        'style' => 'line-height:16px;'
//                                                    ];
//                                                    return '<div style="font-weight:bold;"><div id="ldepartment-' . $model['id'] . '">' . strtoupper($model['department']) . '</div><div>' . Html::a('', $url, $options) . ' <span id="lcategory-' . $model['id'] . '">' . ucfirst(strtolower($model['categoryname'])) . '</span></div></div>';
//                                                }
//                                                    ],
//                                                    [
//                                                        'class' => 'yii\grid\ActionColumn',
//                                                        'template' => '{receive}',
//                                                        'buttons' => [
//                                                            'receive' => function ($url, $model, $key) {
//                                                                //$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
//                                                                $url = 'javascript:;';
//                                                                $options = [
//                                                                    'title' => Yii::t('app', 'Receive'),
//                                                                    'id' => 'receive-btn-' . $model['id'],
//                                                                    'onClick' => 'receiveModels(' . $model['id'] . ')',
//                                                                    'class' => 'btn btn-info glyphicon glyphicon-tasks',
//                                                                ];
//                                                                return Html::a('', $url, $options);
//                                                            }
//                                                                ]
//                                                            ],
                    ],
                ]);
                