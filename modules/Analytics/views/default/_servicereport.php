<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Itemlog;
use app\models\Item;
use app\models\LocationClassment;
use app\models\LocationParent;

?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => 'No Results to display'],
    'columns' => [
        [
            'attribute' => 'model',
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'max-width:275px; text-align:left'],
            'value' => function($model) {
                $_model = Models::findOne($model['model']);
                $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                return '<div style="line-height: 40px;">' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
            }
        ],
        [
            'attribute' => 'serial',
            'label' => 'Serial Number',
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'text-align:left'],
            'value' => function($model) {
                $_url = Html::a($model['serial'], ['/customers/serialdetails', 'id' => $model['id']], ['style' => 'color: #08c']);
                return '<div style="line-height: 40px;">' . $model->serial . '</div>';
            }
                ],
                                        [
                    'attribute' => 'location',
                    'label' => 'Store Number',
                    'headerOptions' => ['style' => 'text-align: left'],
                    'contentOptions' => ['style' => 'max-width:275px;text-align:left'],
                    'format' => 'raw',
                    'value' => function($model) use ($customer) {
                $location = Location::findOne($model['location']);
                $output = $location->storenum;
                //
                if($output=='DIV')
                {
                	$_location = LocationClassment::find()->where(['location_id'=>$location->id])->one();
                	$parent = LocationParent::findOne($_location->parent_id);
                	$output = $parent->parent_code . ' ' . $output;
                }                
                return '<div style="line-height: 40px;">' . $output . '</div>';
            }
                ],
                        [
            'attribute' => 'status',
            'label' => 'Status',
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'text-align:left'],
            'value' => function($model) {
                    $status = (!empty($model['status']))?Item::$status[$model['status']]:"";
                return '<div style="line-height: 40px;">' . $status . '</div>';
            }
                ],
                        [
            'attribute' => 'returntracking',
            'label' => 'Return Tracking',
            'format' => 'raw',
            'headerOptions' => ['style' => 'text-align: left'],
            'contentOptions' => ['style' => 'text-align:left'],
            'value' => function($model) {
                $_shipment = app\models\Shipment::find()->where(['orderid' => $model['ordernumber']])->one();
                $_company = \app\models\ShippingCompany::findOne($_shipment->trackinglink);
                $trackingLink = '';
                if(strtolower($_company->name) == 'ups'){
                    $trackingLink = 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums='.$_shipment->master_trackingnumber;
                } else if(strtolower($_company->name) == 'fedex'){
                    $trackingLink = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers='.$_shipment->master_trackingnumber;
                } else if(strtolower($_company->name) == 'dhls'){
                    $trackingLink = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB='.$_shipment->master_trackingnumber;
                } else if(strtolower($_company->name) == 'usps'){
                    $trackingLink = 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels='.$_shipment->master_trackingnumber;
                }
                return '<div style="line-height: 40px;"><a href="' . $trackingLink . '">'.$_shipment->master_trackingnumber.'</a></div>';
            }
                ],
                [
                    'label' => 'Tagnumber',
                    'attribute' => 'tagnum',
                    'value' => function($model){
                        return $model['tagnum'];
                    }
                ],
                [
                    'label' => 'Date Created',
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function($model) {
                    	$order = \app\modules\Orders\models\Order::findOne($model['ordernumber']);
                        return (!empty($order->created_at) && $order->created_at != "0000-00-00 00:00:00") ? date('m/d/y h:iA', strtotime($order->created_at)) : "-";
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'format' => 'raw',
                    'value' => function($model) {
                        $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
                        $connection = Yii::$app->getDb();
                        $command = $connection->createCommand($sql)
                                ->bindValue(':itemid', $model->id)
                                ->bindValue(':status', $model['status']);
                        $data = $command->queryOne();
                        if($data == NULL){
                            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and userid is NOT NULL and userid != 0 order by created_at desc';
                            $connection = Yii::$app->getDb();
                            $command = $connection->createCommand($sql)
                                    ->bindValue(':itemid', $model->id);
                            $data = $command->queryOne();
                        }                        
                        return $data['firstname'] . ' ' . $data['lastname'];
                    }
                ]
            ],
        ])
?>