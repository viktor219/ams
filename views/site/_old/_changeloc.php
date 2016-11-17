<div class="row">
    <div class="col-md-12 col-sm-12">
        <ul>
            <li><b>Serial: </b><?= $serial; ?></li>
            <li><b>Current Location: </b><?= $curr_location; ?></li>
        </ul>
    </div>
    <div class="col-md-12">
        <?= yii\bootstrap\Html::dropDownList('location', $location->id, $locData, ['id' => 'choose_location', 'prompt'=>'-Choose Location-', 'style' => 'width: 80%']);?>
    </div>
    <?= yii\helpers\Html::hiddenInput('itemid', $itemid); ?>
</div>