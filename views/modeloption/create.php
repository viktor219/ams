<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ModelOption */

$this->title = 'Create Model Option';
$this->params['breadcrumbs'][] = ['label' => 'Model Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="model-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
