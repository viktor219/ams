<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Purchasingitemrequest */

$this->title = 'Create Purchasingitemrequest';
$this->params['breadcrumbs'][] = ['label' => 'Purchasingitemrequests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchasingitemrequest-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
