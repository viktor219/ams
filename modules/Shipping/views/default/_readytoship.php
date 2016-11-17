<?php

use app\modules\Orders\models\Order;
use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Ordertype;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Medias;
use app\models\Item;
use app\models\Itemsordered;
use app\models\Customer;
use app\models\User;
?>

<?= GridView::widget([
            'dataProvider' => $dataProvider,
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
            'summary'=>'',
            'emptyText'=>'No orders available',
            'columns' => [
                [
                    'attribute' => 'customer_id',
                    'label' => 'Customer',
                    'format' => 'raw',
                    'value' => function($model) {
                        $order = Order::findOne($model->ordernumber);
                        $customer = Customer::findOne($order->customer_id);
                        $m=$customer->picture_id;
                        $picture = Medias::findOne($m);
                        $link_picture = Yii::getAlias('@web').'/public/images/customers/'.$picture['filename'];
                        if($picture!==null && file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$picture['filename']))
                            return Html::img($link_picture, ['alt'=>'logo', 'style'=>'cursor:pointer;max-width: 90px;max-height: 35px;', 'class'=>'showCustomer', 'uid'=>$order->customer_id]);
                        else
                            return '<div style="line-height:40px;">' . $customer->companyname . '</div>';
                        }
                ],
                [
                    'label' => 'Order Number',
                    'format' => 'raw',
                    'value' => function($model) {
                        $order_number = "";
                        $order = Order::findOne($model->ordernumber);
                        $order_number = $order->number_generated;

                        if(empty($order_number)) {
                            $location = Location::findOne($order->location_id);
                            if(!empty($location->storenum))
                                $order_number = "Store#: " . $location->storenum;
                            else
                                $order_number = $location->storename;
                        } else {
                            $order_number = $order->number_generated;
                        }
                        return '<div style="line-height:40px;">' . $order_number . '</div>';
                    }
                ],
                [
                    'label' => 'Order Type',
                    'format' => 'raw',
                    'value' => function($model) {
                        $order = Order::findOne($model->ordernumber);
                        return '<div style="line-height:40px;font-weight:bold;">' . Ordertype::findOne($order->ordertype)->name . '</div>';
                    }
                ],
                [
                    'attribute' => 'ready',
                    'format' => 'raw',
                    'value' => function($model) {
                        $order = Order::findOne($model->ordernumber);
                        $number_items = Itemsordered::find()->where(['ordernumber'=>$order['id']])->sum('qty');
                        $numbers_items_readytoship = Item::find()->where(['status'=>array_keys(Item::$shippingstatus)])->count();
                        $readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
                        $readypercentage = round($readypercentage, 2);
                        return '<div style="line-height:40px;">' . $readypercentage . '%</div>';
                    }
                ],
                [
                    'header' => 'Total Quantity',
                    'format' => 'raw',
                    'value' => function($model) {
                            $order = Order::findOne($model->ordernumber);
                            $number_items = Itemsordered::find()->where(['ordernumber'=>$order['id']])->sum('qty');
                            $items = Itemsordered::find()->where(['ordernumber'=>$order['id']])->all();
                            $content = "";
                            $qty = 0;
                            foreach($items as $item)
                            {
                                $qty += $item->qty;
                                $_model = Models::findOne($item->model);
                                $manufacturer = Manufacturer::findOne($_model->manufacturer);
                                $count_model = Itemsordered::find()->where(['ordernumber'=>$order['id'], 'model'=>$item->model])->one()->qty;
                                $name = $manufacturer->name . ' ' . $_model->descrip;
                                $findstatus = Item::find()->where(['ordernumber'=>$order['id'], 'model'=>$item->model])->groupBy('status')->all();
                                $status = array();
                                foreach($findstatus as $stat)
                                {
                                    $status[] = Item::$status[$stat->status];
                                }
                                $newline = "($count_model) $name " . "<span style=\"color:#08c;\">(<b>" . implode(', ', $status) . "</b>)</span>";
                                if($name!=="" && strpos($content, $newline) === false)
                                    $content .= $newline . "<br/>";
                            }
                            return '<a tabindex="0" class="btn btn-sm btn-default popup-marker" id="item-popover_' . $order['id'] . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$order['id'].'" role="button" data-placement="left" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . Html::encode($content) . '" rel="popover" style="color:#08c;">' . $number_items . '</a>';
                    }
                ],
                [
                    'attribute' => 'shipby',
                    'format' => 'raw',
                    'label' => 'Ship By',
                    'value' => function($model) {
                        $order = Order::findOne($model->ordernumber);
                        if($order->shipby){
                            $shipby = strtotime($order->shipby);
                            return '<div style="line-height:40px;">' . date('m/d/Y', $shipby) . '</div>';
                        }
                    }
                ],
            ],
        ]); ?>