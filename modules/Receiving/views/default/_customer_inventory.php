<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Customer;
use app\models\Medias;
use app\modules\orders\models\Order;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Itemsordered;
use app\models\Item;

?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        [
            'attribute' => 'customer',
            'label' => Yii::t('app', 'Customer'),
            'contentOptions' => ['style' => 'width:200px;'],
            'format' => 'raw',
            'value' => function($model) {
        $customer = Customer::findOne($model->customer);

        $_my_media = Medias::findOne($customer->picture_id);

        if (!empty($_my_media->filename)) {
            $target_file = Yii::getAlias('@web') . '/public/images/customers/' . $_my_media->filename;
            if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/' . $_my_media->filename)) {

                return Html::img($target_file, ['alt' => $customer->companyname, 'class' => 'viewCustomer', 'style' => 'cursor:pointer;max-width:90px;max-height:35px;', 'cid' => $customer->id]);
            } else {
                return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
            }
        } else {

            return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
        }
    }
        ],
        [
            'attribute' => 'ordernumber',
            'label' => 'SO#',
            'value' => function($model) {
                return Order::findOne($model->ordernumber)->number_generated;
            }
        ],
        [
            'attribute' => 'ordernumber',
            'label' => 'PO#',
            'value' => function($model) {
                return Order::findOne($model->ordernumber)->customer_po;
            }
        ],
        [
            'attribute' => 'model',
            'label' => 'Description',
            'value' => function($model) {
                $_model = Models::findOne($model->model);
                $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                //$qty = Item::find()->where(['ordernumber'=>null, 'status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
                return $_manufacturer->name . ' ' . $_model->descrip;
            }
        ],
        [
            'header' => 'Qty',
            'format' => 'raw',
            'value' => function($model) {
                $ordernumber = $model->ordernumber;
                $items = Itemsordered::find()->where(['ordernumber' => $ordernumber])->all();
                $qty = 0;
                foreach ($items as $item) {
                    $qty += $item->qty;
                }
                $number_items = $qty;
                $items = Itemsordered::find()->where(['ordernumber' => $ordernumber])->all();
                $content = "";
                $qty = 0;
                foreach ($items as $item) {
                    $qty += $item->qty;
                    $_model = Models::findOne($item->model);
                    $manufacturer = Manufacturer::findOne($_model->manufacturer);
                    $count_model = Itemsordered::find()->where(['ordernumber' => $ordernumber, 'model' => $item->model])->one()->qty;
                    $name = $manufacturer->name . ' ' . $_model->descrip;
                    $findstatus = Item::find()->where(['ordernumber' => $ordernumber, 'model' => $item->model])->groupBy('status')->all();
                    $status = array();
                    foreach ($findstatus as $stat) {
                        $status[] = Item::$status[$stat->status];
                    }
                    $newline = "($count_model) $name " . "<span style=\"color:#08c;\">(<b>" . implode(', ', $status) . "</b>)</span>";
                    if ($name !== "" && strpos($content, $newline) === false)
                        $content .= $newline . "<br/>";
                }
                return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $ordernumber . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder=' . $ordernumber . '" role="button" data-toggle="popover" data-placement="left" data-html="true" data-animation="true" data-trigger="focus" title="Items (' . $number_items . ')" data-content="' . Html::encode($content) . '" rel="popover" style="color:#08c;">' . $number_items . '</a>';
            }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{receive} {update} {delete}',
                    'controller' => 'orders',
                    'contentOptions' => ['class' => 'action-buttons'],
                    'buttons' => [
                        'receive' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Receive',
                                'class' => 'btn btn-info',
                                'type' => 'button',
                                'pid' => $model->id,
                                'onClick' => 'ViewOrderDetails("' . $model->ordernumber . '");'
                            ];
                            $url = "javascript:;";

                            return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
                        },
                                'update' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Edit',
                                'class' => 'btn btn-warning',
                                'type' => 'button'
                            ];
                            $url = "javascript:;";

                            return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
                        },
                                'delete' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Delete',
                                'class' => 'btn btn-danger',
                                'data-content' => 'Delete Order',
                                'id' => 'soft_delete_inventory',
                                'type' => 'button',
                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                            ];
                            $url = \yii\helpers\Url::toRoute(['/receiving/delinventory', 'id' => $model->id]);

                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                        }
                            ],
                        ]
                    ],
                ]);
?> 