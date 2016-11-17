<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Location */

$name = "";

if(!empty($model->storenum))
	$name = "Store#" . $model->storenum;
else if(!empty($model->storename))
	$name = $model->storename;
else 
	$name = $model->address . " " . $model->address2 . " " . $model->city . " " . $model->state . " " . $model->zipcode;

$this->title = 'Update Location: ' . $name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="location-update">

    <?= $this->render('_form', [
        'model' => $model,
    	'customers'	=> $customers,
    	'parent_locations' => $parent_locations    	
    ]) ?>

</div>