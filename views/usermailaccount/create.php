<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UserMailAccount */

$this->title = 'New Mail Account';
$this->params['breadcrumbs'][] = ['label' => 'Mail Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-mail-account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
