<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Item;
use app\models\Vendor;

?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        'number_generated',
        [
            'attribute' => 'vendor_id',
            'label' => 'Vendor',
            'value' => function($model) {
                return Vendor::findOne($model->vendor_id)->vendorname;
            }
        ],
        [
            'label' => 'Qty',
            'format' => 'raw',
            'value' => function($model) {
                $number_items = Item::find()->where(['status' => array_search('In Transit', Item::$status), 'purchaseordernumber' => $model->id])->count();
                return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getpurchaseindexdetails?idpurchase=' . $model->id . '" role="button" data-toggle="popover" data-html="true" data-placement="left" data-animation="true" data-trigger="focus" title="Items (' . $number_items . ')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>';
            }
                ],
                [
                    'attribute' => 'estimated_time',
                    'value' => function($model) {
                        if ($model->estimated_time) {
                            return date('m/d/Y', strtotime($model->estimated_time));
                        }
                    }
                ],
                'trackingnumber',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{revert} {delete}',
                    //'contentOptions' => ['style' => 'width:320px;'],
                    'contentOptions' => ['class' => 'action-buttons'],
                    'controller' => 'orders',
                    'buttons' => [
                        'revert' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Revert',
                                'class' => 'btn btn-info revertPurchase',
                            ];
                            $url = \yii\helpers\Url::toRoute(['/purchase/revert', 'id' => $model->id]);

                            return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
                        },
                                'delete' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Delete',
                                'class' => 'btn btn-danger',
                                'data-content' => 'Delete Order',
                                'id' => 'soft_delete_item',
                                'type' => 'button',
                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                            ];
                            $url = Url::toRoute(['/purchasing/delpurchase', 'id' => $model->id]);
                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                        }
                            ],
                        ]
                    ],
                ]);
?>	