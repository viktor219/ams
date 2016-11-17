<?php

use yii\helpers\Html;

$this->title = 'New User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="user-create">
    <?=
    $this->render('_form', [
        'user' => $user,
        'departments'=>$departments,
		'customers'=>$customers,
		'profile_image'=>$profile_image
    ])
    ?>
</div>