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
use app\models\ReportsTypes;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use app\components\AccessRule;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;

class DefaultController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'reportsettings', 'setoption', 'getserviceopenreport', 'getserviceclosereport', 'getreallocationreport', 'getdivisionreport'],
                'rules' => [
                    [
                        'actions' => ['index', 'setoption', 'reportsettings', 'getserviceopenreport', 'getserviceclosereport', 'getreallocationreport', 'getdivisionreport'],
                        'allow' => true,
                        'roles' => [
                            User::TYPE_ADMIN,
                            User::TYPE_CUSTOMER_ADMIN,
                            User::REPRESENTATIVE,
                            User::TYPE_CUSTOMER,
                            User::TYPE_SALES,
                            User::TYPE_SHIPPING,
                            User::TYPE_BILLING
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
        if (Yii::$app->user->identity->usertype == User::REPRESENTATIVE || Yii::$app->user->identity->usertype == User::TYPE_CUSTOMER) {
            $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
            $customer = Customer::findOne($customer);
            $user = \app\models\Users::findOne(Yii::$app->user->id);
            $divisionIds = [];
//            $query = Item::find()
//                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
//                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
//                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
//                    ->andWhere('status < ' . array_search('Shipped', Item::$status));
            if($user->division_id){
                $reportSettings = \app\models\ReportsSettings::find()
                        ->innerJoin('lv_reports_types', 'lv_reports_types.id = lv_reports_settings.report_type_id')
                        ->where(['userid' => Yii::$app->user->id])
                        ->all();
                if($reportSettings != NULL){
                    foreach($reportSettings as $reportSetting){
                        if($reportSetting->is_division){
    //                    $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $user->division_id])->asArray()->all(), 'location_id');
    //                    $query->andWhere(['location' => $locations]);
                            $divisionIds[$reportSetting->report_type_id] = $user->division_id;
                        }
                    }
                }
            }
//            $serviceReportDataP = new ActiveDataProvider([
//                'query' => $query,
//                'pagination' => ['pageSize' => 10],
//            ]);

//            $query = Item::find()
//                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
//                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
//                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
//                    ->andWhere('status >= ' . array_search('Shipped', Item::$status));
//            $serviceReportCloseP = new ActiveDataProvider([
//                'query' => $query,
//                'pagination' => ['pageSize' => 10],
//            ]);

//            $lastMonthDate = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
//            $todaysDate = date('Y-m-d');
//            $query = Item::find()
//                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
//                    ->where(['status' => array_search('Transferred', Item::$status), 'customer' => $customer->id])
//                    ->andWhere('DATE_FORMAT(lv_items.created_at,"%Y-%m-%d") between "' . $lastMonthDate . '" and "' . $todaysDate . '"');
//            $reAllocationReportDataP = new ActiveDataProvider([
//                'query' => $query,
//                'pagination' => ['pageSize' => 10],
//            ]);
//
//            $locations = Location::find()->innerJoin('lv_items', '`lv_items`.`location` = `lv_locations`.`id`')
//                    ->where(['`lv_items`.`customer`' => $customer->id])
//                    ->groupBy('`lv_items`.`customer`, `lv_items`.`location`')
//                    ->all();
//            $categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
//                    ->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
//                    ->where(['customer' => $customer->id])
//                    ->groupBy('`lv_models`.`category_id`')
//                    ->orderBy('categoryname')
//                    ->all();
//            $_inventorylocations = LocationClassment::find()->select('parent_id')->where(['customer_id' => $customer->id])->andWhere(['not', ['parent_id' => null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();
//            if (Yii::$app->request->isAjax) {
//                $_post = Yii::$app->request->get();
//                if ($_post['report'] == 'service') {
//                    $html = $this->renderPartial('_servicereport', [
//                        'dataProvider' => $serviceReportDataP,
//                        'customer' => $customer
//                    ]);
//                } else if ($_post['report'] == 'serviceclose') {
//                    $html = $this->renderPartial('_servicecloserep', [
//                        'dataProvider' => $serviceReportCloseP,
//                        'customer' => $customer
//                    ]);
//                } else if ($_post['report'] == 'reallocation') {
//                    if (isset($_post['daterange'])) {
//                        $dateRange = explode("|", $_post['daterange']);
//                        $query = Item::find()
//                                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
//                                ->where(['status' => array_search('Transferred', Item::$status), 'customer' => $customer->id])
//                                ->andWhere('DATE_FORMAT(lv_items.created_at,"%Y-%m-%d") between "' . trim($dateRange[0]) . '" and "' . trim($dateRange[1]) . '"');
//                        $reAllocationReportDataP = new ActiveDataProvider([
//                            'query' => $query,
//                            'pagination' => ['pageSize' => 10],
//                        ]);
//                    }
//                    $html = $this->renderPartial('_reallocationreport', [
//                        'dataProvider' => $reAllocationReportDataP,
//                        'customer' => $customer
//                    ]);
//                }
//                $_retArray = array('success' => true, 'html' => $html);
//                echo json_encode($_retArray);
//                exit();
//            } else {
                $_report_types = ArrayHelper::map(ReportsTypes::find()->asArray()->all(), 'id', 'type');
                $customers = $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => \Yii::$app->user->id])->asArray()->all(), 'customerid');
                $users = ArrayHelper::getColumn(UserHasCustomer::find()->where(['customerid' => $customers])->asArray()->all(), 'userid');

                return $this->render('_reports', array(
                            //'serviceReportDataP' => $serviceReportDataP,
                            //'serviceReportCloseP' => $serviceReportCloseP,
//                            'reAllocationReportDataP' => $reAllocationReportDataP,
                            'customer' => $customer,
                            'users' => $users,
                            'divisionIds' => $divisionIds,
//                            '_locations' => $locations,
                           // 'categories' => $categories,
                            '_report_types' => $_report_types,
//                            '_inventorylocations' => $_inventorylocations
                ));
//            }
        } else {
            return $this->render('index');
        }
    }
    
    public function actionGetserviceclosereport(){
            $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
            $customer = Customer::findOne($customer);
            $_post = Yii::$app->request->get();
//            $user = \app\models\Users::findOne(Yii::$app->user->id);            
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                    ->andWhere('status >= ' . array_search('Shipped', Item::$status) . ' AND status <= 21');
//            if($user->division_id){
//                $reportSetting = \app\models\ReportsSettings::find()
//                        ->innerJoin('lv_reports_types', 'lv_reports_types.id = lv_reports_settings.report_type_id')
//                        ->where(['userid' => Yii::$app->user->id, 'lv_reports_types.type' => 'Service (Closed) Report'])
//                        ->one();
//                if($reportSetting != NULL && $reportSetting->is_division){
//                    $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $user->division_id])->asArray()->all(), 'location_id');
//                    $query->andWhere(['location' => $locations]);
//                }
//            }
            if(!empty($_post['division_id'])){
                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $_post['division_id']])->asArray()->all(), 'location_id');
                $query->andWhere(['location' => $locations]);
            }            
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 10],
            ]);
            $html = $this->renderPartial('_servicecloserep', [
                'dataProvider' => $dataProvider,
                'customer' => $customer
            ]);
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
    }
    
    public function actionGetserviceopenreport(){
            $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
            $customer = Customer::findOne($customer);
//            $user = \app\models\Users::findOne(Yii::$app->user->id);
            $_post = Yii::$app->request->get();
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                    ->andWhere('status < ' . array_search('Shipped', Item::$status));
//            if($user->division_id){
//                $reportSetting = \app\models\ReportsSettings::find()
//                        ->innerJoin('lv_reports_types', 'lv_reports_types.id = lv_reports_settings.report_type_id')
//                        ->where(['userid' => Yii::$app->user->id, 'lv_reports_types.type' => 'Service (Open) Report'])
//                        ->one();
//                if($reportSetting != NULL && $reportSetting->is_division){
            if(!empty($_post['division_id'])){
                    $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $_post['division_id']])->asArray()->all(), 'location_id');
                    $query->andWhere(['location' => $locations]);
            }
//            }
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => ['pageSize' => 10],
            ]);
            $html = $this->renderPartial('_servicereport', [
                'dataProvider' => $dataProvider,
                'customer' => $customer
            ]);
            $_retArray = array('success' => true, 'html' => $html);
            echo json_encode($_retArray);
            exit();
    }
    
    public function actionGetreallocationreport(){
        $custId = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($custId);
//        $user = \app\models\Users::findOne(Yii::$app->user->id);
        $date1 = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
        $date2 = date('Y-m-d');
        $_post = Yii::$app->request->get();
        if(isset($_post['daterange'])){
            $dateRange = explode("|", $_post['daterange']);
            $date1 = $dateRange[0];
            $date2 = $dateRange[1];
        }
        $query = Item::find()
                ->innerJoin('lv_itemslog', 'lv_items.id = lv_itemslog.itemid')
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['lv_itemslog.status' => array_search('Transferred', Item::$status), 'lv_items.customer' => $customer->id])
                ->andWhere('DATE(lv_itemslog.created_at) between "' . trim($date1) . '" and "' . trim($date2) . '"')->groupby('lv_items.id');
//        if($user->division_id){
//            $reportSetting = \app\models\ReportsSettings::find()
//                    ->innerJoin('lv_reports_types', 'lv_reports_types.id = lv_reports_settings.report_type_id')
//                    ->where(['userid' => Yii::$app->user->id, 'lv_reports_types.type' => 'Reallocation Report'])
//                    ->one();
//            if($reportSetting != NULL && $reportSetting->is_division){
//                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $user->division_id])->asArray()->all(), 'location_id');
//                $query->andWhere(['location' => $locations]);
//            }
//        }
        if(!empty($_post['division_id'])){
            $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $_post['division_id']])->asArray()->all(), 'location_id');
            $query->andWhere(['location' => $locations]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);
        $html = $this->renderPartial('_reallocationreport', [
            'dataProvider' => $dataProvider,
            'customer' => $customer
        ]);
        $_retArray = array('success' => true, 'html' => $html);
        echo json_encode($_retArray);
        exit();
    }
    
    public function actionGetdivisionreport(){
        $custId = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($custId);
//        $user = \app\models\Users::findOne(Yii::$app->user->id);
        $_post = Yii::$app->request->get();
//        $isDivision = false;
//        if($user->division_id){
//            $reportSetting = \app\models\ReportsSettings::find()
//                    ->innerJoin('lv_reports_types', 'lv_reports_types.id = lv_reports_settings.report_type_id')
//                    ->where(['userid' => Yii::$app->user->id, 'lv_reports_types.type' => 'Division Report'])
//                    ->one();
//            if($reportSetting != NULL && $reportSetting->is_division){
//                $isDivision = true;
//            }
//        }
        if(empty($_post['division_id'])){
            $inventorylocations = LocationClassment::find()->select('parent_id')->where(['customer_id' => $customer->id])->andWhere(['not', ['parent_id' => null]])->innerJoin('lv_locations', '`lv_locations`.`id` = `lv_locations_classments`.`location_id`')->groupBy('location_id')->distinct()->all();
            $html = $this->renderPartial('_divisionreport', ['customer' => $customer, 'locations' => $inventorylocations, '_location' => '']);
        } else {
            $locationClassment = LocationClassment::find()->where(['parent_id' => $_post['division_id']])->one();
            $dataProvider = Yii::$app->common->getDivisionDataProvider($locationClassment);
            $html = $this->renderPartial('_divisionlocation_report', ['customer' => $customer, 'dataProvider' => $dataProvider, 'location' => $locationClassment, 'isDivisionOnly' => true]);
        }
        $_retArray = array('success' => true, 'html' => $html);
        echo json_encode($_retArray);
        exit();
    }


    public function actionReportsettings() {
        $report_types = ReportsTypes::find()->all();
        $report_options = \app\models\ReportsOptions::find()->all();
        $_post = Yii::$app->request->post();
        $divisionId = \app\models\Users::findOne(Yii::$app->user->id)->division_id;
        $division = '';
        if($divisionId){
            $division = LocationParent::findOne($divisionId)->parent_name;
        }
//        if (Yii::$app->request->isPost) {
//            $type = $_post['type'];
//        }
//        $report_setting_model = \app\models\ReportsSettings::find()->where(['userid' => Yii::$app->user->id, 'report_type_id' => $type])->one();
//        if ($report_setting_model == NULL) {
//            $report_setting_model = New \app\models\ReportsSettings;
//            $report_setting_model->report_type_id = $type;
//        }
        if (isset($_post['submit'])) {
            foreach ($_post['report_option_id'] as $type => $report_opts) {
                $report_setting_model = \app\models\ReportsSettings::find()->where(['userid' => Yii::$app->user->id, 'report_type_id' => $type])->one();
                if ($report_setting_model == NULL) {
                    $report_setting_model = New \app\models\ReportsSettings;
                }
                if ($report_setting_model->isNewRecord) {
                    $report_setting_model->report_type_id = $type;
                    $report_setting_model->userid = Yii::$app->user->id;
                    $report_setting_model->created_at = date('Y-m-d H:i:s');
                }
                $report_setting_model->report_option_id = $report_opts;
                $report_setting_model->is_division = (isset($_post['is_division'][$type])) ? 1 : 0;
                if ($report_setting_model->save()) {
                    Yii::$app->getSession()->setFlash('success', 'Report Setting has been saved successfully.');
                } else {
                    Yii::$app->getSession()->setFlash('danger', 'There is some problem in saving Report Setting.');
                }
            }
        }
        return $this->render('_reportsettings', ['report_types' => $report_types, 'report_options' => $report_options, 'division' => $division]);

//        $reportsSettings = \app\models\ReportsOptions::find()->all();
//        return $this->render('_reportsettings', ['report_setting_model' => $report_setting_model, 'reportsSettings' => $reportsSettings]);
    }

    public function actionSetoption($id, $type) {
        $uid = Yii::$app->user->id;
        $customers = ArrayHelper::getColumn(UserHasCustomer::find()->where(['userid' => $uid])->asArray()->all(), 'customerid');
        $users = ArrayHelper::getColumn(UserHasCustomer::find()->where(['customerid' => $customers])->asArray()->all(), 'userid');
        if (!in_array($uid, $users)) {
            $users[] = $uid;
        }
        //\app\models\ReportsSettings::deleteAll(['userid' => $users, 'report_type_id' => $type]);
        $success = false;
        foreach ($users as $user) {
            $report_setting = \app\models\ReportsSettings::find()->where(['userid' => $user, 'report_type_id' => $type])->one();
            if ($report_setting == NULL) {
                $report_setting = New \app\models\ReportsSettings;
            }
            $report_setting->userid = $user;
            $report_setting->report_type_id = $type;
            $report_setting->report_option_id = $id;
            $report_setting->created_at = date('Y-m-d H:i:s');
            $success = $report_setting->save();
        }
        if ($success) {
            Yii::$app->getSession()->setFlash('success', 'Report Setting has been saved successfully.');
        } else {
            Yii::$app->getSession()->setFlash('danger', 'There is some problem in saving Report Setting.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionExportpdf() {
        $_post = Yii::$app->request->get();
        $type = $_post['type'];
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $_media_customer = \app\models\Medias::findOne($customer->id);
        if ($type == 'service') {
            $header = ($_post['isclose'])?'Service (Closed) Report':'Service (Open) Report';
            $pdfDatas = $this->_genPdfServiceReport($_post['isclose'], $_post['division_id']);
            $_view_file = '_servicepdf';
        } else if ($type == 'reallocation') {
            $daterange = isset($_post['daterange']) ? $_post['daterange']: "";
            $header = "Reallocation Report";
            $pdfDatas = $this->_genPdfReallocationReport($daterange, $_post['division_id']);
            $_view_file = '_reallocationpdf';
        } else if ($type == 'division') {
            $header = "Division Report";
            $pdfDatas = $this->_genPdfDivisionReport($_post['parentid'],  $_post['division_id']);
            $_view_file = '_divisionpdf';
        } else if ($type == 'inventory') {
            $header = "Full Inventory Report";
            $pdfDatas = $this->_genPdfInventoryReport($_post['division_id']);
            $_view_file = '_inventorypdf';
        }
        $content = $this->renderPartial($_view_file, ['pdfDatas' => $pdfDatas, '_media_customer' => $_media_customer, 'customer' => $customer, 'header' => $header]);
        $cssContent = '#order-details{ border-collapse: collapse; } #order-details th{
                border: 1px solid gray;
                text-align: left !important;
                font-size: 8px;
                font-family: arial;
                color: #1A82C3;
                border-collapse: collapse !important;
            } #order-details td{font-size: 8px; text-align: left !important; font-family: arial; border-collapse: collapse !important; border: 1px solid gray}';
        $pdf = new Pdf([
            'orientation' => Pdf::ORIENT_LANDSCAPE,
        ]);
        $pdf->content = $content;
        $pdf->orientation = 'L';
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->WriteHTML($cssContent, 1, true, true);
        $mpdf->SetTitle($header);
        //$mpdf->SetHeader("<div style='text-align: center; font-size: 15px'>".$header."</div>");
        $footer = '{PAGENO}';
        $mpdf->SetFooter($footer);
//        $mpdf->SetFooter('{PAGENO}');
        return $pdf->render();
    }

    function actionExport($type) {
        $fileName = 'Service Report';
        $_post = Yii::$app->request->get();
        if ($type == 'service') {
            $fileName = ($_post['isclose']) ? 'Service Report(Closed)': 'Service Report(Open)';
            $this->_genServiceReport($fileName, $_post['isclose'], $_post['division_id']);
        } else if ($type == 'reallocation') {
            $fileName = 'Reallocation Report';
            $this->_genReallocationReport($fileName, $_post['daterange'], $_post['division_id']);
        } else if ($type == 'division') {
            $fileName = 'Division Report';
            $this->_genDivisionReport($fileName, $_post['parentid']);
        } else if ($type == 'inventory') {
            $fileName = 'Inventory Report';
            $this->_genInventoryReport($fileName, $_post['division_id']);
        }
    }

    private function _genServiceReport($fileName, $isClose, $divisionId) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $query = Item::find()
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                ->andWhere('status < ' . array_search('Shipped', Item::$status));
        if ($isClose) {
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                    ->andWhere('status >= ' . array_search('Shipped', Item::$status));
        }
        if(!empty($divisionId)){
                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $divisionId])->asArray()->all(), 'location_id');
                $query->andWhere(['location' => $locations]);
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
            $dateCreated = (!empty($models->created_at) && $model->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($model->created_at)) : "-";
            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql)
                    ->bindValue(':itemid', $model->id)
                    ->bindValue(':status', array_search('In Transit', Item::$status));
            $data = $command->queryOne();
            $order = \app\modules\Orders\models\Order::findOne($model['ordernumber']);
            $createdBy = $data['firstname'] . ' ' . $data['lastname'];
            $store_number = (!empty($location->storenum)) ? $location->storenum:"";
            $name = $location->storename;
            $division = (isset($locationClassment) && $locationClassment != NULL) ? LocationParent::findOne($locationClassment->parent_id)->parent_name : 'Uncategorized';
            $phone = $location->phone;
            $excelDatas[] = [
                'model' => $_manufacturer->name . ' ' . $_model->descrip,
                'serial' => $model['serial'],
                'status' => (!empty($model['status']))?Item::$status[$model['status']]:"",
                'returntracking' => $order->returntracking,
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
        $objWriter->save('php://output');
    }

    private function _genReallocationReport($fileName, $dateRange = NULL, $divisionId) {
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
                ->innerJoin('lv_itemslog', 'lv_items.id = lv_itemslog.itemid')
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['lv_itemslog.status' => array_search('Transferred', Item::$status), 'lv_items.customer' => $customer->id])
                ->andWhere('DATE(lv_itemslog.created_at) between "' . trim($startDate) . '" and "' . trim($endDate) . '"')->groupby('lv_items.id');
        if(!empty($divisionId)){
                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $divisionId])->asArray()->all(), 'location_id');
                $query->andWhere(['location' => $locations]);
        }        
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
            $destination = $output;
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
            $origin = $output;
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
        $objWriter->save('php://output');
    }

    private function _genDivisionReport($fileName, $location) {
        $objPHPExcel = new \PHPExcel();
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        //foreach ($locations as $key => $location) {
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        if (!empty($location)) {
            $location = LocationClassment::find()->where(['parent_id' => $location])->one();
        }
        $div_store_locations = ArrayHelper::getColumn(Location::find()->where(['customer_id'=>$customer->id, 'storenum'=>'DIV'])->asArray()->all(), 'id');
        $sheetName = (!empty($location)) ? LocationParent::findOne($location->parent_id)->parent_name : 'Uncategorized';
        $sql = "SELECT lv_items.location, SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
        $sql .= "SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
        $sql .= "lv_models.id, lv_manufacturers.name, lv_models.descrip,lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname, SUM(storenum='DIV') as qty_division, SUM(storenum<>'DIV') as qty_location, SUM(confirmed=1) as qty_confirmed FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories
                     ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id INNER JOIN lv_locations ON lv_items.location=lv_locations.id";
        $sql .= (!empty($location)) ? " INNER JOIN lv_locations_classments ON lv_items.location=lv_locations_classments.location_id WHERE lv_items.customer=" . $customer->id . " AND lv_locations_classments.parent_id=" . $location->parent_id . "" : " WHERE lv_items.customer=" . $customer->id . " AND location NOT IN (SELECT DISTINCT(location_id) FROM lv_locations_classments)";
        $sql .= " GROUP BY lv_items.model ORDER BY name, descrip";
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
            $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
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
        $objWriter->save('php://output');
    }

    private function _genInventoryReport($fileName, $divisionId) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $objPHPExcel = new \PHPExcel();
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
        if(empty($divisionId)){
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
        }
        
        /************ Locations *********/
        $index = empty($divisionId) ? 1: 0;
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($index);
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
        $query = LocationClassment::find()
                ->innerJoin('lv_locations', 'lv_locations.id = lv_locations_classments.location_id')
                ->where(['customer_id' => $customer, 'lv_locations.deleted' => 0])
                ->groupBy('lv_locations.id');
        if(!empty($divisionId)){
            $query->andWhere(['parent_id' => $divisionId]);
        }
        $activeLocationClass = $query->all();
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
                
        /************** Inventory *************/
        $index = empty($divisionId) ? 2: 1;
        $query = Item::find()->where(['customer' => $customer]);
        if(!empty($divisionId)){
            $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $divisionId])->asArray()->all(), 'location_id');
            $query->andWhere(['location' => $locations]);
        }
        $query->groupBy('model');
        $items = $query->all();
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($index);
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
        
        /********************** Closed Locations ***********************/
        $index = empty($divisionId) ? 3: 2;
        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($index);
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
        
        $query = LocationClassment::find()
                ->innerJoin('lv_locations', 'lv_locations.id = lv_locations_classments.location_id')
                ->where(['customer_id' => $customer, 'lv_locations.deleted' => 1])
                ->groupBy('lv_locations.id');
        if(!empty($divisionId)){
            $query->andWhere(['parent_id' => $divisionId]);
        }
        $closedLocations = $query->all();        
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
        
        /************************ Connection Details **********/
        $index = empty($divisionId) ? 4: 3;
                $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex($index);
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
        
//        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
//        $customer = Customer::findOne($customer);
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
        $objWriter->save('php://output');
    }

    private function _genPdfServiceReport($isClose, $divisionId) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $query = Item::find()
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                ->andWhere('status < ' . array_search('Shipped', Item::$status));
        if ($isClose) {
            $query = Item::find()
                    ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                    ->innerJoin('lv_salesorders', '`lv_items`.`ordernumber` = `lv_salesorders`.`id`')
                    ->where(['customer' => $customer->id, 'lv_salesorders.ordertype' => 2])
                    ->andWhere('status >= ' . array_search('Shipped', Item::$status));
        }

        $models = $query->all();
        $pdfDatas = array();
        foreach ($models as $model) {
            $_model = Models::findOne($model->model);
            $_manufacturer = Manufacturer::findOne($_model->manufacturer);
            $location = Location::findOne($model->location);
            $address = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            if ($location != NULL) {
                $locationClassment = LocationClassment::find()->where(['location_id' => $location->id])->one();
            }
            $dateCreated = (!empty($models->created_at) && $model->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($model->created_at)) : "-";
            $sql = 'SELECT lv_users.* FROM `lv_itemslog` inner join lv_users on lv_users.id = lv_itemslog.userid where itemid = :itemid and status = :status';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql)
                    ->bindValue(':itemid', $model->id)
                    ->bindValue(':status', array_search('In Transit', Item::$status));
            $data = $command->queryOne();
            $createdBy = $data['firstname'] . ' ' . $data['lastname'];
            $store_number = (!empty($location->storenum)) ? $location->storenum : "";
            $name = $location->storename;
            $order = \app\modules\Orders\models\Order::findOne($model['ordernumber']);
            $division = (isset($locationClassment) && $locationClassment != NULL) ? LocationParent::findOne($locationClassment->parent_id)->parent_name : 'Uncategorized';
            $phone = $location->phone;
            $pdfDatas[] = [
                'model' => $_manufacturer->name . ' ' . $_model->descrip,
                'serial' => $model['serial'],
                'origin' => $address,
                'store_number' => $store_number,
                'name' => $name,
                'status' => (!empty($model['status']))?Item::$status[$model['status']]:"",
                'returntracking' => $order->returntracking,
                'tagnum' => $model['tagnum'],
                'division' => $division,
                'phone' => $phone,
                'created_at' => $dateCreated,
                'created_by' => $createdBy
            ];
        }
        return $pdfDatas;
    }

    private function _genPdfReallocationReport($dateRange = NULL, $divisionId) {
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
                ->innerJoin('lv_itemslog', 'lv_items.id = lv_itemslog.itemid')
                ->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
                ->where(['lv_itemslog.status' => array_search('Transferred', Item::$status), 'lv_items.customer' => $customer->id])
                ->andWhere('DATE(lv_itemslog.created_at) between "' . trim($startDate) . '" and "' . trim($endDate) . '"')->groupby('lv_items.id');
        if(!empty($divisionId)){
                $locations = ArrayHelper::getColumn(LocationClassment::find()->where(['parent_id' => $divisionId])->asArray()->all(), 'location_id');
                $query->andWhere(['location' => $locations]);
        }
        $models = $query->all();
        $pdfDatas = array();
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
            $destination = $output;
            $itemLog = Itemlog::find()->where(['itemid' => $model->id, 'status' => array_search('Transferred', Item::$status)])->one();
            $location = Location::findOne($itemLog['locationid']);
            $output = '';
            if (!empty($location->storenum))
                $output .= "Store#: " . $location->storenum . " ";
            if (!empty($location->storename))
                $output .= $location->storename.' ';
            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
            $user = User::findOne($itemLog->userid);
            $modelName = $_manufacturer->name . ' ' . $_model->descrip;
            $serialNumber = $model['serial'];
            $origin = $output;
            $dateTransferred = (!empty($itemLog->created_at) && $itemLog->created_at != "0000-00-00 00:00:00") ? date('F d, Y g:i a', strtotime($itemLog->created_at)) : "-";
            $createdBy = $user->firstname . ' ' . $user->lastname;
            $pdfDatas [] = [
                'model' => $modelName,
                'serial' => $serialNumber,
                'origin' => $origin,
                'destination' => $destination,
                'created_at' => $dateTransferred,
                'created_by' => $createdBy
            ];
        }
        return $pdfDatas;
    }

    private function _genPdfDivisionReport($location) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        //foreach ($locations as $key => $location) {
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
        $sql .= " GROUP BY lv_items.model ORDER BY name, descrip";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $divisionReportDatas = $command->queryAll();
        $pdfDatas = array();
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
//            $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
//            $inStocQty = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id'], 'location'=>$div_store_locations])->count();
//            $inProgress = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id']])->andWhere(['not', ['location'=>$div_store_locations]])->count();
//            $shippedQty = Item::find()->where(['customer'=>$customer->id, 'model'=>$model['id'], 'confirmed'=>1])->count();
//            $totalQty = $inStocQty + $inProgress + $shippedQty;
            $pdfDatas[] = [
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
        return $pdfDatas;
    }

    private function _genPdfInventoryReport($divisionId) {
        $customer = UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
        $customer = Customer::findOne($customer);
        $categories = Category::find()->innerJoin('lv_models', '`lv_models`.`category_id` = `lv_categories`.`id`')
                ->innerJoin('lv_items', '`lv_items`.`model` = `lv_models`.`id`')
                ->where(['customer' => $customer->id])
                ->groupBy('`lv_models`.`category_id`')
                ->orderBy('categoryname')
                ->all();
        $pdfDatas = array();
        foreach ($categories as $key => $category) {
            $sheetName = $category->categoryname;
            $sql = "SELECT lv_items.location, SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "') as instock_qty, SUM(lv_items.status='" . array_search('In Progress', Item::$status) . "') as inprogress_qty,";
            $sql .= (isset($_location)) ? "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "') as shipped_qty," : "SUM(lv_items.status='" . array_search('Shipped', Item::$status) . "') as shipped_qty,";
            $sql .= (isset($_location)) ? " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR (lv_items.status='" . array_search('Shipped', Item::$status) . "' AND location='" . $_location->id . "')) as total, " : " SUM(lv_items.status='" . array_search('In Stock', Item::$status) . "' OR lv_items.status='" . array_search('In Progress', Item::$status) . "' OR lv_items.status='" . array_search('Shipped', Item::$status) . "') as total,";
            $sql .= " lv_models.id, lv_manufacturers.name, lv_models.descrip, lv_models.aei, lv_models.image_id, lv_departements.name as department, lv_categories.categoryname FROM lv_models INNER JOIN lv_items ON lv_models.id=lv_items.model INNER JOIN lv_categories ON `lv_models`.`category_id` = `lv_categories`.`id` LEFT JOIN lv_manufacturers ON lv_models.manufacturer=lv_manufacturers.id LEFT JOIN lv_departements ON lv_models.department=lv_departements.id
                                      WHERE lv_items.customer=" . $customer->id . " AND lv_models.category_id=" . $category->id . " GROUP BY lv_items.model ORDER BY name, descrip";
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $inventoryReports = $command->queryAll();
            foreach ($inventoryReports as $model) {
                $location = Location::findOne($model['location']);
                $store_number = (!empty($location->storenum)) ? "Store#: " . $location->storenum : "";
                $address = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
                $division = $sheetName;
                $name = $location->storename;
                $phone = $location->phone;
                $modelName = $model['name'] . ' ' . $model['descrip'];
                $inStocQty = $model['instock_qty'];
                $inProgress = $model['inprogress_qty'];
                $shippedQty = $model['shipped_qty'];
                $totalQty = $model['total'];
                $departMent = strtoupper($model['department']) . ' ' . ucfirst(strtolower($model['categoryname']));
                $pdfDatas[$sheetName][] = [
                    'model' => $modelName,
                    'name' => $name,
                    'store' => $store_number,
                    'address' => $address,
                    'division' => $division,
                    'phone' => $phone,
                    'stock_qty' => $inStocQty,
                    'stock_progress' => $inProgress,
                    'stock_shipped' => $shippedQty,
                    'total_qty' => $totalQty,
                    'department' => $departMent
                ];
            }
        }
        return $pdfDatas;
    }

}
