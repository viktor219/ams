<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Location;
use app\models\Itemlog;
use app\models\Item;
use yii\widgets\ActiveForm;

$this->title = 'Report Settings';
$this->params['breadcrumbs'][] = ['url' => ['/analytics/index'], 'label' => 'Analytics'];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .small-only td{
        width: 50% !important;
    }
</style>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/stacktable.js"></script>
<div class="panel panel-info" id="report-settings-page">
    <div class="panel-heading">
        <div class="vertical-align">
            <div class="col-md-10 col-xs-9">
                <h4 class="report-setting-label" style="margin: 0">
                    <span class="glyphicon glyphicon-cog"></span>
                    Report Settings 
                </h4>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>
    <div class="report-settings-content" style="padding: 0 20px; margin: 0">
<!--<div class="x_panel">-->
<!--            <div class="x_title">
                Settings
            </div>-->
    <!--<div class="x_content">-->
        <?php $form = ActiveForm::begin(['method' => 'post', 'id' => 'reportSettingForm']); ?> 
        <?php foreach ($report_types as $report_type): ?>
            <!--<div class="x_panel">-->
            <!--<div class="x_title">-->
            <!--<div class="col-md-3 col-xs-9" style="clear: both">-->
            <table class="table table-striped table-bordered stacktable" style="margin-top: 10px;">
                <tbody>
                    <tr>
                        <td style="width: 320px; vertical-align: middle" rowspan="2">
                            <?= $report_type->type; ?>
<!--                        <h4>
                            <span class="glyphicon glyphicon-equalizer"></span>
                            <?php /*$report_type->type; */?>
                        </h4>                         -->
                        </td>
            <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-9">-->
                <!--<div class="x_content">-->
<!--                <table class="table table-striped table-bordered stacktable" style="margin-top: 10px;">-->
<!--                    <thead>
                        <tr>-->
                        <!--<div class="col-md-12 col-xs-12" style="padding: 5px 0 5px 15px;">-->
                        <?php foreach ($report_options as $report_option): ?>
                            <td>
                        <!--<div class="<?php echo($division != '') ? 'col-md-2 col-sm-3 col-xs-3': 'col-md-4 col-sm-4 col-xs-4' ;?>">-->
                                <?= $report_option->name; ?>
                        <!--</div>-->
                            </td>
                        <?php endforeach; ?>
                        <?php if($division != ''): ?>
                            <td class="force">
                            <!--<div class="col-md-6 col-sm-3 col-xs-3">-->
                                Division (<?= $division; ?>) Only
                            <!--</div>-->
                            </td>
                        <?php endif; ?>
                            
                        <!--</div>-->
                        </tr>
<!--                    </thead>
                    <tbody>-->
                        <tr>
                            <td rowspan="2" style="display: none">
                                <h4>
                                    <!--<span class="glyphicon glyphicon-equalizer"></span>-->
                                    <?= $report_type->type; ?>
                                </h4> 
                            </td>
                        <!--<div class="col-md-12 col-xs-12">-->
                        <?php foreach ($report_options as $key => $report_option): ?>
                            <td>
                            <?php $rep_settings = app\models\ReportsSettings::find()->where(['userid' => Yii::$app->user->id, 'report_type_id' => $report_type->id])->one(); ?>
                            <!--<div class="<?php echo($division != '') ? 'col-md-2 col-sm-3 col-xs-3': 'col-md-4 col-sm-4 col-xs-4' ;?>">-->
                                <input type="radio" name="report_option_id[<?= $report_type->id ?>]" class="reportOptions<?php echo ($rep_settings->report_option_id == $report_option->id) ? ' has-checked' : ''; ?>" value="<?= $report_option->id; ?>" <?php echo ($rep_settings->report_option_id == $report_option->id) ? 'checked' : ''; ?>/>
                            <!--</div>-->
                            </td>
                            <?php if($key == 2 && $division != ''): ?>
                            <td>
                                <!--<div class="col-md-2 col-sm-3 col-xs-3">-->
                                    <input type="checkbox" name="is_division[<?= $report_type->id ?>]" class="isDivisionOnly" value="<?= $rep_settings->is_division; ?>" <?php echo ($rep_settings->is_division) ? 'checked' : ""; ?>/>
                                <!--</div>-->
                            </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!--</div>-->
                        </tr>
                    </tbody>
            </table>
            <!--</div>-->
            <!--</div>-->
        <?php endforeach; ?>
        <div style="text-align: center; padding-top: 15px; clear: both; padding-bottom: 15px">
            <input type="hidden" name="submit" />
            <div style="text-align: center">
                <input type="submit" class="btn btn-success" value="Save">
            </div>
        </div>    
        <?php ActiveForm::end(); ?>
    <!--</div>-->
    <!--</div>-->
</div>
</div>

<script>
    $(function () {
            $('#reportSettingForm table').stacktable({
                    myClass: 'table table-striped table-bordered'
            });
            $('.isDivisionOnly').bootstrapSwitch({size: 'mini'});
            $('.isDivisionOnly').on('switchChange.bootstrapSwitch', function(event, state) {
                var name = $(this).attr('name');
                if(state){
                    $('input[name="'+name+'"]').prop('checked', true);
                } else {
                    $('input[name="'+name+'"]').prop('checked', false);
                }
            });
            $('.reportOptions').each(function() {
            	if($(this).hasClass('has-checked'))
            		$(this).bootstrapSwitch({size: 'mini', state: true, radioAllOff: true});
            	else 
            		$(this).bootstrapSwitch({size: 'mini', radioAllOff: true});
			});      
    });
</script>