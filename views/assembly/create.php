<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ModelAssembly */

$this->title = 'New Assembly';
$this->params['breadcrumbs'][] = ['label' => 'Inventory', 'url' => ['/inventory/index']];
$this->params['breadcrumbs'][] = ['label' => 'Model Assemblies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-assembly-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>