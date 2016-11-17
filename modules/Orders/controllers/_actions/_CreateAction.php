<?php
/**
 * Creates a new Order model.
 * If creation is successful, the browser will be redirected to the 'view' page.
 * @return mixed
 */

namespace app\modules\Orders\controllers\_actions;

use yii\base\Action;
use Yii;
use app\modules\Orders\models\Order;
use app\models\QOrder;
use app\models\QItemsordered;
use app\models\ItemHasOption;
use app\models\QItemHasOption;
use app\models\Orderlog;
use app\models\QOrderlog;
use app\models\Ordertype;
use app\models\Location;
use app\models\ModelAssembly;
use app\models\Item;
use app\models\Itemlog;
use app\models\Itemsordered;
use app\models\Models;
use app\models\Customer;
use app\models\Partnumber;
use yii\web\NotFoundHttpException;
use app\models\SystemSetting;
use app\models\Shipment;
use app\models\QShipment;
use app\models\ShipmentType;

class CreateAction extends Action
{
    public function run()
    {
        ini_set('max_execution_time', 60);
        //ini_set('memory_limit', '512M');

        $data = Yii::$app->request->post();

        $isquoteorder = false;

        $session = Yii::$app->session;

        if (isset($data['saveQuote'])) {
            $model = new QOrder();
            $isquoteorder = true;
        } else
            $model = new Order();

        $locations = Location::find()->all();

        if (!empty($data)) {
            // var_dump($data);
            // exit(1);
            $purchasetype = (int)$data['purchasetype'];
            $customer = Customer::findOne($data['customerId']);
            if ($purchasetype != 1)
                $_instock_customer = $customer->id;
            else
                $_instock_customer = 4;
            $shipby = date('Y-m-d', strtotime($data['shipby']));
            $number_generated = $this->generateNumber($customer, $purchasetype, $shipby);
            $datetime = date('Y-m-d H:i:s');
            $successorder = false;
            $model->customer_id = $customer->id;
            $model->location_id = $data['location'];
            $model->type = ShipmentType::find()->where(['like', 'name', $data['shipmenttype']])->one()->id;
            $model->notes = $data['notes'];
            $model->ordertype = $data['purchasetype'];
            $model->shipby = $shipby;
            ///---- review
            $model->number_generated = $number_generated;
            $model->customer_po = $data['enduser'];
            $model->enduser_po = $data['customerorder'];
            $model->orderfile = base64_decode($session['__order_picture_id']);
            $model->created_at = $datetime;
            if ($model->save())
                $successorder = true;
            else
                $successorder = false;
            //log order
            if (isset($data['saveQuote']))
                $orderlog = new QOrderlog;
            else
                $orderlog = new Orderlog;
            $orderlog->orderid = $model->id;
            $orderlog->userid = Yii::$app->user->id;
            $orderlog->status = 1;
            $orderlog->save();
            //save shipments
            if (isset($data['saveQuote']))
                $shipment = new QShipment;
            else
                $shipment = new Shipment;
            $shipment->orderid = $model->id;
            $shipment->accountnumber = $data['accountnumber'];
            $shipment->shipping_deliverymethod = $data['shippingmethod'];
            $shipment->locationid = $data['location'];
            $shipment->trackinglink = $data['shippingcompany'];
            $shipment->save();
            $i = 1;
            foreach ($data['quantity'] as $key => $value) {
                if ($model->ordertype == 1 || $model->ordertype == 2) {
                    $_find_model = Models::findOne($data['modelid'][$key]);
                    $_data_model = $data['modelid'][$key];
                } else if ($model->ordertype == 3 || $model->ordertype == 4) {
                    $_find_model = Models::findOne($data['modelsid'][$key]);
                    $_data_model = $data['modelsid'][$key];
                }
                if ((!empty($_find_model) && !$_find_model->assembly)) //save simple items [not assembly]
                {
                    if (!empty($data['modelid'][$key]) || !empty($data['modelsid'][$key])) {
                        $price = 0;
                        $p_model = "";
                        if ($model->ordertype == 3 || $model->ordertype == 4)
                            $p_model = $data['modelsid'];
                        else
                            $p_model = $data['modelid'];
                        if (!empty($data['price'][$key]))
                            $price = $data['price'][$key];
                        $option_key = $i;
                        //
                        if (isset($data['saveOrder'])) //save in lv_items for normal Order only
                        {
                            $in_stock = Item::find()->where(['customer' => $_instock_customer, 'model' => $p_model[$key], 'status' => 4])->count();
                            if ($model->ordertype == 1 || $model->ordertype == 4 || ($model->ordertype == 3 && $customer->trackincomingserials == 1))//purchase, warehousing orders
                            {
                                $__qtyrequested = 0;
                                if ($value > $in_stock) {
                                    $__qtyrequested = $value - $in_stock;
                                }
                                if ($__qtyrequested > 0) {
                                    //
                                    for ($i = 0; $i < $__qtyrequested; $i++) {
                                        $item = new Item;
                                        $item->status = 1;
                                        $item->model = $p_model[$key];
                                        $item->ordernumber = $model->id;
                                        $item->customer = $model->customer_id;
                                        $item->location = $data['location'];
                                        $item->received = $datetime;
                                        $item->lastupdated = $datetime;
                                        $item->notes = $data["notes"];
                                        if ($item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = 1;
                                            $itemlog->itemid = $item->id;
                                            $itemlog->locationid = $item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $p_model[$key]])->all();
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    //exit(1);
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $p_model[$key]])->limit($value)->all();
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                }
                            } else if ($model->ordertype == 3 && $customer->trackincomingserials == 0) //integration order
                            {
                                $_model = Models::findOne($p_model[$key]);
                                if (Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count() > $value) {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->limit($value)->all();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->all();
                                    $qtytoinserted = $value - Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //
                                    for ($i = 0; $i < $qtytoinserted; $i++) {
                                        $item = new Item;
                                        $item->status = array_search('Requested', Item::$status);
                                        $item->model = $p_model[$key];
                                        $item->ordernumber = $model->id;
                                        $item->customer = 4;
                                        $item->location = $data['location'];
                                        $item->received = $datetime;
                                        $item->lastupdated = $datetime;
                                        $item->notes = $data["notes"];
                                        if ($item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Requested', Item::$status);
                                            $itemlog->itemid = $item->id;
                                            $itemlog->locationid = $item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                }
                                //}
                            } else if ($model->ordertype == 2) {
                                $_model = Models::findOne($p_model[$key]);
                                if (Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count() > $value) {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->limit($value)->all();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->all();
                                    $qtytoinserted = $value - Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //check if customerstoreinventory
                                    if ($customer->customerstoreinventory) {
                                        for ($i = 0; $i < $qtytoinserted; $i++) {
                                            $item = new Item;
                                            $item->status = array_search('Requested', Item::$status);
                                            $item->model = $p_model[$key];
                                            $item->ordernumber = $model->id;
                                            $item->customer = 4;
                                            $item->location = $data['location'];
                                            $item->received = $datetime;
                                            $item->lastupdated = $datetime;
                                            $item->notes = $data["notes"];
                                            if ($item->save()) {
                                                //track item
                                                $itemlog = new Itemlog;
                                                $itemlog->userid = Yii::$app->user->id;
                                                $itemlog->status = array_search('Requested', Item::$status);
                                                $itemlog->itemid = $item->id;
                                                $itemlog->locationid = $item->location;
                                                $itemlog->save();
                                                $successorder = true;
                                            }
                                        }

                                    } else {
                                        for ($i = 0; $i < $qtytoinserted; $i++) {
                                            $item = new Item;
                                            $item->status = array_search('In Transit', Item::$status);
                                            $item->model = $p_model[$key];
                                            $item->ordernumber = $model->id;
                                            $item->customer = $model->customer_id;
                                            $item->location = $data['location'];
                                            $item->received = $datetime;
                                            $item->lastupdated = $datetime;
                                            $item->notes = $data["notes"];
                                            if ($item->save()) {
                                                //track item
                                                $itemlog = new Itemlog;
                                                $itemlog->userid = Yii::$app->user->id;
                                                $itemlog->status = array_search('In Transit', Item::$status);
                                                $itemlog->itemid = $item->id;
                                                $itemlog->locationid = $item->location;
                                                $itemlog->save();
                                                $successorder = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //for quote or not quote orders...
                        if (isset($data['saveQuote']))
                            $order = new QItemsordered;
                        else
                            $order = new Itemsordered;
                        $order->customer = $model->customer_id;
                        $order->qty = $value;
                        $order->price = str_replace(',', '', $price);
                        $order->package_optionid = $data['package_option'][$option_key][0];
                        $order->model = $p_model[$key];
                        $order->ordernumber = $model->id;
                        $order->timestamp = $datetime;
                        $order->status = $status;
                        $order->notes = $data['itemnotes'][$key];
                        $order->ordertype = $data['purchasetype'];
                        if ($order->save()) {
                            if ($data['purchasetype'] == 1 || $data['purchasetype'] == 2) {
                                //save partnumbers
                                $partnumber = Partnumber::find()->where(['customer' => $model->customer_id, 'model' => $p_model[$key]])->one();
                                if ($partnumber === null)
                                    $partnumber = new Partnumber;
                                //update pricing
                                if ($data['purchasetype'] == 1)
                                    $partnumber->purchasepricing = $price;
                                else if ($data['purchasetype'] == 2)
                                    $partnumber->repairpricing = $price;
                                if ($partnumber === null) {
                                    $partnumber->customer = $model->customer_id;
                                    $partnumber->model = $p_model[$key];
                                }
                                $partnumber->save();
                            }
                            //save cleaning option
                            if (isset($data['cleaning_option'][$option_key])) {
                                foreach ($data['cleaning_option'][$option_key] as $value1) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $p_model[$key];
                                    $option->optionid = $value1;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            //save config option
                            if (isset($data['config_option'][$option_key])) {
                                foreach ($data['config_option'][$option_key] as $value2) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $p_model[$key];
                                    $option->optionid = $value2;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            //save testing option
                            if (isset($data['testing_option'][$option_key])) {
                                foreach ($data['testing_option'][$option_key] as $value3) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $p_model[$key];
                                    $option->optionid = $value3;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            $successorder = true;
                        } else
                            $successorder = false;
                    }
                } else //save assembly items
                {
                    $assembly_items = ModelAssembly::find()->where(['modelid' => $_data_model])->all();

                    foreach ($assembly_items as $assembly_item) {
                        $model_id = $assembly_item->partid;
                        $model_qty = $assembly_item->quantity;
                        $price = 0;
                        $p_model = "";
                        if (!empty($data['price'][$key]))
                            $price = $data['price'][$key];
                        $option_key = $i;
                        $in_stock = Item::find()->where(['model' => $model_id, 'status' => 4])->count();
                        //
                        if (isset($data['saveOrder'])) //save in lv_items for normal Order only
                        {
                            $in_stock = Item::find()->where(['model' => $model_id, 'status' => 4])->count();
                            if ($model->ordertype == 1 || $model->ordertype == 4 || ($model->ordertype == 3 && $customer->trackincomingserials == 1))//purchase, warehousing orders
                            {
                                $_assemblyQty = $model_qty * $value;
                                $__qtyrequested = 0;
                                if ($_assemblyQty > $in_stock) {
                                    $__qtyrequested = $_assemblyQty - $in_stock;
                                }
                                if ($__qtyrequested > 0) {

                                    for ($i = 0; $i < $__qtyrequested; $i++) {
                                        $item = new Item;
                                        $item->status = array_search('Requested', Item::$status);
                                        $item->model = $model_id;
                                        $item->ordernumber = $model->id;
                                        $item->customer = 4;
                                        $item->location = $data['location'];
                                        $item->received = $datetime;
                                        $item->lastupdated = $datetime;
                                        $item->notes = $data["notes"];
                                        if ($item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Requested', Item::$status);
                                            $itemlog->itemid = $item->id;
                                            $itemlog->locationid = $item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $model_id])->all();
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $model_id])->limit($_assemblyQty)->all();
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                }
                            } else if ($model->ordertype == 3 && $customer->trackincomingserials == 0) //integration order
                            {
                                $_model = Models::findOne($model_id);
                                if (Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count() > $_assemblyQty) {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->limit($_assemblyQty)->all();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->all();
                                    $qtytoinserted = $_assemblyQty - Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //
                                    for ($i = 0; $i < $qtytoinserted; $i++) {
                                        $item = new Item;
                                        $item->status = array_search('Requested', Item::$status);
                                        $item->model = $model_id;
                                        $item->ordernumber = $model->id;
                                        $item->customer = 4;
                                        $item->location = $data['location'];
                                        $item->received = $datetime;
                                        $item->lastupdated = $datetime;
                                        $item->notes = $data["notes"];
                                        if ($item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Requested', Item::$status);
                                            $itemlog->itemid = $item->id;
                                            $itemlog->locationid = $item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                }
                                //}
                            } else if ($model->ordertype == 2) {
                                $_model = Models::findOne($model_id);
                                if (Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count() > $_assemblyQty) {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->limit($_assemblyQty)->all();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                } else {
                                    $_instock_items = Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->all();
                                    $qtytoinserted = $_assemblyQty - Item::find()->where(['customer' => $_instock_customer, 'status' => array_search('In Stock', Item::$status), 'model' => $_model->id])->count();
                                    //
                                    foreach ($_instock_items as $_instock_item) {
                                        $_instock_item->status = array_search('Reserved', Item::$status);
                                        $_instock_item->ordernumber = $model->id;
                                        if ($_instock_item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('Reserved', Item::$status);
                                            $itemlog->itemid = $_instock_item->id;
                                            $itemlog->locationid = $_instock_item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                    //
                                    for ($i = 0; $i < $qtytoinserted; $i++) {
                                        $item = new Item;
                                        $item->status = array_search('In Transit', Item::$status);
                                        $item->model = $model_id;
                                        $item->ordernumber = $model->id;
                                        $item->customer = $model->customer_id;
                                        $item->location = $data['location'];
                                        $item->received = $datetime;
                                        $item->lastupdated = $datetime;
                                        $item->notes = $data["notes"];
                                        if ($item->save()) {
                                            //track item
                                            $itemlog = new Itemlog;
                                            $itemlog->userid = Yii::$app->user->id;
                                            $itemlog->status = array_search('In Transit', Item::$status);
                                            $itemlog->itemid = $item->id;
                                            $itemlog->locationid = $item->location;
                                            $itemlog->save();
                                            $successorder = true;
                                        }
                                    }
                                }
                            }
                        }
                        //
                        if (isset($data['saveQuote']))
                            $order = new QItemsordered;
                        else
                            $order = new Itemsordered;
                        $order->customer = $model->customer_id;
                        $order->qty = $_assemblyQty;
                        $order->price = str_replace(',', '', $price);
                        $order->package_optionid = $data['package_option'][$option_key][0];
                        //$order->model = array_search($data['description'][$key], $m);
                        $order->model = $model_id;
                        $order->ordernumber = $model->id;
                        $order->timestamp = $datetime;
                        //$order->status = $status;
                        $order->notes = $data['itemnotes'][$key];
                        $order->ordertype = $data['purchasetype'];
                        if ($order->save()) {
                            //save cleaning option
                            if (isset($data['cleaning_option'][$option_key])) {
                                foreach ($data['cleaning_option'][$option_key] as $value1) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $model_id;
                                    $option->optionid = $value1;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            //save config option
                            if (isset($data['config_option'][$option_key])) {
                                foreach ($data['config_option'][$option_key] as $value2) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $model_id;
                                    $option->optionid = $value2;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            //save testing option
                            if (isset($data['testing_option'][$option_key])) {
                                foreach ($data['testing_option'][$option_key] as $value3) {
                                    if (isset($data['saveQuote']))
                                        $option = new QItemHasOption;
                                    else
                                        $option = new ItemHasOption;
                                    $option->orderid = $model->id;
                                    $option->itemid = $model_id;
                                    $option->optionid = $value3;
                                    $option->ordertype = $data['purchasetype'];
                                    $option->save();
                                }
                            }
                            $successorder = true;
                        } else
                            $successorder = false;
                    }
                }
                $i++;
            }

            if ($successorder === true) {
                //clear session file order
                if (isset($session['__order_picture_id']))
                    unset($session['__order_picture_id']);

                if ($isquoteorder) {
                    $_message = 'Quote Order {' . $model->number_generated . '} has been created successfully!';
                    Yii::$app->getSession()->setFlash('warning', $_message);
                } else {
                    $_message = 'Order {' . $model->number_generated . '} has been created successfully!';
                    Yii::$app->getSession()->setFlash('success', $_message);
                }
            } else {
                $errors = json_encode($model->errors) . '<br/>' . json_encode($item->errors) . '<br/>' . json_encode($order->errors);
                $_message = $errors;
                Yii::$app->getSession()->setFlash('danger', $_message);
            }

            return $this->controller->redirect(Yii::$app->request->referrer);
        } else {
            return $this->controller->render('create', [
                'model' => $model,
                'locations' => $locations,
                'assetSetting' => SystemSetting::find()->one()
            ]);
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function generateNumber($customer, $purchasetype, $date = null)
    {
        $return = null;
        $generated = $this->generateUniqueStoreNum();
        $today = date('Y-m-d');
        //
        if ($purchasetype == 1 || $purchasetype == 3 || $purchasetype == 4)//purchase
        {
            $count = Order::find()->where(['customer_id' => $customer->id])
                ->andWhere(['<>', 'ordertype', 2])
                ->andWhere("date(created_at)= '$today'")->count();

            $count += 1;
            //var_dump($count);

            $ordernum = sprintf("%02d", $count);

            //$ordernum = ($ordernum=="00") ? "01" : $ordernum;
            $return = $customer->code . date('m') . date('d') . date('y') . $ordernum;
            $find = Order::find()->where(['number_generated' => $return])->count();
            if ($find > 0) {
                $_find_ordernum = str_replace($customer->code . date('m') . date('d') . date('y'), '', $return);
                $_find_ordernum = (int)$_find_ordernum;
                $_find_ordernum += 1;
                $_find_ordernum = sprintf("%02d", $_find_ordernum);
            }
            $return = ($find > 0) ? $customer->code . date('m') . date('d') . date('y') . $_find_ordernum : $return;
        } else if ($purchasetype == 2) {
            //verify code unicity
            $unique = $customer->code . '-' . $generated;
            $find = Order::find()->where(['number_generated' => $unique])->count();
            $return = ($find) ? $this->generateNumber($customer, $purchasetype, $date) : $unique;
        }

        /*$shipment = Order::findOne(['number_generated'=>$return]);
         //var_dump($shipment);exit(1);
        if(!empty($shipment))
            $return = $this->generateNumber($customer, $purchasetype, $date);*/

        return $return;
    }

    public function generateUniqueStoreNum()
    {
        $allowed_characters = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0);
        $number_of_digits = 6;
        $number_of_allowed_character = count($allowed_characters);
        $unique = "";
        for ($i = 1; $i <= $number_of_digits; $i++) {
            $unique .= $allowed_characters[rand(0, $number_of_allowed_character - 1)];
        }
        $unique = abs($unique);
        $gen_length = strlen($unique);
        $diff = $number_of_digits - $gen_length;
        if ($diff > 0) {
            $i = 1;
            while ($i <= $diff) {
                $unique .= rand(0, $number_of_allowed_character);
                $i++;
            }
        }
        return $unique;
    }
}