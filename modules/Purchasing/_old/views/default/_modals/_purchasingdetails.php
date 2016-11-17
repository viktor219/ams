<?php

use app\models\Location;
use app\models\Itemcondition;
use yii\bootstrap\Html;
?>

<div class="modal fade" id='purchasingDetails'>
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <?= Html::beginForm(Yii::$app->request->baseUrl.'/purchasing/changestatus','POST', ['id' => 'changeStatusForm']); ?>
            <?= Html::hiddenInput('itemid','', ['id' => 'itemid']); ?>
            <?= Html::hiddenInput('model','', ['id' => 'modelid']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add <span id="title-loaded"></span> To Inventory</h4>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row row-margin">
                        <div id="entry1" class="clonedInput">
                            <div class="row form-group">
                                <div class="form-group col-sm-2 r_qty-group">
                                    <input class="rquantity form-control" type="number" name="quantity" id="quantity_1" value="" min="1" placeholder="Qty">
                                </div>
                                <div class="form-group col-sm-7 r_model-group">
                                    <select class="form-control receiving_location_select2_single" tabindex="-1" id="rselectLocation" name="location" >
                                        <option selected="selected" value="">Select A Location</option>
                                        <?php
                                        $locations = Location::find()->where(['customer_id' => 4])->all();
                                        foreach ($locations as $location) {
                                            $output = "";
                                            if (!empty($location->storenum))
                                                $output .= "Store#: " . $location->storenum . " - ";
                                            if (!empty($location->storename))
                                                $output .= $location->storename . ' - ';
                                            //
                                            $output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
                                            echo '<option value="' . $location->id . '">' . $output . '</option>';
                                        }
                                        ?>
                                    </select>																																										
                                </div>	
                                <div class="form-group col-sm-2 r_model-group clear_row_margin">
                                    <select class="form-control itemoption" name="conditionid" id="itemoption_1">
                                        <option value="">Select An Option</option>
                                        <?php foreach (Itemcondition::find()->all() as $option) : ?>
                                            <option value="<?php echo $option->id; ?>" <?php if ($option->id == 4): ?>selected<?php endif; ?>><?php echo $option->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>														
                                </div>															
                                <div class="form-group col-sm-1 clear_serialized-group clear_row_margin" style="display:none;padding-left:5px;">
                                    <button class="btn btn-success clear_item_button" id="Clearbtn_1" type="button"><span class="glyphicon glyphicon-remove-circle"></span></button>
                                </div>																													
                            </div>													
                            <!--<div class="row form-group">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-9">
                                            <textarea class="comment form-control"  id="itemNote_1" placeholder="Add additional notes or instructions..." style="display: none;" rows="3" name="itemnotes[]" ></textarea>
                                    </div>	
                                    <div class="col-sm-1"></div>					
                            </div>	-->										
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
                <button type="submit" class="btn btn-success" ><?php echo Yii::t('app', 'Save'); ?></button>
            </div>
            <?= Html::endForm(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>