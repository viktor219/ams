<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserMailAccount */

$this->title = 'Update User Mail Account: ' . ' ' . $model->userid;
$this->params['breadcrumbs'][] = ['label' => 'User Mail Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->userid, 'url' => ['view', 'id' => $model->userid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-mail-account-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
