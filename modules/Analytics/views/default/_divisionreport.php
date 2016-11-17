<?php

//use Yii;
use yii\helpers\Html;
use app\models\Item;
use app\models\LocationParent;
//use yii\grid\GridView;
use yii\data\SqlDataProvider;
use app\components\Common;
?>
<style>
    .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th
    {
        font-size: 14px;
        text-align: center;
    }
    .table>thead>tr>th:nth-child(2),
    .table>tbody>tr>td:nth-child(2)
    {
        text-align: left;
    }
    .x_content
    {
        padding: 0px;
    }
</style>
<?= $this->render("@app/modules/Customers/views/default/_modals/_editcategorymodal"); ?>
<?php
$download_excel = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download Spreadsheet</div><div class="hide-desktop"><i class="fa fa-file-excel-o" aria-hidden="true"></i></div>';
$download_pdf = '<div class="hide-mobile"><span class="glyphicon glyphicon-download"></span> Download PDF</div><div class="hide-desktop"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>';
?>
<div class="row row-margin">
    <div class="col-md-12 col-sm-6 col-xs-12">
        <div class="x_panel" style="padding:0px;border:none;">
            <div class="x_content">
                <?php
                //var_dump($locations);
                //array_unshift($locations, null);
                //$div_store_locations = ArrayHelper::getColumn(Location::find()->where(['customer_id'=>$customer->id, 'storenum'=>'DIV'])->asArray()->all(), 'id');
                ?>
<?php foreach ($locations as $location): ?>
                    <div class="row" style="padding: 2px; padding-left: 10px; font-size: 14px; background: #73879C; color: #FFF; border-radius:5px; margin-bottom: 10px;">
                        <div class="col-md-5 vcenter col-xs-8" style="line-height: 30px;">
                            <span class="glyphicon glyphicon-lock"></span> <b><?php echo (!empty($location)) ? LocationParent::findOne($location->parent_id)->parent_name : 'Uncategorized'; ?></b>
                        </div>
                        <div class="col-md-7 vcenter text-right col-xs-4" style="margin-top:3px;"> 
                            <button style="margin: 0" class="btn btn-xs btn-info glyphicon glyphicon-plus" id="load-models-location-<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>" lid="<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>" pid="<?= $customer->id; ?>"></button>
                            <button class="btn btn-xs btn-info glyphicon glyphicon-minus" id="close-models-location-<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>" lid="<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>" style="display: none;margin: 0"></button>
                            <?php $myLocId = (!empty($location)) ? $location->parent_id : ''; ?>
                            <?= Html::a($download_pdf, Yii::$app->request->baseUrl . '/analytics/exportpdf?type=division&parentid=' . $myLocId, ['class' => 'btn btn-success download-pdf-link', 'style' => 'padding: 0px 5px; font-size: 12px; line-height: 21px; margin: 0px 0px;', 'target' => '_BLANK']) ?>
                            <?= Html::a($download_excel, Yii::$app->request->baseUrl . '/analytics/export?type=division&parentid=' . $myLocId, ['class' => 'btn btn-success download-excel-link', 'style' => 'padding:0px 5px; font-size: 12px; line-height: 21px; margin: 0px 0px;']) ?>
                        </div>
                    </div>
                    <div id="loaded-content-location-<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>" style="display: none;">
                        <div id="gridview-<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>">
                            <?php
                                $dataProvider = Yii::$app->common->getDivisionDataProvider($location);
                                echo $this->render('_divisionlocation_report', ['customer' => $customer, 'dataProvider' => $dataProvider, 'isDivisionOnly' => false]);
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!--<script src="<?php //echo Yii::$app->request->baseUrl;  ?>/public/js/uc/overview-ownstockpage.js"></script>-->