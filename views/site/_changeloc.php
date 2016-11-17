<?php 
	use yii\helpers\ArrayHelper;
	//var_dump($customer_locations);
	$locData = ArrayHelper::map($customer_locations, 'id', 'name');
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <ul>
            <li><b>Serial: </b><?= $model->serial; ?></li>
            <li><b>Current Location: </b><?= $current_location; ?></li>
        </ul>
    </div>
</div>
<div class="row">
	<div class="col-md-2"></div>
    <div class="col-md-10">
        <?= yii\bootstrap\Html::dropDownList('location', $location->id, $locData, ['id' => 'choose_location', 'prompt'=>'-Choose Location-', 'style' => 'width: 80%']);?>
    </div>
</div>
    <?= yii\helpers\Html::hiddenInput('itemid', $itemid); ?>