<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\data\SqlDataProvider;
use app\models\Item;
use app\models\UserHasCustomer;
use app\models\Customer;
use app\modules\Orders\models\Order;
use app\models\Location;
use app\models\ReturnedlabelFile;
use app\models\Shipment;
use app\models\ShipmentMethod;
use app\models\SalesorderWs;
use app\models\ShipmentBoxDetail;
use yii\helpers\ArrayHelper;
use app\models\ShipmentsItems;

class Common extends Component {

    const MONGO_DB_NAME = 'ams';
    const _labelreturn_PATH = "public/medias/labels/";
    const _temp_PDF_PATH = "public/temp/pdf/salesorder/";

    public function getMongoDb() {
        $m = new \MongoClient();
        $db = $m->selectDB(self::MONGO_DB_NAME);
        return $db;
    }

    public function getInventory($condition = array(), $sort = array('name' => 1, "descrip" => 1)) {
        $m = new \MongoClient();
        $db = $m->selectDB(self::MONGO_DB_NAME);
        $collection = new \MongoCollection($db, 'inventorymodels');
        $cursor = $collection->find($condition);
        $cursor->sort($sort);
        return iterator_to_array($cursor);
    }

    public function getDivisionDataProvider($location) {
        $custId = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($custId);
        $sql = "SELECT SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty,
                SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
        $sql .= "lv_models.id, 
                 lv_manufacturers.name,
                lv_models.descrip,
                lv_models.aei,
                lv_models.image_id,
                lv_departements.name as department,
                lv_categories.categoryname,
                SUM(storenum='DIV') as qty_division,
                SUM(storenum<>'DIV') as qty_location,
                SUM(confirmed=1) as qty_confirmed
                FROM lv_models 
                INNER JOIN lv_items 
                ON lv_models.id=lv_items.model
                INNER JOIN lv_categories
                ON `lv_models`.`category_id` = `lv_categories`.`id`
                LEFT JOIN lv_manufacturers
                ON lv_models.manufacturer=lv_manufacturers.id
                LEFT JOIN lv_departements
                ON lv_models.department=lv_departements.id
                INNER JOIN lv_locations
                ON lv_items.location=lv_locations.id";
        $sql .= (!empty($location)) ? " INNER JOIN lv_locations_classments
                    ON lv_items.location=lv_locations_classments.location_id
                    WHERE lv_items.customer=" . $customer->id . "
                    AND lv_locations_classments.parent_id=" . $location->parent_id . "" : " WHERE lv_items.customer=" . $customer->id . " AND location NOT IN (SELECT DISTINCT(location_id) FROM lv_locations_classments)";
        $sql .= " GROUP BY lv_items.model ORDER BY name, descrip";
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'pagination' => ['pageSize' => 100],
        ]);
        return $dataProvider;
    }

    public function generateLabel($id) {
        $_retArray = array();
        $errors = array();
        $order = Order::findOne($id);
        $location = Location::findOne($order->location_id);
        if (empty($order->number_generated)) {
            if (!empty($location->storenum))
                $name = "Store#" . $location->storenum;
            else
                $name = $location->storename;
            $name = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
        } else
            $name = $order->number_generated;
        if (empty(ReturnedlabelFile::find()->where(['orderid' => $order->id])->one())) {
            $_shipment = Shipment::find()->where(['orderid' => $order->id])->one();
            $_deliverymethod = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);
            $_shipment_request_api_company = 'UPS';

            if ($_deliverymethod->shipping_company_id === 1) {
                $ups = new \Ups\Entity\Service;
                $ups->setCode($_deliverymethod->_value);
                $__shipping_method = $ups->getName();
            } else if ($_deliverymethod->shipping_company_id === 3) { //Waiting DHL issues solved
            } else {
                $__shipping_method = $_deliverymethod->_value;
            }
            //dimensions settings
            $_length = '20';
            $_depth = '20';
            $_height = '10';
            $_weight = '5';

            $ispallet = (strpos(strtolower($__shipping_method), 'freight') !== false) ? true : false; //should be used later for pallet
            $_related_warehouse_id = SalesorderWs::find()->where(['service_id' => $order->id])->one()->warehouse_id;
            if (!empty($_related_warehouse_id))
                $_related_warehouse = Order::findOne($_related_warehouse_id);
            else {
                $_related_warehouse = $order;
                $_related_warehouse_id = $order->id;
            }

            $_warehouse_location = Location::findOne($_related_warehouse->location_id);

            $_warehouse_customer = Customer::findOne($_related_warehouse->customer_id);

            $_request_shipment = new \RocketShipIt\Shipment($_shipment_request_api_company);

            $_request_shipment->setParameter('returnCode', '9');
            $_request_shipment->setParameter('service', '03');
            //
            $_request_shipment->setParameter('toCompany', 'Asset Enterprises, Inc.');
            $_request_shipment->setParameter('toPhone', '8643318678');
            $_request_shipment->setParameter('toAddr1', '3431 N. Industrial Drive');
            $_request_shipment->setParameter('toCity', 'Simpsonville');
            $_request_shipment->setParameter('toState', 'SC');
            $_request_shipment->setParameter('toCode', '29681');
            $storename = $_warehouse_location->storename;
            $storenames = explode('-', $storename);
            $_storename = $storenames[0];
            if (empty($_storename))
                $_storename = $storename;
            if (strlen($_storename) > 38)
                $_storename = mb_strimwidth($_storename, 0, 38, ".");

            $_request_shipment->setParameter('fromName', $_warehouse_customer->companyname);
            if (!empty($_warehouse_location->storename)) {
                $_request_shipment->setParameter('fromAddr1', $_storename);
                $_request_shipment->setParameter('fromAddr2', $_warehouse_location->address);
            } else
                $_request_shipment->setParameter('fromAddr1', $_warehouse_location->address);
            $_request_shipment->setParameter('fromCity', $_warehouse_location->city);
            $_request_shipment->setParameter('fromState', $_warehouse_location->state);
            $_request_shipment->setParameter('fromCode', $_warehouse_location->zipcode);

            $_package = new \RocketShipIt\Package($_shipment_request_api_company);
            $_package->setParameter('length', $_length);
            $_package->setParameter('width', $_depth);
            $_package->setParameter('height', $_height);
            $_package->setParameter('weight', $_weight);

            $_request_shipment->addPackageToShipment($_package);

            $response = $_request_shipment->submitShipment();

            if (isset($response['trk_main'])) {
                $_shipment->shipping_cost = $response['charges'];
                $_shipment->master_trackingnumber = $response['trk_main'];
                $_shipment->trackinglink = $_deliverymethod->shipping_company_id;
                $_shipment->dateshipped = date('Y-m-d H:i:s');
                $_shipment->save(false);
                //
                foreach ($response['pkgs'] as $package) {
                    $this->generateLabelOrder($id, $package);
                    //
                    $shipmentBoxDetail = new ShipmentBoxDetail;
                    $shipmentBoxDetail->modelid = 0;
                    $shipmentBoxDetail->pallet_box_number = 1;
                    $shipmentBoxDetail->shipmentid = $_shipment->id;
                    $shipmentBoxDetail->label_image = $package['label_img'];
                    $shipmentBoxDetail->trackingnumber = $package['pkg_trk_num'];
                    $shipmentBoxDetail->label_html = $package['label_html'];
                    $shipmentBoxDetail->save();
                }
                $items = ArrayHelper::getColumn(Item::find()->where(['ordernumber' => $order->id, 'status' => array_search('Awaiting Return', Item::$status)])->asArray()->all(), 'id');
                foreach ($items as $item) {
                    $shipmentItemModel = ShipmentsItems::find()->where(['shipmentid' => $id, 'itemid' => $item])->one();
                    if ($shipmentItemModel == NULL) {
                        $shipmentItemModel = New ShipmentsItems;
                        $shipmentItemModel->itemid = $item;
                        $shipmentItemModel->shipmentid = $_shipment->id;
                        $shipmentItemModel->date_added = date('Y-m-d H:i:s');
                        $shipmentItemModel->save();
                    }
                }
            } else {
                //var_dump($response['error']);    
                $_message = isset($response['error']) ? $response['error'] : json_encode($response);
                $errors = array('error' => true, 'message' => $_message);
            }
        }
        //
        if (empty($errors)) {
            $label_files = ReturnedlabelFile::find()->where(['orderid' => $order->id])->all();

            //$html = $this->renderPartial('_labelreturn', ['model' => $order, 'files' => $label_files]);

            $_filepath = '/ams/' . self::_temp_PDF_PATH . $this->generateLabelOrder($id, $package, $extension = ".png", true);
            $_retArray = array('success' => true, 'id' => $id, 'title' => 'SO# ' . $name, 'filename' => $_filepath);
        } else
            $_retArray = $errors;
        return json_encode($_retArray);
    }

    private function generateLabelOrder($id, $package, $extension = ".png", $reprint = false) {
        $model = Order::findOne($id);

        /*
         * TODO : temporary destination path for label image generated
         */
        if ($reprint) {
            $_shipment = Shipment::find()->where(['orderid' => $id])->one();

            $shipmentBoxDetail = ShipmentBoxDetail::find()->where(['shipmentid' => $_shipment->id])->one();

            $labelname = $_shipment->master_trackingnumber;

            $filename = $labelname . $extension;

            $path = self::_labelreturn_PATH . $filename;

            $_encodedfile = base64_decode($shipmentBoxDetail->label_image);

            if (!file_exists(Yii::getAlias('@webroot') . '/' . $path))
                file_put_contents(Yii::getAlias('@webroot') . '/' . $path, $_encodedfile);
        } else {
            $path = self::_labelreturn_PATH . $package['pkg_trk_num'] . $extension;

            $_encodedfile = base64_decode($package['label_img']);

            file_put_contents(Yii::getAlias('@webroot') . '/' . $path, $_encodedfile);

            $labelname = $package['pkg_trk_num'];

            $extension = ".pdf";

            $returnedlabelfile = new ReturnedlabelFile;
            $returnedlabelfile->orderid = $model->id;
            $returnedlabelfile->filename = $package['pkg_trk_num'] . $extension;
            $returnedlabelfile->save();
        }
    }

}
