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
                                $sql = "SELECT SUM( total ) / quantity as average
									FROM (
									
									SELECT SUM( price * qty ) AS total, sum( qty ) AS quantity
									FROM `lv_itemspurchased`
									INNER JOIN `lv_purchases` ON `lv_purchases`.`id` = `lv_itemspurchased`.`ordernumber`
									INNER JOIN `lv_items` ON `lv_items`.`purchaseordernumber` = `lv_itemspurchased`.`ordernumber`
									WHERE (
										`lv_itemspurchased`.`model` = :model
									)
									AND (
										`lv_itemspurchased`.created_at
										BETWEEN :monthsago
										AND :now
									)
                                	AND (
                                		`lv_items`.status = :status
                                	)
									GROUP BY `purchaseordernumber`
									)c";
                                //
	                                $average = Yii::$app->db->createCommand($sql)
		                                ->bindValue(':model', $model->model)
		                                ->bindValue(':monthsago', $MonthsAgo)
		                                ->bindValue(':status', array_search('In Transit', Item::$status))
		                                ->bindValue(':now', $now)
		                                ->queryScalar();

                                return number_format($average, 2);
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
                                    'template' => '{create} {receive} {delete}',
                                    'contentOptions' => ['style' => 'width:180px;', 'class' => 'action-buttons'],
                                    'controller' => 'orders',
                                    'buttons' => [
                                        'create' => function ($url, $model, $key) {
                                            $requestid = Item::find()->where(['model' => $model->model, 'ordernumber' => $model->ordernumber, 'customer' => $model->customer, 'created_at' => $model->created_at])->one()->id;
                                            $options = [
                                                'title' => 'Create PO',
                                                'class' => 'btn btn-info',
                                                'type' => 'button',
                                            ];
                                            $url = Url::toRoute(['/purchasing/create', 'request' => base64_encode($requestid)]);

                                            return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', $url, $options);
                                        },
                                                'update' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'Edit',
                                                'class' => 'btn btn-warning',
                                                'type' => 'button',
                                                'onClick' => 'EditItemPurchased("' . $model->id . '");'
                                            ];
                                            $url = "javascript:;";

                                            return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
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
                                            $url = Url::toRoute(['/itemspurchased/sdelete', 'id' => $model->id]);
                                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                                        },
                                        'receive' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'receive',
                                                'class' => 'btn btn-primary',
                                                'id' => 'receive_inventory',
                                                'data-content' => 'Recreive Inventory',
                                                'type' => 'button',
                                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                                            ];
                                            $url = Url::toRoute(['/purchasing/getreceivemodel', 'id' => $model->model, 'ordernumber' => $model->ordernumber]);
                                            return Html::a('<span class="glyphicon glyphicon-save" aria-hidden="true"></span>', $url, $options);
                                        }
                                            ],
                                        ]
                                    ],
                                ]);
?>    