<?php 
use app\modules\Orders\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Item;
use app\models\Customer;
use app\models\ShipmentBoxDetail;
use app\models\Itemsordered;
use app\models\Ordertype;
use app\models\Shipment;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

	$this->title  = "Create Shipment";
	
	$this->params['breadcrumbs'][] = ['label' => 'Shipping', 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	
$_shipment = Shipment::find()->where(['orderid'=>$model->id])->one();
$shipmethod = ShipmentMethod::findOne($_shipment->shipping_deliverymethod);
$_company = ShippingCompany::findOne($_method->shipping_company_id);
if($shipmethod->shipping_company_id===1)
{
	$ups = new \Ups\Entity\Service;
	$ups->setCode($shipmethod->_value);	
	$__shipping_method = $ups->getName();
}
else if($shipmethod->shipping_company_id===3) //Waiting DHL issues solved
{}
else
{
	$__shipping_method = $shipmethod->_value;
}
$_delivery_method = $_company->name . ' ' . $__shipping_method;

$number_items = Itemsordered::find()->where(['ordernumber'=>$model->id])->sum('qty');
$numbers_items_readytoship = Item::find()->where(['status'=>array_keys(Item::$shippingstatus), 'ordernumber'=>$model->id])->count();
$readypercentage = ($number_items != 0) ? ($numbers_items_readytoship / $number_items) * 100 : 0;
$readypercentage = round($readypercentage, 2); 
?>	
<?= $this->render("_modals/_showdimensionsmodal");?>
<style>
	thead td, tbody td {
		text-align: left; 
	}
	
	#shipping-details-gridview-parent .panel-info>.panel-heading {
		color: #31708f;
		background-color: #EEE;
		border-color: #FFF;
	}
	#shipping-details-gridview-parent .panel-info
	{
		border-color: #DDD;
		border-width: 1px;
		box-shadow: 0 0 3px #ccc;
	}
</style>
<div class="inprogres-index">
<!-- Sales Order Dashboard -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-8 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span> <?= Html::encode($this->title) ?> 
					</h4>
				</div>
				<div class="col-md-4 vcenter text-right">
				<?php 
					$options_top_1 = ['class' => 'btn btn-primary'];
					if(Item::find()->where(['status'=>array_search('In Shipping', Item::$status), 'ordernumber'=>$model->id])->count() > 0) 
						$options_top_1['disabled']=true;
					$options_top_2 = ['class' => 'btn btn-success'];
					if(Item::find()->where(['status'=>array_search('In Shipping', Item::$status), 'ordernumber'=>$model->id])->count() > 0) 
						$options_top_2['disabled']=true;					
				?>
					<?= Html::a('<span class="glyphicon glyphicon-print"></span> Print Packing List', ['/shipping/printpackinglist', 'id'=>$model->id], $options_top_1) ?>
					<?= Html::a('<span class="glyphicon glyphicon-store"></span> Create Shipment', 'javascript:;', $options_top_2) ?>				
				</div>
			</div>
		</div>
		<div class="panel-body" id="shipping-details-gridview-parent">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="row vertical-align">
						<div class="col-md-6 vcenter" style="font-weight:bold;">
							Order Type: <?php echo Ordertype::findOne($model->ordertype)->name;?> <span style="color: #508caa;">SO# : <?php echo $model->number_generated;?></span> Delivery Method :<?php echo $_delivery_method;?> <span style="color: #508caa;"><?php echo $readypercentage;?>% complete</span>
						</div>
						<div class="col-md-6 vcenter text-right"></div>
					</div>
				</div>
				<div class="panel-body">
					<?= GridView::widget([
						'dataProvider' => $dataProvider,
						'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
						'summary'=>'', 
						'showHeader'=>false,
						'columns' => [
							[
								'attribute'=>'model',
								'format'=>'raw',
								'value'=>function ($model) { 
									$_model = Models::findOne($model->model);
									$_manufacturer = Manufacturer::findOne($_model->manufacturer);	
									$customer = Customer::findOne($model->customer);
									$nb_ship_model = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_keys(Item::$shippingallstatus)])->count();
									$nb_inshipping_model = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_keys(Item::$shippingstatus)])->count();
									$nb_ship_model_printed = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_keys(Item::$shippingstatus), 'labelprinted'=>1])->count();
									$has_box_configuration = ShipmentBoxDetail::find()->where(['orderid'=>$model->ordernumber, 'modelid'=>$model->model])->count();
									$_nb_ship_model_printed = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_keys(Item::$shippingallstatus), 'labelprinted'=>1])->count();
									$_print_label_output = ($customer->requirelabelbox) ? Html::a('<span class="glyphicon glyphicon-print"></span> Print Labels', ['printalllabel', 'order'=>$model->ordernumber, 'model'=>$_model->id], ['class' => ($nb_ship_model !== $_nb_ship_model_printed) ? 'btn btn-xs btn-info' : 'btn btn-xs btn-success']) : '';	
									$_box_config_button = (strpos(strtolower($__shipping_method), 'freight') === false) ? Html::a(($has_box_configuration) ? '<span class="glyphicon glyphicon-edit"></span> Weight & Dimensions' : '<span class="glyphicon glyphicon-plus"></span> Weight & Dimensions', 'javascript:;', ['class' => ($has_box_configuration) ? 'btn btn-xs btn-warning' : 'btn btn-xs btn-info', 'onClick'=>(!$has_box_configuration) ? 'openBoxConfigModal('. $model->ordernumber .', '. $_model->id .', 1)' : 'openBoxConfigModal('. $model->ordernumber .', '. $_model->id .', 2)']) : '';
									$_readytoship_button = ($nb_inshipping_model==0) ? Html::a('<span class="glyphicon glyphicon-ok-sign"></span> Ready To Ship', 'javascript:;', ['class' => 'btn btn-xs btn-success ready_to_ship']) : Html::a('<span class="glyphicon glyphicon-ok-sign"></span> Ready To Ship', ['readytoshipmodel', 'orderid'=>$model->ordernumber, 'modelid'=>$model->model], ['class' => 'btn btn-xs btn-info ready_to_ship']);
									return '<div class="row vertical-align"><div style="line-height:40px;font-size: 18px;" class="col-md-6 vcenter"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$_model->id.'">('. $nb_ship_model .') <b>' . $_manufacturer->name . ' ' . $_model->descrip . '</b></a></div><div class="col-md-6 vcenter text-right">'. $_print_label_output .' '. $_box_config_button .' '. $_readytoship_button .'</div></div>				
									<div class="model-loaded-content panel-collapse collapse out" mid="'.$_model->id.'" oid="'.$model->ordernumber.'" id="collapse'.$_model->id.'">
										<div class="panel-body"></div>
									</div>';
								}
							]
						],
					]); ?>
				</div>
			</div>
		</div>
    </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/shipping.js"></script>