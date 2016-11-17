<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Itemlog */

$this->title = 'Create Itemlog';
$this->params['breadcrumbs'][] = ['label' => 'Itemlogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="itemlog-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
