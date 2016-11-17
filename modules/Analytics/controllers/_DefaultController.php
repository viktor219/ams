<?php

namespace app\modules\Analytics\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
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
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\components\AccessRule;

class DefaultController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            User::TYPE_ADMIN,
                            User::TYPE_CUSTOMER_ADMIN,
                            User::REPRESENTATIVE,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        if (Yii::$app->user->identity->usertype == User::REPRESENTATIVE) {
            $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
            $customer = Customer::findOne($customer);
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->where(['status' => array_search('In Transit', Item::$status), 'customer' => $customer->id]);
            $serviceReportDataP = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 10],
            ]);
            $lastMonthDate = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $todaysDate = date('Y-m-d');
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->where(['status' => array_search('Transferred', Item::$status), 'customer' => $customer->id])
                    ->andWhere('DATE_FORMAT(lv_items.created_at,"%Y-%m-%d") between "' . $lastMonthDate . '" and "' . $todaysDate . '"');
            $reAllocationReportDataP = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 10],
            ]);

            $locations = Location::find()->innerJoin('lv_items', '`lv_items`.`location` = `lv_locations`.`id`')
                    ->where(['`lv_items`.`customer`' => $customer->id])
                    ->groupBy('`lv_items`.`customer`, `lv_items`.`location`')
                    ->all();
            $categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
                    ->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
                    ->where(['customer' => $customer->id])
                    ->groupBy('`lv_models`.`category_id`')
                    ->orderBy('categoryname')
                    ->all();
            $_inventorylocations = LocationClassment::find()->select('parent_id')->where(['customer_id' => $customer->id])->andWhere(['not', ['parent_id' => null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();
            if (Yii::$app->request->isAjax) {
                $_post = Yii::$app->request->get();
                if ($_post['report'] == 'service') {
                    $html = $this->renderPartial('_servicereport', [
                        'dataProvider' => $serviceReportDataP,
                        'customer' => $customer
                    ]);
                } else if ($_post['report'] == 'reallocation') {
                    if (isset($_post['daterange'])) {
                        $dateRange = explode("|", $_post['daterange']);
                        $query = Item::find()
                                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                                ->where(['status' => array_search('Transferred', Item::$status), 'customer' => $customer->id])
                                ->andWhere('DATE_FORMAT(lv_items.created_at,"%Y-%m-%d") between "' . trim($dateRange[0]) . '" and "' . trim($dateRange[1]) . '"');
                        $reAllocationReportDataP = new ActiveDataProvider([
                            'query' => $query,
                            'pagination' => ['pageSize' => 10],
                        ]);
                    }
                    $html = $this->renderPartial('_reallocationreport', [
                        'dataProvider' => $reAllocationReportDataP,
                        'customer' => $customer
                    ]);
                }
                $_retArray = array('success' => true, 'html' => $html);
                echo json_encode($_retArray);
                exit();
            } else {

                return $this->render('_reports', array(
                            'serviceReportDataP' => $serviceReportDataP,
                            'reAllocationReportDataP' => $reAllocationReportDataP,
                            'customer' => $customer,
                            '_locations' => $locations,
                            'categories' => $categories,
                            '_inventorylocations' => $_inventorylocations
                ));
            }
        } else {
            return $this->render('index');
        }
    }

    function actionExport($type) {
        $fileName = 'Service Report';
        $_post = Yii::$app->request->get();
        if ($type == 'service') {
            $this->_genServiceReport($fileName);
        } else if ($type == 'reallocation') {
            $fileName = 'Reallocation Report';
            $this->_genReallocationReport($fileName, $_post['daterange']);
        } else if ($type == 'division') {
            $fileName = 'Division Report';
            $this->_genDivisionReport($fileName, $_post['parentid']);
        } else if ($type == 'inventory') {
            $fileName = 'Inventory Report';
            $this->_genInventoryReport($fileName);
        }
    }

    private function _genServiceReport($fileName) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $query = Item::find()
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['status' => array_search('In Transit', Item::$status), 'customer' => $customer->id]);

        $objPHPExcel = new \PHPExcel();
        $sheet = 0;
        $objPHPExcel->setActiveSheetIndex($sheet);

        $models = $query->all();
        foreach ($models as $model) {
            $_model = Models::findOne($model->model);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
            $location = Location::findOne($model->location);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " - ";
            if (!empty($location->storename))
                $output .= $location->storename . ' - ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $location = $customer->companyname . ' - ' . $output;
            $dateCreated = (!empty($models->created_at) && $model->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($model->created_at)) : "-";
            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql)
                    ->bindValue(':itemid', $model->id)
                    ->bindValue(':status', array_search('In Transit', Item::$status));
            $data = $command->queryOne();
            $createdBy = $data['firstname'] . ' ' . $data['lastname'];
            $excelDatas[] = [
                'model' => $_manufacturer->name . ' ' . $_model->descrip,
                'serial' => $model['serial'],
                'origin' => $location,
                'created_at' => $dateCreated,
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
                ->setCellValue('C1', 'Store Number')
                ->setCellValue('D1', 'Date Created')
                ->setCellValue('E1', 'Created By');

        $row = 2;
        foreach ($excelDatas as $excelData) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['serial']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['origin']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['created_at']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['created_by']);
            $row++;
        }

        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    private function _genReallocationReport($fileName, $dateRange = NULL) {
        $objPHPExcel = new \PHPExcel();
        $sheet = 0;
        $objPHPExcel->setActiveSheetIndex($sheet);
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $startDate = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
        $endDate = date('Y-m-d');
        if ($dateRange != NULL) {
            $dateRange = explode("|", $dateRange);
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
        }
        $query = Item::find()
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['status' => array_search('Transferred', Item::$status), 'customer' => $customer->id])
                ->andWhere('DATE_FORMAT(lv_items.created_at,"%Y-%m-%d") between "' . $startDate . '" and "' . $endDate . '"');
        $models = $query->all();
        foreach ($models as $model) {
            $_model = Models::findOne($model['model']);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
            $itemLog = Itemlog::find()->select('locationid')->where(['itemid' => $model->id])->orderBy(['status' => SORT_ASC])->one();
            $location = Location::findOne($itemLog['locationid']);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " - ";
            if (!empty($location->storename))
                $output .= $location->storename . ' - ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $itemLog = Itemlog::find()->select('locationid')->where(['itemid' => $model->id, 'status' => array_search('Transferred', Item::$status)])->one();
            $location = Location::findOne($itemLog['locationid']);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " - ";
            if (!empty($location->storename))
                $output .= $location->storename . ' - ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql)
                    ->bindValue(':itemid', $model->id)
                    ->bindValue(':status', array_search('In Transit', Item::$status));
            $data = $command->queryOne();

            $modelName = $_manufacturer->name . ' ' . $_model->descrip;
            $serialNumber = $model['serial'];
            $origin = $customer->companyname . ' - ' . $output;
            $destination = $customer->companyname . ' - ' . $output;
            $dateTransferred = (!empty($model->created_at) && $model->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($model->created_at)) : "-";
            $createdBy = $data['firstname'] . ' ' . $data['lastname'];
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
                ->setCellValue('C1', 'Store Number')
                ->setCellValue('D1', 'Destination')
                ->setCellValue('E1', 'Date Transferred')
                ->setCellValue('F1', 'Created By');

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
        $objWriter->save('php://output');
    }

    private function _genDivisionReport($fileName, $location) {
        $objPHPExcel = new \PHPExcel();
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        //foreach ($locations as $key => $location) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        if(!empty($location)){
            $location = LocationClassment::find()->where(['parent_id' => $location])->one();
        }
        $sheetName = (!empty($location)) ? LocationParent::findOne($location->parent_id)->parent_name : 'Uncategorized';
        $sql = "SELECT SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
        $sql .= "lv_models.id, lv_manufacturers.name, lv_models.descrip,lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories
                     ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id INNER JOIN lv_locations ON lv_items.location=lv_locations.id";
        $sql .= (!empty($location)) ? " INNER JOIN lv_locations_classments ON lv_items.location=lv_locations_classments.location_id WHERE lv_items.customer=" . $customer->id . " AND lv_locations_classments.parent_id=" . $location->parent_id . "" : " WHERE lv_items.customer=" . $customer->id . " AND location NOT IN (SELECT DISTINCT(location_id) FROM lv_locations_classments WHERE parent_id IS NULL)";
        $sql .= " GROUP BY lv_items.model ORDER BY name, descrip";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $divisionReportDatas = $command->queryAll();
        $excelDatas = array();
        foreach ($divisionReportDatas as $model) {
            $modelName = $model['name'] . ' ' . $model['descrip'];
            $inStocQty = $model['instock_qty'];
            $inProgress = $model['inprogress_qty'];
            $shippedQty = $model['shipped_qty'];
            $totalQty = $model['total'];
            $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
            $excelDatas[] = [
                'model' => $modelName,
                'stock_qty' => $inStocQty,
                'stock_progress' => $inProgress,
                'stock_shipped' => $shippedQty,
                'total_qty' => $totalQty,
                'department' => $departMent
            ];
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

        $objPHPExcel->getActiveSheet()->setTitle($sheetName)
                ->setCellValue('A1', 'Model')
                ->setCellValue('B1', 'In Stock')
                ->setCellValue('C1', 'In Progress')
                ->setCellValue('D1', 'In Shipped')
                ->setCellValue('E1', 'Total')
                ->setCellValue('F1', 'Department');

        $row = 2;
        foreach ($excelDatas as $excelData) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['stock_qty']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['stock_progress']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['stock_shipped']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['total_qty']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['department']);
            $row++;
        }
        //}
        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    private function _genInventoryReport($fileName) {
        $objPHPExcel = new \PHPExcel();
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
                ->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
                ->where(['customer' => $customer->id])
                ->groupBy('`lv_models`.`category_id`')
                ->orderBy('categoryname')
                ->all();
        foreach ($categories as $key => $category) {
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($key);
            $sheetName = $category->categoryname;
            $sql = "SELECT SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
            $sql .= (isset($_location)) ? "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "') as shipped_qty," : "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
            $sql .= (isset($_location)) ? " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR (lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "')) as total, " : " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
            $sql .= " lv_models.id, lv_manufacturers.name, lv_models.descrip, lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id
                                      WHERE lv_items.customer=" . $customer->id . " AND lv_models.category_id=" . $category->id . " GROUP BY lv_items.model ORDER BY name, descrip";
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $inventoryReports = $command->queryAll();
            $excelDatas = array();
            foreach ($inventoryReports as $model) {
                $modelName = $model['name'] . ' ' . $model['descrip'];
                $inStocQty = $model['instock_qty'];
                $inProgress = $model['inprogress_qty'];
                $shippedQty = $model['shipped_qty'];
                $totalQty = $model['total'];
                $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
                $excelDatas[] = [
                    'model' => $modelName,
                    'stock_qty' => $inStocQty,
                    'stock_progress' => $inProgress,
                    'stock_shipped' => $shippedQty,
                    'total_qty' => $totalQty,
                    'department' => $departMent
                ];
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

            $objPHPExcel->getActiveSheet()->setTitle($sheetName)
                    ->setCellValue('A1', 'Model')
                    ->setCellValue('B1', 'In Stock')
                    ->setCellValue('C1', 'In Progress')
                    ->setCellValue('D1', 'In Shipped')
                    ->setCellValue('E1', 'Total')
                    ->setCellValue('F1', 'Department');

            $row = 2;
            foreach ($excelDatas as $excelData) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $excelData['model']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $excelData['stock_qty']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $excelData['stock_progress']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $excelData['stock_shipped']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $excelData['total_qty']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $excelData['department']);
                $row++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        $filename = $fileName . "_" . date("d-m-Y-His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
//        'customer'=>$customer, 'categories'=>$categories
    }

}
