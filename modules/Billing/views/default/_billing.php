<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\models\Customer;
use app\models\Medias;
use app\models\Location;
use app\models\Ordertype;
use app\models\Itemsordered;
use app\models\Item;

?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'emptyText' => 'No orders available',
    'columns' => [
        [
            'attribute' => (Yii::$app->user->identity->usertype !== User::REPRESENTATIVE) ? 'customer_id' : '',
            'label' => (Yii::$app->user->identity->usertype !== User::REPRESENTATIVE) ? 'Customer' : 'Store Number',
            'format' => 'raw',
            'value' => function($model) {
                if (Yii::$app->user->identity->usertype === User::REPRESENTATIVE) {
                    $location = Location::findOne($model->location_id);
                    if (!empty($location->storenum))
                        $_output = $location->storenum;
                    else if (!empty($location->storename))
                        $_output = $location->storename;
                    else
                        $_output = Customer::findOne($model->customer_id)->companyname;
                }
                else {
                    $customer = Customer::findOne($model->customer_id);
                    $m = $customer->picture_id;
                    $picture = Medias::findOne($m);
                    $link_picture = Yii::getAlias('@web') . '/public/images/customers/' . $picture['filename'];
                    if ($picture !== null && file_exists(dirname(__FILE__) . '/../../../../public/images/customers/' . $picture['filename']))
                        $_output = Html::img($link_picture, ['alt' => 'logo', 'style' => 'cursor:pointer;max-width: 90px;max-height: 35px;', 'class' => 'showCustomer', 'uid' => $model->customer_id]);
                    else
                        $_output = $customer->companyname;
                }
                return '<div style="line-height:40px;">' . $_output . '</div>';
            }
                ],
                [
                    'label' => 'Order',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width:120px;'],
                    'attribute' => 'number_generated',
                    'value' => function($model) {
                $output = "";
                if (empty($model->number_generated)) {
                    $location = Location::findOne($model->location_id);
                    if (!empty($location->storenum))
                        $output .= "Store#: " . $location->storenum;
                    //if(!empty($location->storename))
                    else
                        $output .= $location->storename;
                    //
                    //$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;								
                } else
                    $output .= $model->number_generated;
                return '<div style="line-height:40px;">' . $output . '</div>';
            }
                ],
                //(Yii::$app->user->identity->usertype===User::REPRESENTATIVE) ? []: '',
                [
                    'label' => 'Order Type',
                    'attribute' => 'ordertype',
                    'format' => 'raw',
                    'visible' => function ($model) {
                        if (Yii::$app->user->identity->usertype === User::REPRESENTATIVE) {
                            return true;
                        } else {
                            return false;
                        }
                    },
                    'value' => function($model) {
                        $_ordertype = OrderType::findOne($model->ordertype);
                        if ($_ordertype !== null)
                            $_output = $_ordertype->name;
                        else
                            $_output = '-';
                        return '<div style="line-height:40px;">' . $_output . '</div>';
                    }
                ],
                [
                    'header' => 'Qty',
                    'format' => 'raw',
                    'value' => function($model) {
                        $number_items = Item::find()->where(['ordernumber' => $model->id, 'status' => array_search('Shipped', Item::$status)])->count();
                        //return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$model->id.'" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" rel="popover" style="color:#08c;">' . $number_items . '</a>';							
                        return '<a tabindex="0" class="btn btn-default" style="color:#08c;">' . $number_items . '</a>';
                    }
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => 'Created',
                            'format' => 'raw',
                            'value' => function($model) {
                                if (!empty($model->created_at) && $model->created_at != '0000-00-00 00:00:00')
                                    $created_at = date('m/d/Y', strtotime($model->created_at));
                                else
                                    $created_at = '-';
                                return '<div style="line-height:40px;">' . $created_at . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function($model) {
                                $highstatus = Item::find()->where(['ordernumber' => $model->id])->orderBy('status DESC')->one()->status;
                                $sql = 'select if( (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer) != 0, ((SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer and status =:status) * 100) / (SELECT count(*) as totalItems FROM `lv_items` where ordernumber = :customer),0) completePercent';
                                $completeItems = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':customer', $model->id)
                                        ->bindValue(':status', $highstatus)
                                        ->queryAll();
                                $completepercentage = (float) $completeItems[0]['completePercent'];
//							$completepercentage =  ($totalitems != 0) ? (($completeitems * 100) / $totalitems) : 0;
                                //round percentages 
                                if ((float) $completepercentage !== floor($completepercentage))
                                    $completepercentage = round($completepercentage);
                                $output = '<a tabindex="0" class="btn btn-default popup-marker btn-status" data-placement="left" id="qty-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexstatus?idorder=' . $model->id . '" data-content="" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="' . $completepercentage . '% ' . Item::$status[$highstatus] . '" rel="popover" style="color:#08c;">' . $completepercentage . '% ' . Item::$status[$highstatus] . '</a>';
                                return '<div style="line-height:40px;">' . $output . '</div>';
                            }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{view}',
                                    'contentOptions' => [ 'class' => 'action-buttons'],
                                    'buttons' => [
                                        'view' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'View',
                                                'class' => 'btn btn-info',
                                            ];
                                            $url = \yii\helpers\Url::toRoute(['/billing/invoice', 'id'=>$model->id]);
                                            //$url = 'javascript:;';
                                            return Html::a('<span class="glyphicon glyphicon-expand" aria-hidden="true"></span>', $url, $options);
                                        },
                                                'sendinvoice' => function ($url, $model, $key) {
                                            $options = [
                                                'title' => 'Send Invoice',
                                                'class' => 'btn btn-success',
                                                'target' => '_blank'
                                            ];
                                            //$url = \yii\helpers\Url::toRoute(['/orders/viewpicklist', 'id'=>$model->id]);
                                            $url = 'javascript:;';
                                            return Html::a('<span class="glyphicon glyphicon-send" aria-hidden="true"></span>', $url, $options);
                                        },
                                            ],
                                        ],
                                    ],
                                ]);
                                