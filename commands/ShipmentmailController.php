<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use \app\models\Shipment;
use \app\models\ShipmentBoxDetail;
use app\models\ReportsLog;
use app\models\Customer;
use app\models\Item;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Location;
use app\models\ShipmentMethod;
use app\models\ShippingCompany;
use app\models\User;

class ShipmentmailController extends Controller {
    
    const FILE_UPLOAD_PATH = "/public/temp/reports";
    const DEBUG = false;
    
    public function actionIndex($message = 'hello world') {
        $date = date('Y-m-d');
//        $shipments = Shipment::find()->where('DATE(dateshipped) = :date and master_trackingnumber is NOT NULL', [':date' => $date])->all();
//        $shipmentSql = 'SELECT group_concat(lv_shipments.id) as shipment_id, lv_salesorders.customer_id FROM `lv_shipments` join lv_salesorders on lv_salesorders.id = lv_shipments.orderid where master_trackingnumber is NOT NULL and date(lv_shipments.dateshipped) = :date group by lv_salesorders.customer_id';
        $shipmentSql = 'SELECT group_concat(lv_shipments.id) as shipment_id, lv_salesorders.customer_id FROM `lv_shipments` join lv_salesorders on lv_salesorders.id = lv_shipments.orderid where master_trackingnumber is NOT NULL and date(lv_shipments.dateshipped) = :date and (select max(status) from lv_items where ordernumber = lv_salesorders.id limit 1) = :status group by customer_id';
        $command = Yii::$app->db->createCommand($shipmentSql)
                ->bindValue(':date', $date)
                ->bindValue(':status', array_search("Shipped", Item::$status));
        $shipmentDatas = $command->queryAll();
        $fileName = 'Shipment Report';
        foreach ($shipmentDatas as $shipmentData) {
            $shipment_ids = explode(",", $shipmentData['shipment_id']);
            $objPHPExcel = new \PHPExcel();
            $sheet = 0;
            $objPHPExcel->setActiveSheetIndex($sheet);
            $customer = Customer::findOne($shipmentData['customer_id']);
            $customerDatas = [];
            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::findOne($shipment_id);
                $shipmethod = ShipmentMethod::findOne($shipment->shipping_deliverymethod);
                $_company = ShippingCompany::findOne($shipmethod->shipping_company_id);
                $orderLog = \app\models\Orderlog::find()->where(['orderid' => $shipment->orderid])->orderBy('created_at desc')->one();
                $createdByUser = User::findOne($orderLog->userid);
                $trackingLink = 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $shipment->master_trackingnumber;
                if (strtolower($_company->name) == 'fedex') {
                    $trackingLink = 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=' . $shipment->master_trackingnumber;
                } else if (strtolower($_company->name) == 'dhl') {
                    $trackingLink = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=' . $shipment->master_trackingnumber;
                } else if (strtolower($_company->name) == 'usps') {
                    $trackingLink = 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels=' . $shipment->master_trackingnumber;
                }
                $address = '';
                $location = Location::findOne($shipment->locationid);
                if (!empty($location->storenum)) {
                    $address .= "Store#: " . $location->storenum . ' ';
                }
                if (!empty($location->storename)) {
                    $address .= $location->storename . ', ';
                }
                $address .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
                $customerDatas[$shipment_id] = [
                    'master_tracking' => $shipment->master_trackingnumber,
                    'tracking_link' => $trackingLink,
                    'address' => $address
                ];
                $sql = "SELECT count(*) as qty, model, if(outgoingpalletnumber is NULL || outgoingpalletnumber = '', outgoingboxnumber, outgoingpalletnumber ) as pallet_box_number, if(outgoingpalletnumber is NULL || outgoingpalletnumber = '', 'box', 'pallet' ) as box_pallet_type from `lv_shipments_items` join lv_items on lv_items.id = lv_shipments_items.itemid where lv_shipments_items.shipmentid = :shipment_id and status = :status group by case when (outgoingpalletnumber is NULL || outgoingpalletnumber = '') then  outgoingboxnumber else outgoingpalletnumber end";
                $command = Yii::$app->db->createCommand($sql)
                        ->bindValue(':status', array_search('Shipped', Item::$status))
                        ->bindValue(':shipment_id', $shipment->id);
                $shipmentDatas = $command->queryAll();
                $totalQty = 0;
                foreach ($shipmentDatas as $shipmentData) {
                    $shipment_box_detail = ShipmentBoxDetail::find()->where(['shipmentid' => $shipment->id, 'pallet_box_number' => $shipmentData['pallet_box_number']])->one();
                    $model = Models::findOne($shipmentData['model']);
                    $manufacturer = Manufacturer::findOne($model->manufacturer);
                    $box_pallet_type = ucfirst($shipmentData['box_pallet_type']);
                    $customerDatas[$shipment_id]['box_pallet_type'] = $box_pallet_type;
                    $customerDatas[$shipment_id]['ship_details'][] = [
                        'pallet_box_number' => ucfirst($shipmentData['box_pallet_type']) . ' #' . $shipmentData['pallet_box_number'],
                        'trackingnumber' => $shipment_box_detail['trackingnumber'],
                        'model' => $manufacturer->name . ' ' . $model->descrip,
                        'quantity' => $shipmentData['qty'],
                        'created_by' => $createdByUser->firstname.' '. $createdByUser->lastname
                    ];
                }
            }
            $row = 1;
            foreach ($customerDatas as $excelDatas) {
                $totalQty = 0;
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);

                $objPHPExcel->getActiveSheet()->setTitle($fileName)
                        ->setCellValue('A' . $row, 'Address')
                        ->setCellValue('C' . $row, 'Master Tracking Number')
                        ->setCellValue('A' . ($row + 2), 'Model')
                        ->setCellValue('B' . ($row + 2), $box_pallet_type . ' Number')
                        ->setCellValue('C' . ($row + 2), 'Tracking Number')
                        ->setCellValue('D' . ($row + 2), 'Quantity');
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $row . ':B' . $row);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . ($row + 1) . ':B' . ($row + 1));
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $row . ':D' . $row);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . ($row + 1) . ':D' . ($row + 1));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($row + 2) . ':D' . ($row + 2))->getFont()->setBold(true);

                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle("A" . $row . ":D" . $row)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($row + 2) . ":D" . ($row + 2))->applyFromArray($style);

                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
                );

                $objPHPExcel->getActiveSheet()->setCellValue('A' . ($row + 1), $excelDatas['address']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . ($row + 1), $excelDatas['master_tracking']);
                $dataRow = ($row + 3);
                foreach ($excelDatas['ship_details'] as $excelData) {
                    $totalQty += (int) $excelData['quantity'];
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $dataRow, $excelData['model']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $dataRow, $excelData['pallet_box_number']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $dataRow, $excelData['trackingnumber']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $dataRow, $excelData['quantity']);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . $dataRow . ":D" . $dataRow)->applyFromArray($style);
                    $dataRow++;
                }
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                );
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $dataRow . ':C' . $dataRow);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $dataRow . ':C' . $dataRow)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $dataRow, 'Total Items');
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
                );
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $dataRow, $totalQty);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $dataRow)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('D' . $dataRow)->applyFromArray($style);
                $row = ($row + 2) + count($excelDatas['ship_details']) + 3;
            }
            header('Content-Type: application/vnd.ms-excel');
            $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
            header('Content-Disposition: attachment;filename=' . $filename . ' ');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save(getcwd() . self::FILE_UPLOAD_PATH . '/' . $filename);
            $attachFilename = self::FILE_UPLOAD_PATH . '/' . $filename;
            
            $users = \yii\helpers\ArrayHelper::getColumn(\app\models\UserHasCustomer::find()->where(['customerid' => $customer->id])->asArray()->all(), 'userid');
            $reports_settings_models = \app\models\ReportsSettings::find()->where(['report_type_id'=> 6, 'userid' => $users])->all();
            foreach($reports_settings_models as $reports_settings_model){
                $reports_log = ReportsLog::find()->where(['report_type_id' => 6, 'customer_id' => $reports_settings_model->userid])->orderBy('date_sent desc')->one();
                $sendMail = false;
                $date1 = date('Y-m-d');
                $date2 = date('Y-m-d', strtotime($reports_log->date_sent));
                $diff = abs(strtotime($date2) - strtotime($date1));
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $reportOption = '';
                $reportOptionId = $reports_settings_model->report_option_id;
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
                if ($sendMail && $attachFilename != NULL) {
                    $reportLog = New ReportsLog;
                    $reportLog->report_type_id = 6;
                    $reportLog->customer_id = $reports_settings_model->userid;
                    $reportLog->date_sent = date('Y-m-d H:i:s');
                    $reportLog->save();
                    $baseUrl = \Yii::$app->getUrlManager()->createAbsoluteUrl('');
                    $downloadFileLink = \Yii::$app->getUrlManager()->createAbsoluteUrl($attachFilename);
                    $userModel = User::findOne($reports_settings_model->userid);
                    $body = $this->renderPartial("/site/_shipment_mailreport", ['downloadFileLink' => $downloadFileLink, 'model' => $userModel, 'baseUrl' => $baseUrl, 'customerDatas' => $customerDatas, 'createdByUser' => $createdByUser]);
                    Yii::$app->mailer->compose()
                        ->setFrom([Yii::$app->params['supportEmail'] => 'Asset Management System'])
                        //->setTo(['matt.ebersole@assetenterprises.com'])
                        ->setTo($userModel->email)
                        ->setSubject('Your '. $fileName.' for '.date('l, F dS', strtotime($date)))
                        ->setHtmlBody($body)
                        ->send();
                }
            }
            
//            $reports_log = ReportsLog::find()->where(['report_type_id' => 6, 'customer_id' => $customer->id])->orderBy('date_sent desc')->one();
//            $sendMail = false;
//            $date1 = date('Y-m-d');
//            $date2 = date('Y-m-d', strtotime($reports_log->date_sent));
//            $diff = abs(strtotime($date2) - strtotime($date1));
//            $years = floor($diff / (365 * 60 * 60 * 24));
//            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
//            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
//            if ($reports_log == NULL) {
//                    $sendMail = true;
//            } else if ($days >= 1 || $months >= 1 || $years >= 1) {
//                    $sendMail = true;
//            }
//            if ($sendMail) {
//                if ($attachFilename != NULL) {
//                    $reportLog = New ReportsLog;
//                    $reportLog->report_type_id = 6;
//                    $reportLog->customer_id = $customer->id;
//                    $reportLog->date_sent = date('Y-m-d H:i:s');
//                    $reportLog->save();
//                }
//                $baseUrl = \Yii::$app->getUrlManager()->createAbsoluteUrl('');
//                $downloadFileLink = \Yii::$app->getUrlManager()->createAbsoluteUrl($attachFilename);
//                $body = $this->renderPartial("/site/_shipment_mailreport", ['downloadFileLink' => $downloadFileLink, 'model' => $customer, 'baseUrl' => $baseUrl, 'customerDatas' => $customerDatas]);
////                $reports_settings_models = ReportsSettings::find()->where(['report_type_id'=>6])->all();
//                foreach ($reports_settings_models as $reports_settings_model) {
//                	$userid = $reports_settings_model->userid;
//                	$user = User::findOne($userid);
//	                Yii::$app->mailer->compose()
//	                        ->setFrom([Yii::$app->params['supportEmail'] => 'Asset Management System'])
//	                        ->setTo(['matt.ebersole@assetenterprises.com'])
//	                        //->setTo($user->email)
//	                        ->setSubject('Your '. $fileName.' for '.date('l, F dS', strtotime($date)))
//	                        ->setHtmlBody($body)
//	                        ->send();
//                }
//            }            
        }
    }
}
