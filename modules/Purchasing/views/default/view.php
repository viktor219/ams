<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\Vendor;
use app\models\Users;
use app\models\Item;
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;

/* @var $this yii\web\View */
/* @var $model app\models\Purchase */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Purchases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id])->all();
$items = ArrayHelper::getColumn($items, 'model');
$items = array_unique($items);
$output = "";
//
foreach ($items as $item){
	$_model = Models::findOne($item);
	$_man = Manufacturer::findOne($_model->manufacturer);
	$qty = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id, 'model'=>$item])->count();
	$itempurchased = Itemspurchased::findOne(['ordernumber'=>$model->id, 'model'=>$item]);
	$output .= '<b>' . $qty . ' X ' . $_man->name . ' ' . $_model->descrip . ' ($' . $itempurchased->price . ') </b>';
	$output .= '<br/>';
}
if(empty($item))
	$output = "No items to received.";

//
$trackingnumber = (!empty($model->trackingnumber)) ? $model->trackingnumber : '-';
?>
<?= $this->render("_modals/_receiveqtymodal", ['items'=>$items, 'model'=>$model]);?>
<div class="purchase-view">
<?php if(!empty($item)) :?>
	<p>
		<a href="javascript:;" class="btn btn-info" id="ReceiveQtyBtn">Receive Quantity</a>
	</p>
<?php endif;?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
            	'attribute'=>'vendor_id',
				'value'=>Vendor::findOne($model->vendor_id)->vendorname
			],
            //'shipping_company',
            'number_generated',
    		[
            	'attribute'=>'estimated_time',
    			'value'=>date('m/d/Y', strtotime($model->estimated_time))
    		],
            [	
				'attribute'=>'trackingnumber',
				'value'=> $trackingnumber
			],
            [ 
				'attribute'=>'requestedby',
				'value'=>Users::findOne($model->user_id)->firstname . ' ' . Users::findOne($model->user_id)->lastname[0] . '.'
			],
    		[
    			'label'=>'Items',
				'format'=>'raw',
    			'value'=> $output
    		]
        ],
    ]) ?>

</div>