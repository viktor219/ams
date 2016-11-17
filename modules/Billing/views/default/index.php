<?php
use yii\widgets\ActiveForm;
$this->title = 'Billing';
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<?= $this->render("@app/modules/Orders/views/default/_modals/_customerdetails"); ?>
<!-- Billing Dashboard -->
<div class="panel panel-info">
    <div class="panel-heading">
        <div class="row vertical-align">
            <div class="col-md-7 vcenter">
                <h4><span class="glyphicon glyphicon-send"></span> Billing Overview</h4>
            </div>
            <div class="col-md-5 vcenter text-right">
                    <?php $form = ActiveForm::begin([
                                'action' => ['index'],
                                'method' => 'get',
                                //'options' => ['onkeypress'=>"return event.keyCode != 13;"]
                            ]); ?>
                        <div id="searchorder-group" class="pull-right top_search">
                                <div class="input-group">
                                        <span class="input-group-btn">
                                                <button class="btn btn-success" id="searchBillingBtn" type="button"><b style="color:#FFF;">?</b></button> 
                                        </span>
                                        <input type="search" placeholder="Search" id="searchBilling" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
                                </div>
                        </div>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div style="padding: 0 30px;">
        <div class="row" id="billing-main-gridview">
            <ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
                <li role="presentation" class="active col-sx-3"><a href="#billinghome" onClick="loadBilling('', '');" id="billing-tab-1" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                </li>
                <li role="presentation" class="col-sx-3"><a href="#billingpurchase" onClick="loadBilling('', 'purchase');" role="tab" id="order-tab-2" data-toggle="tab"  aria-expanded="false">Purchase</a>
                </li>
                <li role="presentation" class="col-sx-3"><a href="#billingservice" onClick="loadBilling('', 'service');" role="tab" id="order-tab-3" data-toggle="tab" aria-expanded="false">Service</a>
                </li>
                <li role="presentation" class="col-sx-3"><a href="#billingintegration" onClick="loadBilling('', 'integration');" role="tab" id="order-tab-4" data-toggle="tab" aria-expanded="false">Integration</a>
                </li> 
                <li role="presentation" class="col-sx-3"><a href="#billingwarehouse" onClick="loadBilling('', 'warehouse');" role="tab" id="order-tab-5" data-toggle="tab" aria-expanded="false">Warehouse</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" aria-labelledby="billing-all-tab">
                    <div id="billing-loaded-content">
                    </div>                        
                </div>
            </div>
        </div>
        <div class="row" id="billing-search-gridview" style="display:none;">
            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                    <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                        <li role="presentation" class="active"><a href="#billingsearch" id="billing-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="billing-results-count"><b>0</b></span>) </a>
                        </li>
                    </ul>
                    <!--<div id="loading-search" style="display:none;"><p><img src="<?php //echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>     -->
                    <div id="myTabContent" class="tab-content">    
                            <div role="tabpanel" class="tab-pane fade active in" id="billingsearch" aria-labelledby="home-tab">
                                <div id="billing-loaded-content-search"></div>
                            </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        loadBilling('', '');
    });
</script>
<script src="<?php echo Yii::$app->request->baseUrl; ?>/public/js/uc/billing.js"></script>
<!-- End -->

