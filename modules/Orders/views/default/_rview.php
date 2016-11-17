<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\Customer;
use app\models\Medias;
use app\models\Models;
use app\models\Ordertype;
use app\models\Manufacturer;
use app\models\Item;
use app\models\Itemsordered;
use app\models\ItemHasOption;
use app\models\ModelOption;
use app\models\OrderPackageOptoin;
use app\models\Department;
use app\models\Location;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'View Order';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//
	$items = Itemsordered::find()->where(['ordernumber'=>$model->id])->all();
	$content = "";
	foreach($items as $item)
	{
		$_model = Models::findOne($item->model);
		$manufacturer = Manufacturer::findOne($_model->manufacturer);
		$count_model = Itemsordered::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->one()->qty;
		$name = $manufacturer->name . ' ' . $_model->descrip;
		$findstatus = Item::find()->where(['ordernumber'=>$model->id, 'model'=>$item->model])->groupBy('status')->all();
		$status = array();
		foreach($findstatus as $stat)
		{
			$status[] = Item::$status[$stat->status];
		}
		//var_dump($status);
		$newline = "($count_model) $name <span style='color:#08c;'>(<b>" . implode(', ', $status) . "</b>)</span>";
		if($name!=="" && strpos($content, $newline) === false)
			$content .= $newline . "<br/>";
	}
	$number_items = (new \yii\db\Query())->from('{{%itemsordered}}')
								->where(['ordernumber'=>$model->id])
								->sum('qty');
//
$shipby = "";
if($model->shipby){
	$shipby = strtotime($model->shipby);
	//return date("M d g:ia",$shipby);
	$shipby = date('m/d/Y', $shipby);
}
//
$type_order = "";
if ($model->type == 1)  
	$type_order = '<span class="blue-small">Primary</span>';
if ($model->type == 3)
	$type_order = '<span class="green-small">Conversion</span>';
if ($model->type == 2) 
	$type_order = '<span class="red-small">Secondary</span>';

//
$location = Location::findOne($model->location_id);
if(!empty($location->storenum))
	$output .= "Store#: " . $location->storenum . " - ";
if(!empty($location->storename))
	$output .= $location->storename  . ' - ';
//
$output .= $location->address . " " . $location->city . " " . $location->state . " " . $location->zipcode;
//
if($model->ordertype==2)//repair ---> Item
	$items = Item::find()->where(['ordernumber'=>$model->id])->groupBy('model')->all();
else //others ---> ItemOrdered
	$items = Itemsordered::find()->where(['ordernumber'=>$model->id])->groupBy('model')->all();
$output_options = "";
foreach ($items as $item)
{
	$_mod = Models::findOne($item->model);
	$_manuf = Manufacturer::findOne($_mod->manufacturer);
	$output_options .= "<b>$_mod->descrip $_manuf->name</b> <br/>";
	$options = ItemHasOption::find()->where(['orderid'=>$model->id, 'itemid'=>$item->id])->all();
	$package_options= array();
	$cleaning_options= array();
	$testing_options= array();
	$config_options= array();
	if(!empty($item->package_optionid))
		$package_options[]=OrderPackageOptoin::findOne($item->package_optionid)->name;
	foreach ($options as $roption)
	{
		$option = ModelOption::findOne($roption->optionid);
		if($option->optiontype==1)
			$cleaning_options[] = $option->name;
		else if($option->optiontype==2)
			$config_options[] = $option->name;
		else if($option->optiontype==3)
			$testing_options[] = $option->name;
	}
//
	if(count($package_options)!==0)
	{
		$output_options .= "- Package Option : "
				. implode(', ', $package_options)
				. "<br/>";
	}
	if(count($cleaning_options)!==0)
	{
		$output_options .= "- Testing Option : "
				. implode(', ', $cleaning_options)
				. "<br/>";
	}
	if(count($config_options)!==0)
	{
		$output_options .= "- Configuration Option : "
				. implode(', ', $config_options)
				. "<br/>";
	
	}
	if(count($testing_options)!==0)
	{
		$output_options .= "- Testing Option : "
				. implode(', ', $testing_options)
				. "<br/>";
	}
	
	$output_options .= "<br/>";
	//echo $output_options;
}
$_media_customer = Medias::findOne(Customer::findOne($model->customer_id)->picture_id);
$_media_order = Medias::findOne($model->orderfile);

$items = Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'ordernumber'=>$model->id])->all();
$items = ArrayHelper::getColumn($items, 'model');
$items = array_unique($items);
?>
<?= $this->render("_modals/_receiveqtymodal", ['items'=>$items, 'model'=>$model]);?>
<div class="order-view">
<?php if(!empty($item)) :?>
	<p>
		<a href="javascript:;" class="btn btn-info" id="ReceiveQtyBtn">Receive Quantity</a>
	</p>
<?php endif;?>
<div class="customer-pic" style="text-align:center">
	<?= Html::img(Yii::getAlias('@web').'/public/images/customers/'.$_media_customer->filename, ['alt'=>'logo', 'height'=>'100px', 'width'=>'100px', 'style'=>'cursor:pointer;', 'onClick'=>'loadCustomerDetails(' . $model->customer_id . ');'])?>
</div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			[
				'label' => 'Number of items',
				'format' => 'raw',
				'value' => '<a tabindex="0" class="btn btn-default" id="item-popover_' . $model->id . '" role="button" data-toggle="popover" data-animation="true" data-html="true" data-placement="left" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . $content . '" rel="popover">' . $number_items . '</a>'
			],
            [
				'attribute' => 'location_id',
                'label' => Yii::t('app', 'Location'),
                'format'=>'html',
                'value' => $output . '&nbsp;<a class="btn-xs btn-success pull-right viewAllLocations" href="'.Yii::$app->request->baseUrl.'/location/view/?id='.$model->location_id.'&order='.$model->id.'">View</a>'
            ],
            [
				'attribute' => 'number_generated',
				'label' => Yii::t('app', 'Order Number'),
				'value' => $model->number_generated
			],
            'notes:ntext',
    		[
    		'label'=>'Type',
    		'format'=>'raw',
    		'value'=>($model->ordertype!==null) ? Ordertype::findOne($model->ordertype)->name : "",
    		],
			[
				'label'=>'Shipment Method',
				'format'=>'raw',
				'value'=>$type_order,
			],
			[
				'label'=>'Ship By',
				'value'=>$shipby,
			],
[
	'label'=>'Ordered date',
	'value'=>$model->created_at
]
           // 'modified_at',
        ],
    ]) ?>

</div>
