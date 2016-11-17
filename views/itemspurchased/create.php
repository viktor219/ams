<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Itemspurchased */

$this->title = 'Create Itemspurchased';
$this->params['breadcrumbs'][] = ['label' => 'Itemspurchaseds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="itemspurchased-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
