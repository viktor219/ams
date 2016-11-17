<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Receive Inventory';
$this->params['breadcrumbs'][] = ['label' => 'Receiving', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <?= $this->render('_form', [
        //'model' => $model,
    ]) ?>

</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/receiving.js"></script>