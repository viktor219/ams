<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Users;

/* @var $this yii\web\View */
/* @var $model app\models\UserMailAccount */

$user = Users::findOne($model->userid);
$this->title = $user->firstname . ' ' . $user->lastname;
$this->params['breadcrumbs'][] = ['label' => 'User Mail Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-mail-account-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
				'label'=>'Email',
				'value'=> $user->email
			],
            'password',
        ],
    ]) ?>

</div>
