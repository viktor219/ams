<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Update ' . $user->firstname . ' ' . $user->lastname . ' Data';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-update">

    <?= $this->render('_form', [
        'user' => $user,
    	'departments' => $departments,
		'customers'=>$customers,
    	'profile_image'=>$profile_image
    ]) ?>

</div>
