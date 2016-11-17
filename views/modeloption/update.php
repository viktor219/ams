<?php

use yii\helpers\Html;
?>
<div class="model-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        	'models' => $models,
    		'model' => $model
    ]) ?>

</div>
