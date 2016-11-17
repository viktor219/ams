<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\ShippingCompany;
use app\models\Item;
use app\models\User;
use app\models\Itemlog;

class ShipmenttrackController extends Controller {

    public function init() {
        parent::init();
        Yii::$app->user->setIdentity(User::findOne(['id' => 2]));
    }

    public function actionIndex() {
        $sql = 'SELECT lv_items.id, lv_salesorders.ordertype, lv_items.ordernumber, lv_items.location, lv_shipments.id as shipmentid, lv_shipments.master_trackingnumber, lv_shipments.trackinglink FROM `lv_items` join lv_shipments on lv_shipments.orderid = lv_items.ordernumber join lv_salesorders on lv_salesorders.id = lv_items.ordernumber where lv_shipments.master_trackingnumber is NOT NULL and lv_shipments.master_trackingnumber != "" and (lv_items.status = :status_shipped || lv_items.status = :status_awaiting )';
        $datas = \Yii::$app->db->createCommand($sql)
                ->bindValue(':status_awaiting', array_search('Awaiting Return', Item::$status))
                ->bindValue(':status_shipped', array_search('Shipped', Item::$status))
                ->queryAll();
        foreach ($datas as $data) {
            if ($data['trackinglink']  && $data['trackinglink'] != 5) {
                $shippingCompany = ShippingCompany::findOne($data['trackinglink']);
            } else {
                $shippingCompany = ShippingCompany::findOne(1);
            }
//            $shippingCompany = ShippingCompany::findOne($data['trackinglink']);
            $t = New \RocketShipIt\Track($shippingCompany->name);
            $response = $t->track($data['master_trackingnumber']);
            if (isset($response['Data']['Packages'])) {
                $packages = $response['Data']['Packages'];
                foreach ($packages as $package) {
                    $isInTransit = false;
                    $isDelivered = false;
                    foreach ($package['Activity'] as $activity) {
                        if (isset($activity['StatusTypeCode']) && ($activity['StatusTypeCode'] == 'P' || $activity['StatusTypeCode'] == 'I')) {
                            $isInTransit = true;
                        } else if (isset($activity['StatusTypeCode']) && $activity['StatusTypeCode'] == 'D') {
                            $isInTransit = false;
                            $isDelivered = true;
                            break;
                        }
                    }
                    if ($isInTransit) {
                        $itemLog = Itemlog::find()->where(['itemid' => $data['id'], 'status' => array_search('In Transit', Item::$status)])->orderBy('created_at desc')->one();
                        if ($itemLog == NULL) {
                            $item = Item::findOne($data['id']);
                            $item->status = array_search('In Transit', Item::$status);
                            if ($item->save()) {
                                $itemLog = New Itemlog;
                                $itemLog->itemid = $item->id;
                                $itemLog->locationid = $data['location'];
                                $itemLog->shipment_id = $data['shipmentid'];
                                $itemLog->status = array_search('In Transit', Item::$status);
                                $itemLog->userid = 0;
                                $itemLog->created_at = date('Y-m-d H:i:s');
                                $itemLog->save();
                            }
                        }
                        break;
                    }
                    if($isDelivered){
                        $chStatus = array_search('Received', Item::$status);
                        if($data['ordertype'] != 2){
                            $chStatus = array_search('Delivered', Item::$status);
                        }
                        $itemLog = Itemlog::find()->where(['itemid' => $data['id'], 'status' => $chStatus])->orderBy('created_at desc')->one();
                        if ($itemLog == NULL) {
                            $item = Item::findOne($data['id']);
                            $item->status = $chStatus;
                            if ($item->save()) {
                                $itemLog = New Itemlog;
                                $itemLog->itemid = $item->id;
                                $itemLog->locationid = $data['location'];
                                $itemLog->shipment_id = $data['shipmentid'];
                                $itemLog->status = $chStatus;
                                $itemLog->userid = 0;
                                $itemLog->created_at = date('Y-m-d H:i:s');
                                $itemLog->save();
                            }
                        }
                        break;
                    }
                }
            }
        }
    }

}
