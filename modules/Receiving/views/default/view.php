<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Vendor;
use app\models\Users;
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;

/* @var $this yii\web\View */
/* @var $model app\models\Purchase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = Itemspurchased::find()->where(['ordernumber'=>$model->id])->all();
$output = "";
//
foreach ($items as $item){
	$_model = Models::findOne($item->model);
	$_man = Manufacturer::findOne($_model->manufacturer);
	$output .= '<b>' . $item->qty . ' X ' . $_man->name . ' ' . $_model->descrip . ' ($' . $item->price . ') </b>';
	$output .= '<br/>';
}
?>
<?= $this->render("_modals/_receiveqtymodal", ['items'=>$items, 'model'=>$model]);?>
<div class="purchase-view">
<p>
	<a href="javascript:;" class="btn btn-info" id="ReceiveQtyBtn">Receive Quantity</a>
</p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
            	'attribute'=>'vendor_id',
				'value'=>Vendor::findOne($model->vendor_id)->vendorname
			],
            'shipping_company',
            'number_generated',
    		[
            	'attribute'=>'estimated_time',
    			'value'=>date('m/d/Y', strtotime($model->estimated_time))
    		],
            'trackingnumber',
            [ 
				'attribute'=>'requestedby',
				'value'=>Users::findOne($model->user_id)->firstname . ' ' . Users::findOne($model->user_id)->lastname
			],
    		[
    			'label'=>'Items',
				'format'=>'raw',
    			'value'=> $output
    		]
        ],
    ]) ?>

</div>