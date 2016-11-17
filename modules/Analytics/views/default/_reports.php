<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Itemlog;
use app\models\Item;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Analytics';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .dropdown-menu:after{
        right: 45%;
    }
    .min-height-100{
        min-height: 100px;
    }
    ul.bar_tabs{
        background: none !important; 
        padding-left: 0 !important;
        margin: 15px 0px 0px 0px !important;
    }
    .bar_tabs .text_label,
    .bar_tabs .text_label:active{
        padding: 6px 10px; 
        background: white !important;
        min-width: 130px;
    }
    .glyphicon-download{
        color: white !important;
    }
    .grid-view td{
        font-weight: normal;
    }
</style>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/public/css/stack_index.css" rel="stylesheet">
<?php 
    $download_excel = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download Spreadsheet</div><div class="hide-desktop"><span class="fa fa-file-excel-o" aria-hidden="true"></span></div>';
    $download_pdf = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download PDF</div><div class="hide-desktop"><span class="fa fa-file-pdf-o" aria-hidden="true"></span></div>';
?>
<div class="Analytics-default-index">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="col-md-12 vcenter">
                    <div class="col-md-6 col-xs-6">
                        <h4>
                            <span class="glyphicon glyphicon-equalizer"></span>
                            Analytics
                        </h4>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <div class="pull-right">
                            <?php if (Yii::$app->user->identity->usertype == app\models\User::TYPE_CUSTOMER || Yii::$app->user->identity->usertype == app\models\User::REPRESENTATIVE): ?>
                                <a href="<?php echo Yii::$app->request->baseUrl; ?>/analytics/reportsettings" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span> Report Settings</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding: 15px 15px;">
            <div class="x_panel" style="border: none">
                <?= Html::hiddenInput('openDivisionId', isset($divisionIds[1]) ? $divisionIds[1]: "", ['id' => 'openservice-divisionId']); ?>
                <?= Html::hiddenInput('closeDivisionId', isset($divisionIds[2]) ? $divisionIds[2]: "", ['id' => 'closeservice-divisionId']); ?>
                <?= Html::hiddenInput('reallocDivisionId', isset($divisionIds[3]) ? $divisionIds[3]: "", ['id' => 'reallocation-divisionId']); ?>
                <?= Html::hiddenInput('divisionId', isset($divisionIds[4]) ? $divisionIds[4]: "", ['id' => 'division-divisionId']); ?>
                <?php /*Html::hiddenInput('invenDivisionId', isset($divisionIds[5]) ? $divisionIds[5]: "", ['id' => 'inventory-divisionId']); */?>
<!--                <div class="x_title">
                    <div class="row vertical-align">
                        <div class="col-md-12 vcenter">
                            <h4 class="pull-right report-customer-name" style="padding-right: 15px;">
                                <?php
                                //$cust = app\models\UserHasCustomer::find()->where(['userid' => Yii::$app->user->id])->one()->customerid;
                                //$customer_model = app\models\Customer::findOne($cust);
                                //echo $customer_model->companyname . ' Reports';
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>-->
                <div class="x_content" style="padding: 0 15px;">
                    <div class="row row-margin">
                        <div class="x_panel">
                            <div class="x_title">
                                <div class="col-md-5 col-xs-6">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Open Service Report
                                        <span class="report_label" style="font-size: 14px;">
                                            <?php
                                            $label = '';
                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Service (Open) Report', $_report_types)])->one();
                                            if ($reportSetting != NULL) {
                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
                                                $label = '(<strong>' . $reportOpts->name . ' Email<strong>)';
                                            }
                                            echo $label;
                                            ?>
                                        </span>
                                    </h4>                            
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <div class="col-md-8 col-xs-6 download-icons" style="padding: 0">
                                        <?php
//                                            $label = '';
//                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Service (Open) Report', $_report_types)])->one();
//                                            if ($reportSetting != NULL) {
//                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
//                                                $label = 'Report (' . $reportOpts->name . ')';
//                                            }
                                        ?>
                                        <!--                                            <div style="float: right; margin-top: 10px; margin-right: 10px">
                                                                                        <b><?php //echo $label;       ?></b>
                                                                                    </div>-->
                                        <?php $divisionId = isset($divisionIds[1])?$divisionIds[1]:""; ?>
                                        <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=service&isclose=0&division_id='. $divisionId, ['class' => 'btn-sm btn btn-success download-excel-link', 'style' => 'float: right']) ?>
                                        <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=service&isclose=0&division_id='. $divisionId, ['class' => 'btn-sm btn btn-success download-pdf-link', 'style' => 'float: right', 'target' => '_BLANK']) ?>
                                    </div>
                                    <div class="col-md-4 col-xs-6" style="padding: 0">
                                        <ul class="nav panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12 min-height-100" id="service-report-content">
                                    <div id="openservice-loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
                                    <?php /*$this->render('_servicereport', ['dataProvider' => $serviceReportDataP, 'customer' => $customer,]); */?>
                                </div>
                            </div>
                        </div>           
                    </div>
                    <div class="row row-margin">
                        <div class="x_panel">
                            <div class="x_title">
                                <div class="col-md-5 col-xs-6">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Closed Service Report
                                        <span class="report_label" style="font-size: 14px;">
                                            <?php
                                            $label = '';
                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Service (Closed) Report', $_report_types)])->one();
                                            if ($reportSetting != NULL) {
                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
                                                $label = '(<strong>' . $reportOpts->name . ' Email<strong>)';
                                            }
                                            echo $label;
                                            ?>
                                        </span>
                                    </h4>                            
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <div class="col-md-8 col-xs-6 download-icons" style="padding: 0">
                                        <?php
//                                            $label = '';
//                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Service (Closed) Report', $_report_types)])->one();
//                                            if ($reportSetting != NULL) {
//                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
//                                                $label = 'Report (' . $reportOpts->name . ')';
//                                            }
                                        ?>
                                        <!--                                            <div style="float: right; margin-top: 10px; margin-right: 10px">
                                                                                        <b><?php //echo $label;       ?></b>
                                                                                    </div>-->
                                       <?php $divisionId = isset($divisionIds[2])?$divisionIds[2]:""; ?> 
                                        <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=service&isclose=1&division_id='. $divisionId, ['class' => 'btn btn-sm btn-success download-excel-link', 'style' => 'float: right; color: white']) ?>
                                        <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=service&isclose=1&division_id='. $divisionId, ['class' => 'btn btn-sm btn-success download-pdf-link', 'style' => 'float: right; color: white', 'target' => '_BLANK']) ?>
                                    </div>
                                    <div class="col-md-4 col-xs-6" style="padding: 0">
                                        <ul class="nav panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12 min-height-100" id="serviceclose-report-content">
                                    <div id="closeservice-loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
                                    <?php /*$this->render('_servicecloserep', ['dataProvider' => $serviceReportCloseP, 'customer' => $customer]); */?>
                                </div>		                                       
                            </div>
                        </div>
                    </div>
                    <div class="row row-margin">
                        <div class="x_panel">
                            <div class="x_title">               
                                <div class="col-md-5 vcenter col-xs-6">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Reallocation Report
                                        <span class="report_label" style="font-size: 14px;">
                                            <?php
                                            $label = '';
                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Reallocation Report', $_report_types)])->one();
                                            if ($reportSetting != NULL) {
                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
                                                $label = '(<strong>' . $reportOpts->name . ' Email<strong>)';
                                            }
                                            echo $label;
                                            ?>  
                                        </span>
                                    </h4>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <div class="col-md-8 col-xs-6 download-icons" style="padding: 0">
                                        <?php
//                                            $label = '';
//                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Reallocation Report', $_report_types)])->one();
//                                            if ($reportSetting != NULL) {
//                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
//                                                $label = 'Report (' . $reportOpts->name . ')';
//                                            }
                                        ?>
                                        <!--                                            <div style="float: right; margin-top: 10px; margin-right: 10px">
                                                                                        <b><?php //echo $label;       ?></b>
                                                                                    </div>-->
                                        <?php $divisionId = isset($divisionIds[3])?$divisionIds[3]:""; ?>
                                        <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=reallocation&division_id='. $divisionId, ['class' => 'btn btn-sm btn-success download-excel-link reallocation_excel_link', 'style' => 'float: right; color: white']) ?>
                                        <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=reallocation&division_id='.$divisionId, ['class' => 'btn btn-sm btn-success download-pdf-link reallocation_pdf_link', 'style' => 'float: right; color: white', 'target' => '_BLANK']) ?>
                                    </div>
                                    <div class="col-md-4 col-xs-6" style="padding: 0">
                                        <ul class="nav panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div id="reportrange" class="pull-left" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        <span><?php echo date('F d, Y', strtotime(date('Y-m-d') . " -1 month")); ?> - <?php echo date('F d, Y'); ?></span> <b class="caret"></b>
                                    </div>
                                </div>	
                                <div class="col-md-12 col-sm-12 col-xs-12 min-height-100" style="margin-top: 10px" id="reallocation-report-content">
                                    <div id="reallocation-loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
                                    <?php /*$this->render('_reallocationreport', ['dataProvider' => $reAllocationReportDataP, 'customer' => $customer,]); */?>
                                </div>	

                            </div>
                        </div>
                    </div>
                    <div class="row row-margin">
                        <div class="x_panel">
                            <div class="x_title">
                                <div class="col-md-5 vcenter col-xs-6">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Division Report
                                        <span class="report_label" style="font-size: 14px;">
                                            <?php
                                            $label = '';
                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Division Report', $_report_types)])->one();
                                            if ($reportSetting != NULL) {
                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
                                                $label = '(<strong>' . $reportOpts->name . ' Email<strong>)';
                                            }
                                            echo $label;
                                            ?>                                        
                                        </span>
                                    </h4>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <div class="col-md-10 col-xs-6">
                                        <?php
//                                            $label = '';
//                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Division Report', $_report_types)])->one();
//                                            if ($reportSetting != NULL) {
//                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
//                                                $label = 'Report (' . $reportOpts->name . ')';
//                                            }
                                        ?>
                                        <!--                                            <div style="float: right; margin-top: 10px; margin-right: 10px">
                                                                                        <b><?php //echo $label;       ?></b>
                                                                                    </div>-->
                                    </div>
                                    <div class="col-md-2 col-xs-6" style="padding: 0">
                                        <ul class="nav panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12 min-height-100" id="division-report-content">
                                    <div id="division-loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
                                    <?php /*$this->render('_divisionreport', ['customer' => $customer, 'locations' => $_inventorylocations, '_location' => '']) */?>
                                </div>	
                            </div>
                        </div>
                    </div>
                    <div class="row row-margin">
                        <div class="x_panel">
                            <div class="x_title" style="margin: 0; border: none;">
                                <div class="col-md-5 col-xs-6">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Full Inventory Report
                                        <span class="report_label" style="font-size: 14px;">
                                            <?php
//                                            $label = '';
//                                            $reportSetting = app\models\ReportsSettings::find()->where(['userid' => $users, 'report_type_id' => array_search('Full Inventory Report', $_report_types)])->one();
//                                            if ($reportSetting != NULL) {
//                                                $reportOpts = \app\models\ReportsOptions::findOne($reportSetting->report_option_id);
//                                                $label = '(<strong>' . $reportOpts->name . ' Email<strong>)';
//                                            }
//                                            echo $label;
                                            ?>
                                        </span>
                                    </h4>                            
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <div class="col-md-8 col-xs-6 download-icons" style="padding: 0">
                                                                                    <div style="float: right; margin-top: 10px; margin-right: 10px">
                                                                                        <b><?php //echo $label;       ?></b>
                                                                                    </div>
                                        <?php $divisionId = isset($divisionIds[5])?$divisionIds[5]:""; ?>
                                        <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=inventory&division_id='. $divisionId, ['class' => 'btn btn-sm btn-success download-excel-link', 'style' => 'float: right; color: white']); ?>
                                        <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=inventory&division_id='.$divisionId, ['class' => 'btn btn-sm btn-success download-pdf-link', 'style' => 'float: right; color: white', 'target' => '_BLANK']); ?> 
                                    </div>
                                    <div class="col-md-4 col-xs-6" style="padding: 0">
                                        <ul class="nav panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#">Settings 1</a>
                                                    </li>
                                                    <li><a href="#">Settings 2</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                <?php /*    <?= $this->render('_inventoryreport', ['customer' => $customer, 'categories' => $categories, '_locations' => '']); */?> 
                                </div>		                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/datatables/src/DataTables.js"></script>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/stacktable.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/analytics.js"></script>