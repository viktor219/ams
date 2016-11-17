<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\ReportsSettings;
use app\models\ReportsLog;
use app\models\Customer;
use app\models\Item;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Location;
use app\models\Itemlog;
use app\models\Category;
use app\models\LocationParent;
use app\models\LocationClassment;
use app\models\UserHasCustomer;
use app\models\User;

class AutomatereportController extends Controller {

    const FILE_UPLOAD_PATH = "/public/temp/reports";
    
    public function actionIndex($message = 'hello world') {
        $baseUrl = \Yii::$app->getUrlManager()->createAbsoluteUrl('');
        $reports_settings_models = ReportsSettings::find()->all();
        $new_path = getcwd() . self::FILE_UPLOAD_PATH;
        if (!is_dir($new_path)) {
            mkdir($new_path, 0777, true);
        }
        foreach ($reports_settings_models as $reports_settings_model) {
            $type = $reports_settings_model->report_type_id;
            $userid = $reports_settings_model->userid;
            $user = User::findOne($userid);
            $reportOptionId = $reports_settings_model->report_option_id;
            $customer = UserHasCustomer::find()->where(['userid' => $userid])->one()->customerid;
            $customer_model = Customer::findOne($customer);
            //$reports_log = ReportsLog::find()->where(['report_type_id' => $type, 'customer_id' => $customer])->orderBy('date_sent desc')->one();
            $reports_log = ReportsLog::find()->where(['report_type_id' => $type, 'customer_id' => $userid])->orderBy('date_sent desc')->one();
            $sendMail = false;
            $date1 = date('Y-m-d');
            $date2 = date('Y-m-d', strtotime($reports_log->date_sent));
            $diff = abs(strtotime($date2) - strtotime($date1));
            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            $reportOption = '';
            if ($reportOptionId == 1) {
                if ($reports_log == NULL) {
                    $sendMail = true;
                } else if ($days >= 1 || $months >= 1 || $years >= 1) {
                    $sendMail = true;
                }
                $reportOption = 'Daily';
            } else if ($reportOptionId == 2) {
                if ($reports_log == NULL) {
                    $sendMail = true;
                } else if ($days >= 7 || $months >= 1 || $years >= 1) {
                    $sendMail = true;
                }
                $reportOption = 'Weekly';
            } else if ($reportOptionId == 3) {
                if ($reports_log == NULL) {
                    $sendMail = true;
                } else if ($days >= 30 || $months >= 1 || $years >= 1) {
                    $sendMail = true;
                }
                $reportOption = 'Monthly';
            }
            //var_dump($sendMail, $userid);
            if ($sendMail==true) {
                if ($type == 1 || $type == 2) {
                    $fileName = ($type == 2) ? 'Closed Service Reports': 'Open Service Reports';
                    $attachFilename = $this->_genServiceReport($fileName, $customer, ($type == 2) ? true : false);
                } else if ($type == 3) {
                    $fileName = 'Reallocation Reports';
                    $attachFilename = $this->_genReallocationReport($fileName, $customer);
                } else if ($type == 4) {
                    $fileName = 'Division Reports';
                    $attachFilename = $this->_genDivisionReport($fileName, $customer);
                } else if ($type == 5) {
                    $fileName = 'Inventory Reports';
                    $attachFilename = $this->_genInventoryReport($fileName, $customer);
                }
				//
                $downloadFileLink = \Yii::$app->getUrlManager()->createAbsoluteUrl($attachFilename);
                $body = $this->renderPartial("/site/_mailreport", ['downloadFileLink' => $downloadFileLink, 'model' => $customer_model, 'fileName' => $fileName, 'reportOption' => $reportOption, 'baseUrl' => $baseUrl]);
                Yii::$app->mailer->compose()
                        ->setFrom([Yii::$app->params['supportEmail'] => 'Asset Management System'])
                        //->setTo(['matt.ebersole@assetenterprises.com']) 
                       	->setTo($user->email)
                        ->setSubject($fileName.' for your account')
                        ->setHtmlBody($body)
                        //->attach(getcwd() . '/' . $attachFilename) 
                        ->send();
                //
                        //                if ($attachFilename != NULL) {
                        $reportLog = New ReportsLog;
                        $reportLog->report_type_id = $type;
                        //$reportLog->customer_id = $customer; //use user id instead customer id
                        $reportLog->customer_id = $user->id;
                        $reportLog->date_sent = date('Y-m-d H:i:s');
                        $reportLog->save();
                        //              }                
            }
        }
    }

    private function _genServiceReport($fileName, $customer_id, $isClose) {
//        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer_id);
        $query = Item::find()
        ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
        ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
        ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2, 'lv_salesorders.deleted'=>0])
        ->andWhere('status < ' . array_search('Shipped', Item::$status). ' OR status = '.array_search('Awaiting Return', Item::$status));
        if ($isClose) {
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2, 'lv_salesorders.deleted'=>0])
                    ->andWhere('status >= ' . array_search('Shipped', Item::$status). ' AND status <= '.array_search('Scrap', Item::$status));
        }

        $objPHPExcel = new \PHPExcel();
        $sheet = 0;
        $objPHPExcel->setActiveSheetIndex($sheet);
        $models = $query->all();
        $excelDatas = array();
        foreach ($models as $model) {
            $_model = Models::findOne($model->model);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
            $location = Location::findOne($model->location);
//            $output = '';
//            if (!empty($location->storenum))
//                $output .= "Store#: " . $location->storenum . " - ";
//            if (!empty($location->storename))
//                $output .= $location->storename . ' - ';
            $address = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            //$location = $customer->companyname . ' - ' . $address;
            if ($location != NULL) {
                $locationClassment = LocationClassment::find()->where(['location_id' => $location->id])->one();
            }
            $order = \app\modules\Orders\models\Order::findOne($model['ordernumber']);
            $dateCreated = (!empty($order->created_at) && $order->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($order->created_at)) : "-";
            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql)
                    ->bindValue(':itemid', $model->id)
                    ->bindValue(':status', $model['status']);
            $data = $command->queryOne();
            $shipment = \app\models\Shipment::find()->where(['orderid' => $model['ordernumber']])->one();
            $createdBy = $data['firstname'] . ' ' . $data['lastname'];
            $store_number = (!empty($location->storenum)) ? $location->storenum:"";
            $name = $location->storename;
            $division = (isset($locationClassment) && $locationClassment != NULL) ? LocationParent::findOne($locationClassment->parent_id)->parent_name : 'Uncategorized';
            $phone = $location->phone;
            $excelDatas[] = [
                'model' => $_manufacturer->name . ' ' . $_model->descrip,
                'serial' => $model['serial'],
                'status' => (!empty($model['status']))?Item::$status[$model['status']]:"",
                'returntracking' => $shipment->master_trackingnumber,
                'tagnum' => $model['tagnum'],
                'origin' => $address,
                'store_number' => $store_number,
                'name' => $name,
                'division' => $division,
                'phone' => $phone,
                'created_at' => $dateCreated,
                'created_by' => $createdBy
            ];
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);

        $objPHPExcel->getActiveSheet()->setTitle($fileName)
                ->setCellValue('A1', 'Model')
                ->setCellValue('B1', 'Serial Number')
                ->setCellValue('C1', 'Name')
                ->setCellValue('D1', 'Status')
                ->setCellValue('E1', 'Return Tracking')
                ->setCellValue('F1', 'Tagnumber')
                ->setCellValue('G1', 'Store Number')
                ->setCellValue('H1', 'Address')
                ->setCellValue('I1', 'Division')
                ->setCellValue('J1', 'Phone')
                ->setCellValue('K1', 'Date Created')
                ->setCellValue('L1', 'Created By');

        $row = 2;
        foreach ($excelDatas as $excelData) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['serial']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['status']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['returntracking']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['tagnum']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $excelData['store_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $excelData['origin']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $excelData['division']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $excelData['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $excelData['created_at']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $excelData['created_by']);
            $row++;
        }

        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(getcwd() . self::FILE_UPLOAD_PATH . '/' . $filename);
        return self::FILE_UPLOAD_PATH . '/' . $filename;
    }

    private function _genReallocationReport($fileName, $customer_id, $dateRange = NULL) {
        $objPHPExcel = new \PHPExcel();
        $sheet = 0;
        $objPHPExcel->setActiveSheetIndex($sheet);
        //$customer = UserHasCustomer::find()->where(['userid' => $userid])->one()->customerid;
        $customer = Customer::findOne($customer_id);
        $startDate = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
        $endDate = date('Y-m-d');
        if ($dateRange != NULL) {
            $dateRange = explode("|", $dateRange);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        }
        $query = Item::find()
                ->innerJoin('lv_itemslog', 'lv_items.id = lv_itemslog.itemid')
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['lv_itemslog.status' => array_search('Transferred', Item::$status), 'lv_items.customer' => $customer->id])
                ->andWhere('DATE(lv_itemslog.created_at) between "' . trim($startDate) . '" and "' . trim($endDate) . '"')->groupby('lv_items.id');
//        if(!empty($divisionId)){
//                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $divisionId])->asArray()->all(), 'location_id');
//                $query->andWhere(['location' => $locations]);
//        } 
        $models = $query->all();
        $excelDatas = array();
        foreach ($models as $model) {
            $_model = Models::findOne($model['model']);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
//            $itemLog = Itemlog::find()->select('locationid')->where(['itemid' => $model->id])->orderBy(['status' => SORT_ASC])->one();
//            $location = Location::findOne($itemLog['locationid']);
            $location = Location::findOne($model->location);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " ";
            if (!empty($location->storename))
                $output .= $location->storename . ' ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $destination = $customer->companyname . ' - ' . $output;
            $itemLog = Itemlog::find()->where(['itemid' => $model->id, 'status' => array_search('Transferred', Item::$status)])->one();
            $location = Location::findOne($itemLog['locationid']);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " - ";
            if (!empty($location->storename))
                $output .= $location->storename . ' - ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
//            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
//            $connection = Yii::$app->getDb();
//            $command = $connection->createCommand($sql)
//                    ->bindValue(':itemid', $model->id)
//                    ->bindValue(':status', array_search('In Transit', Item::$status));
//            $data = $command->queryOne();

            $user = User::findOne($itemLog->userid);
            $modelName = $_manufacturer->name . ' ' . $_model->descrip;
            $serialNumber = $model['serial'];
            $origin = $customer->companyname . ' - ' . $output;
            $dateTransferred = (!empty($itemLog->created_at) && $itemLog->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($itemLog->created_at)) : "-";
            $createdBy = $user->firstname . ' ' . $user->lastname;            
            $excelDatas [] = [
                'model' => $modelName,
                'serial' => $serialNumber,
                'origin' => $origin,
                'dstination' => $destination,
                'created_at' => $dateTransferred,
                'created_by' => $createdBy
            ];
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

        $objPHPExcel->getActiveSheet()->setTitle($fileName)
                ->setCellValue('A1', 'Model')
                ->setCellValue('B1', 'Serial Number')
                ->setCellValue('C1', 'Transferred Form')
                ->setCellValue('D1', 'Transferred To')
                ->setCellValue('E1', 'Date Transferred')
                ->setCellValue('F1', 'Transferred By');

        $row = 2;
        foreach ($excelDatas as $excelData) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['serial']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['origin']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['dstination']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['created_at']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['created_by']);
            $row++;
        }

        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(getcwd() . self::FILE_UPLOAD_PATH . '/' . $filename);
        return self::FILE_UPLOAD_PATH . '/' . $filename;
    }

    private function _genDivisionReport($fileName, $customer_id, $location) {
        $objPHPExcel = new \PHPExcel();
//        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer_id);
        //foreach ($locations as $key => $location) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        if (!empty($location)) {
            $location = LocationClassment::find()->where(['parent_id' => $location])->one();
        }
//        $div_store_locations = ArrayHelper::getColumn(Location::find()->where(['customer_id'=>$customer->id, 'storenum'=>'DIV'])->asArray()->all(), 'id');
        $sheetName = (!empty($location)) ? LocationParent::findOne($location->parent_id)->parent_name : 'Uncategorized';
        $sql = "SELECT lv_items.location, SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
        $sql .= "lv_models.id, lv_manufacturers.name, lv_models.descrip,lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname, SUM(storenum='DIV') as qty_division, SUM(storenum<>'DIV') as qty_location, SUM(confirmed=1) as qty_confirmed FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories
                     ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id INNER JOIN lv_locations ON lv_items.location=lv_locations.id";
        $sql .= (!empty($location)) ? " INNER JOIN lv_locations_classments ON lv_items.location=lv_locations_classments.location_id WHERE lv_items.customer=" . $customer->id . " AND lv_locations_classments.parent_id=" . $location->parent_id . "" : " WHERE lv_items.customer=" . $customer->id . " AND location NOT IN (SELECT DISTINCT(location_id) FROM lv_locations_classments)";
//        $sql .= " GROUP BY lv_items.model ORDER BY name, descrip";
        $sql .= " GROUP BY lv_items.location, lv_items.model ORDER BY lv_locations.storenum";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $divisionReportDatas = $command->queryAll();
        $excelDatas = array();
        foreach ($divisionReportDatas as $model) {
            $location = Location::findOne($model['location']);
            $store_number = (!empty($location->storenum)) ? "Store#: " . $location->storenum : "";
            $address = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $division = $sheetName;
            $name = $location->storename;
            $phone = $location->phone;
            $modelName = $model['name'] . ' ' . $model['descrip'];
            $inStocQty = $model['qty_division'];
            $inProgress = $model['qty_location'];
            $shippedQty = $model['qty_confirmed'];
            $totalQty = $model['qty_division'] + $model['qty_location'] + $model['qty_confirmed'];
//            $instockqty = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id'], 'location'=>$div_store_locations])->count();
//            $inprogress_qty = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id']])->andWhere(['not', ['location'=>$div_store_locations]])->count();
//            $shipped_qty = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id'], 'confirmed'=>1])->count();
//            $totalQty = $instockqty + $inprogress_qty + $shipped_qty;
//            $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
            $excelDatas[] = [
                'model' => $modelName,
                'address' => $address,
                'store' => $store_number,
                'name' => $name,
                'division' => $division,
                'phone' => $phone,
                'stock_qty' => $inStocQty,
                'stock_progress' => $inProgress,
                'stock_shipped' => $shippedQty,
                'total_qty' => $totalQty,
//                'department' => $departMent
            ];
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

        $objPHPExcel->getActiveSheet()->setTitle($sheetName)
                ->setCellValue('A1', 'Model')
                ->setCellValue('B1', 'Name')
                ->setCellValue('C1', 'Store Number')
                ->setCellValue('D1', 'Address')
                ->setCellValue('E1', 'Division')
                ->setCellValue('F1', 'Phone')
                ->setCellValue('G1', 'Qty At Division')
                ->setCellValue('H1', 'Qty On Location')
                ->setCellValue('I1', 'Confirmed Qty')
                ->setCellValue('J1', 'Total');
//                ->setCellValue('K1', 'Department');

        $row = 2;
        foreach ($excelDatas as $excelData) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['store']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['address']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['division']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $excelData['stock_qty']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $excelData['stock_progress']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $excelData['stock_shipped']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $excelData['total_qty']);
//            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $excelData['department']);
            $row++;
        }
        //}
        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(getcwd() . self::FILE_UPLOAD_PATH . '/' . $filename);
        return self::FILE_UPLOAD_PATH . '/' . $filename;
    }

    private function _genInventoryReport($fileName, $customer) {
        $objPHPExcel = new \PHPExcel();
//        $customer = Customer::findOne($customer_id);        
        /************** Divisions *************/
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.5);
        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
        $locationClassments = LocationClassment::find()
                ->innerJoin('lv_locations', 'lv_locations.id = lv_locations_classments.location_id')
                ->where(['customer_id' => $customer])
                ->groupBy('lv_locations_classments.parent_id')->all();
        $Divisions = $Locations = $Inventory = $ClosedLocations = $ConnectionDetails = [];
        $row = 2;
        $objPHPExcel->getActiveSheet()->setTitle('Divisions ('.count($locationClassments).')')
            ->setCellValue('A1', 'Division ID')
            ->setCellValue('B1', 'Division Name');
        $style = [
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $fontStyle = [
            'font' => array(
                'name' => 'Calibri',
                'size' => 9
            ),
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $styleAlignLeft = [
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $objPHPExcel->getActiveSheet()->getStyle("A1:B1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A1:B1")->applyFromArray($fontStyle);
        foreach($locationClassments as $locationClassment){
            $division = LocationParent::findOne($locationClassment->parent_id);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $division->parent_code);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $division->parent_name);            
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":B".$row)->applyFromArray($fontStyle);
            $row++;
        }
        $Divisions = NULL;
        $locationClassments  = NULL;
        unset($Divisions); unset($locationClassments);
        
        /************ Locations *********/
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(38);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $row = 2;        
        $activeLocationClass = LocationClassment::find()
                ->innerJoin('lv_locations', 'lv_locations.id = lv_locations_classments.location_id')
                ->where(['customer_id' => $customer, 'lv_locations.deleted' => 0])
                ->groupBy('lv_locations.id')->all();
        $objPHPExcel->getActiveSheet()->setTitle('Locations ('.count($activeLocationClass).')')
            ->setCellValue('A1', 'Division ID')
            ->setCellValue('B1', 'Store Number')
            ->setCellValue('C1', 'Store Name')
            ->setCellValue('D1', 'Address')
            ->setCellValue('E1', 'City')
            ->setCellValue('F1', 'State')
            ->setCellValue('G1', 'Zip')
            ->setCellValue('H1', 'Phone')
            ->setCellValue('I1', 'Email');      
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($fontStyle);
        foreach($activeLocationClass as $locClass){
            $location = Location::findOne($locClass->location_id);
            $locationParent = LocationParent::findOne($locClass->parent_id);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $locationParent->parent_code);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $location->storenum);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $location->storename);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $location->address.' '. $location->address2);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $location->city);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $location->state);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $location->zipcode);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $location->phone);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $location->email);
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":I".$row)->applyFromArray($fontStyle);
            $objPHPExcel->getActiveSheet()->getStyle("C".$row.":E".$row)->applyFromArray($styleAlignLeft);
            $row++;
        }
        $Locations = NULL;
        $Location = NULL;
        unset($Locations);unset($Location);
        
        /********************** Closed Locations ***********************/
                $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9.2);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(38.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(38);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(50);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $row = 2;
        
        $closedLocations = LocationClassment::find()
                ->innerJoin('lv_locations', 'lv_locations.id = lv_locations_classments.location_id')
                ->where(['customer_id' => $customer, 'lv_locations.deleted' => 1])
                ->groupBy('lv_locations.id')->all();
        $objPHPExcel->getActiveSheet()->setTitle('Closed Locations ('.count($closedLocations).')')
            ->setCellValue('A1', 'Division ID')
            ->setCellValue('B1', 'Store Number')
            ->setCellValue('C1', 'Active Items')
            ->setCellValue('D1', 'Store Name')
            ->setCellValue('E1', 'Address')
            ->setCellValue('F1', 'City')
            ->setCellValue('G1', 'State')
            ->setCellValue('H1', 'Zip')
            ->setCellValue('I1', 'Phone')
            ->setCellValue('J1', 'Email');        
        $objPHPExcel->getActiveSheet()->getStyle("A1:J1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A1:J1")->applyFromArray($fontStyle);
        foreach($closedLocations as $Closedlocation){
            $location = Location::findOne($Closedlocation->location_id);
            $locationParent = LocationParent::findOne($Closedlocation->parent_id);
            $activeItems = Item::find()->where(['customer' => $customer, 'location' => $location->id])->count();
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $locationParent->parent_code);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $location->storenum);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $activeItems);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $location->storename);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $location->address.' '. $location->address2);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $location->city);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $location->state);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $location->zipcode);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $location->phone);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $location->email);
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":J".$row)->applyFromArray($fontStyle);
            $objPHPExcel->getActiveSheet()->getStyle("D".$row.":F".$row)->applyFromArray($styleAlignLeft);
            $row++;
        }
        $ClosedLocations = NULL;
        $ClosedLocation = NULL;
        $closedLocations = NULL;
        unset($ClosedLocations); unset($ClosedLocation); unset($closedLocations);
        
        /************** Inventory *************/
        $items = Item::find()->where(['customer' => $customer])->groupBy('model')->all();
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(2);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setTitle('Inventory ('.count($items).')')
            ->setCellValue('A1', 'Division ID')
            ->setCellValue('B1', 'Store Number')
            ->setCellValue('C1', 'Model Description')
            ->setCellValue('D1', 'Serial Number')
            ->setCellValue('E1', 'Tag Number')
            ->setCellValue('F1', 'Confirmed')
            ->setCellValue('G1', 'Status')
            ->setCellValue('H1', 'Last Serviced Date')
            ->setCellValue('I1', 'Shipped Date');     
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($fontStyle);
        $row = 2;
        foreach($items as $item){
            $locationClass = LocationClassment::find()->where(['location_id' => $item->location])->one();
            $locationParent = LocationParent::findOne($locationClass->parent_id);
            $location = Location::findOne($item->location);
            $models = Models::findOne($item->model);
            $shipped = ($item->shipped != '0000-00-00 00:00:00' && $item->shipped != NULL) ? date('m/d/Y', strtotime($item->shipped)): '-';
            $itemLog = Itemlog::find()->where(['itemid' => $item->id, 'status' => array_search('Serviced', Item::$status)])->orderBy('created_at DESC')->one();
            $serviceDate = ($itemLog != NULL)? date('m/d/Y', strtotime($itemLog->created_at)): 'Not Serviced';
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $locationParent->parent_code);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $location->storenum);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $models->descrip);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $item->serial);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $item->tagnum);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, ($item->confirmed)? 'Yes': 'No');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, Item::$status[$item->status]);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $serviceDate);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $shipped);
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":I".$row)->applyFromArray($fontStyle);
            $row++;
        }
        $Inventory = NULL; $items = NULL; $item = NULL;
        unset($Inventory); unset($items); unset($item);     
        
        /************************ Connection Details **********/
                $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(4);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12.3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setTitle('Connection Details ('.count($activeLocationClass).')')
            ->setCellValue('A1', 'Store Number')
            ->setCellValue('B1', 'Connection Type')
            ->setCellValue('C1', 'IP Address')
            ->setCellValue('D1', 'Subnet Mask')
            ->setCellValue('E1', 'Gateway')
            ->setCellValue('F1', 'Primary DNS')
            ->setCellValue('G1', 'Secondary DNS')
            ->setCellValue('H1', 'WINS Server')
            ->setCellValue('I1', 'Notes');
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("A1:I1")->applyFromArray($fontStyle);
        $row = 2;
        foreach($activeLocationClass as $locClass){
            $locDetail = \app\models\LocationDetails::find(['locationid' => $locClass->location_id])->one();
            $loc = Location::findOne($locClass->location_id);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $loc->storenum);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $loc->connection_type);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $locDetail->ipaddress);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $locDetail->subnet_mask);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $locDetail->gateway);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $locDetail->primary_dns);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $locDetail->secondary_dns);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $locDetail->wins_server);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $loc->notes);
            $objPHPExcel->getActiveSheet()->getStyle("A".$row.":I".$row)->applyFromArray($fontStyle);
            $row++;
        }
        
        $activeLocationClass = NULL;
        $ConnectionDetail = NULL;
        $ConnectionDetails = NULL;
        unset($activeLocationClass); unset($ConnectionDetails); unset($ConnectionDetail);    
                
//        $categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
//                ->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
//                ->where(['customer' => $customer->id])
//                ->groupBy('`lv_models`.`category_id`')
//                ->orderBy('categoryname')
//                ->all();
//        foreach ($categories as $key => $category) {
//            $objPHPExcel->createSheet();
//            $objPHPExcel->setActiveSheetIndex($key);
//            $sheetName = $category->categoryname;
//            $sql = "SELECT lv_items.location, SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
//            $sql .= (isset($_location)) ? "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "') as shipped_qty," : "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
//            $sql .= (isset($_location)) ? " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR (lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "')) as total, " : " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
//            $sql .= " lv_models.id, lv_manufacturers.name, lv_models.descrip, lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id
//                                      WHERE lv_items.customer=" . $customer->id . " AND lv_models.category_id=" . $category->id . " GROUP BY lv_items.model ORDER BY name, descrip";
//            $connection = Yii::$app->getDb();
//            $command = $connection->createCommand($sql);
//            $inventoryReports = $command->queryAll();
//            $excelDatas = array();
//            foreach ($inventoryReports as $model) {
//                $location = Location::findOne($model['location']);
//                $store_number = (!empty($location->storenum)) ? "Store#: " . $location->storenum : "";
//                $address = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
//                $division = $sheetName;
//                $name = $location->storename;
//                $phone = $location->phone;
//                $modelName = $model['name'] . ' ' . $model['descrip'];
//                $inStocQty = $model['instock_qty'];
//                $inProgress = $model['inprogress_qty'];
//                $shippedQty = $model['shipped_qty'];
//                $totalQty = $model['total'];
//                $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
//                $excelDatas[] = [
//                    'model' => $modelName,
//                    'name' => $name,
//                    'store' => $store_number,
//                    'address' => $address,
//                    'division' => $division,
//                    'phone' => $phone,
//                    'stock_qty' => $inStocQty,
//                    'stock_progress' => $inProgress,
//                    'stock_shipped' => $shippedQty,
//                    'total_qty' => $totalQty,
//                    'department' => $departMent
//                ];
//            }
//            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
//
//            $objPHPExcel->getActiveSheet()->setTitle($sheetName)
//                    ->setCellValue('A1', 'Model')
//                    ->setCellValue('B1', 'Name')
//                    ->setCellValue('C1', 'Store Number')
//                    ->setCellValue('D1', 'Address')
//                    ->setCellValue('E1', 'Division')
//                    ->setCellValue('F1', 'Phone')
//                    ->setCellValue('G1', 'In Stock')
//                    ->setCellValue('H1', 'In Progress')
//                    ->setCellValue('I1', 'In Shipped')
//                    ->setCellValue('J1', 'Total')
//                    ->setCellValue('K1', 'Department');
//
//            $row = 2;
//            foreach ($excelDatas as $excelData) {
//                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
//                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['name']);
//                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['store']);
//                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['address']);
//                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['division']);
//                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['phone']);
//                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $excelData['stock_qty']);
//                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $excelData['stock_progress']);
//                $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $excelData['stock_shipped']);
//                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $excelData['total_qty']);
//                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $excelData['department']);
//                $row++;
//            }
//        }
        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(getcwd() . self::FILE_UPLOAD_PATH . '/' . $filename);
        return self::FILE_UPLOAD_PATH . '/' . $filename;
    }

}
