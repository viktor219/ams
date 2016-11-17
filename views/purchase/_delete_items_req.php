<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Item;
use app\models\Models;
use app\models\Manufacturer;

?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        /* [
          'attribute'=>'owner_id',
          'label'=>'Request By',
          'value'=> function($model) {
          return Users::findOne($model->owner_id)->firstname . ' ' . Users::findOne($model->owner_id)->lastname;
          }
          ], */
        [
            'attribute' => 'qty',
            'format' => 'raw',
            'label' => 'Quantity',
            'value' => function($model) {
                $number_items = Item::find()->where(['ordernumber' => $model->ordernumber, 'status' => 1, 'model' => $model->model])->count();
                return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getitemsrequestedindexdetails?&idorder=' . $model->ordernumber . '&itemid=' . $model->id . '" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items (' . $number_items . ')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>';
            }
                ],
                [
                    'attribute' => 'model',
                    'label' => 'Description',
                    'value' => function($model) {
                        //$item = Item::findOne($model->item);
                        $_model = Models::findOne($model->model);
                        $_man = Manufacturer::findOne($_model->manufacturer);
                        return $_man->name . ' ' . $_model->descrip;
                    }
                ],
                [//sum of all of that item on all sales orders for the past 90 days
                    'label' => 'Total Recently Sold',
                    'value' => function($model) {
                        $now = date('Y-m-d');
                        $MonthsAgo = date("Y-m-d", strtotime("-3 month"));
                        return Item::find()->where(['ordernumber' => $model->ordernumber, 'model' => $model->model])
                                        ->andWhere("created_at between '$MonthsAgo' and '$now'")
                                        ->count();
                    }
                        ],
                        [//sum of price from all purchase orders with that item in the last 90 days divided by the quantity of purchase orders with that item in the last 90 days
                            'label' => 'Average Cost',
                            'value' => function($model) {
                                $now = date('Y-m-d');
                                $MonthsAgo = date("Y-m-d", strtotime("-3 month"));
                                $sumprice = (new \yii\db\Query())->from('{{%itemspurchased}}')
                                        ->where(['model' => $model->model])
                                        ->andWhere("created_at between '$MonthsAgo' and '$now'")
                                        ->sum('price');
                                $sumqty = (new \yii\db\Query())->from('{{%itemspurchased}}')
                                        ->where(['model' => $model->model])
                                        ->andWhere("created_at between '$MonthsAgo' and '$now'")
                                        ->sum('qty');

                                return ($sumqty != 0) ? number_format(($sumprice / $sumqty), 2) : '-';
                            }
                                ],
                                /* [
                                  'label'=>'Preferred Vendor',
                                  'value'=> function($model) {
                                  //$item = Item::findOne($model->item);
                                  $_model = Models::findOne($model->model);
                                  return $_model->prefered_vendor;
                                  }
                                  ],
                                  [
                                  'label'=>'Order Number',
                                  'value'=> function($model) {
                                  $order = Order::findOne($model->ordernumber);
                                  return $order->number_generated;
                                  }
                                  ],
                                  [
                                  'label'=>'Customer',
                                  'value'=> function($model) {
                                  $order = Order::findOne($model->ordernumber);
                                  return Customer::findOne($order->customer_id)->companyname;
                                  }
                                  ], */
                                [
                                    'attribute' => 'created_at',
                                    'label' => 'Requested Date',
                                    'value' => function($model) {
                                        return date('m/d/Y', strtotime($model->created_at));
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{revert} {delete}',
                                    'contentOptions' => ['style' => 'width:180px;', 'class' => 'action-buttons'],
                                    'controller' => 'orders',
                                    'buttons' => [
                                        'revert' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'Revert',
                                                'class' => 'btn btn-info revertItems',
                                            ];
                                            $url = \yii\helpers\Url::toRoute(['/purchase/revertitems', 'id' => $model->id]);

                                            return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
                                        },
                                                'delete' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'Delete',
                                                'class' => 'btn btn-danger',
                                                'id' => 'soft_delete_purchase_item',
                                                'data-content' => 'Delete Order',
                                                'type' => 'button',
                                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                                            ];
                                            $url = Url::toRoute(['/purchasing/delitems', 'id' => $model->id]);
                                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                                        }
                                            ],
                                        ]
                                    ],
                                ]);
?>   