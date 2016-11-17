<?php

use yii\widgets\ActiveForm;

$this->title = "Merge Inventory";
$this->params['breadcrumbs'][] = ['url' => ['/inventory/index'], 'label' => 'Inventory'];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    ul.typeahead.dropdown-menu li a{
        text-align: left;
    }
</style>
<link href="<?php echo Yii::$app->request->baseUrl; ?>/public/css/tabs.css" rel="stylesheet">
<?php //LOAD REORDER FORM ---> ?>
<div class="order-index">
    <!-- Inventory Transfer Dashboard -->
    <div class="panel x_panel">
        <div class="x_title">
            <div class="row vertical-align">
                <div class="col-md-8 vcenter">
                    <h4>
                        <span class="glyphicon glyphicon-list-alt"></span>
                            <?= yii\helpers\Html::encode($this->title) ?>:
                            <?= $manufacturer->name . ' '. $model->descrip; ?>
                    </h4>
                    
                </div>
                <ul class="nav navbar-right panel_toolbox">
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
        <div class="x_content">
                <?php $form = ActiveForm::begin(['method' => 'post']); ?> 
                <?= yii\bootstrap\Html::hiddenInput('model_id', $model->id, ['id' => 'model_id', 'class' => 'merge_models_id']); ?>
            <div class="col-md-12">
<?= yii\helpers\Html::textInput('select_model', '', ['placeholder' => 'Select An Item', 'id' => 'search_model_item', 'class' => 'form-control']); ?>
            </div>
            <div class="col-md-12">
                <div id="model-item-details" style="display:none; margin-top: 10px">
                    <table width="100%" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Part Numbers</th>
                                <th>Category</th>
                                <th>Model</th>
                                <th>Inventory</th>
                                <th>Reorder Qty</th>
                                <th>Plt Qty</th>
                                <th>Strip Charac.</th>
                                <th>Checkit</th>
                                <th>Character Count</th>
                                <th>Department</th>
                                <th>Assembly</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div class="col-md-12" style="text-align: center">
                        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-share-alt"></span> Merge</button>
                    </div>
                </div>
            </div>
<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- End -->
<script>
    $(function () {
        $('#search_model_item').typeahead({
            onSelect: function (item) {
                var id = item.value;
                $.ajax({
                    url: jsBaseUrl + "/inventory/getmodeldetails?id=" + id,
                    beforeSend: function () {
                        $('#loading').show();
                        $('#search_model_item').attr('disabled', true);
                    },
                    success: function (data) {
                        $('#model-item-details tbody').append(data);
                        $("#model-item-details").show();
                        $('[id^="modelitem-popover_"], [id^="stock-popover_"]').on('click', function () {
                            var e = $(this);
                            var html = e.data('content');
                            //alert(html.length);
                            if (html.length == 0)
                            {
                                $.ajax({
                                    url: e.data('poload'),
                                    dataType: "json",
                                    beforeSend: function () {
                                        e.popover().popover('hide');
                                        $('#loading').show();
                                        e.prop('disabled', true);
                                    },
                                    success: function (data) {
                                        if (data.success)
                                        {
                                            e.attr('data-content', data.html);
                                            $('#loading').hide();
                                            e.prop('disabled', false);
                                            e.popover().popover('show');
                                        }
                                    }
                                });
                            }
                        });
                    },
                    complete: function () {
                        $('#loading').hide();
                        $('#search_model_item').attr('disabled', false);
                        $('#search_model_item').val('');
                        $('.remove-transfer').click(function () {
                            $(this).parents('tr').remove();
                            if (!$('#model-item-details tbody tr').length) {
                                $('#model-item-details').hide();
                            }
                        });
                    }
                });
                return 'test';
            },
            ajax: jsBaseUrl + "/inventory/searchmodels?id=" + $("#model_id").val(),
            items: 15,
            matcher: function (item) {
                return true;
            }
//            highlighter: function(item){
//                    var searchValue = this.query.trim();
//                    var searchWords = searchValue.split(" ");    
//                    var length = searchWords.length;
//                    var highlighString = item;
//                     for(var i=0;i<length;i++){
//                          var regex = new RegExp(searchWords[i], 'gi');
//                          var matcher = highlighString.match(regex , searchWords[i])
//                          if(matcher != null){
//                              matcher[0].replace("<strong>","");
//                              matcher[0].replace("</strong>","");
//                              highlighString = highlighString.replace(regex , "<strong>"+matcher[0]+'</strong>');
//                          } else {
//                              searchWords[i].replace("<strong>","");
//                              searchWords[i].replace("</strong>","");
//                              highlighString = highlighString.replace(regex , "<strong>"+searchWords[i]+'</strong>');
//                          }
//                    }
//                    return highlighString;
//            }
        });
    })
</script>