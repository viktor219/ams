<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Inventory */

$this->title = Yii::t('app', 'New Inventory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventory'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-create">
    <?= $this->render('_form', [
        'model' => $model,
    	'partnumber' => $partnumber,
    	'files' => $files
    ]) ?>
</div>
