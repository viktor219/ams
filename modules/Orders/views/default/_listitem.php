<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Item;
use app\models\Itemsordered;
use app\modules\Orders\models\Order;
use app\models\Ordertype;
use app\models\Customer;
use app\models\Medias;
use app\models\Partnumber;
use app\models\Department;
use app\models\ItemHasOption;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['/orders/index']];
$this->params['breadcrumbs'][] = $this->title;

//Item::find()->where(['ordernumber'=>$order->id, 'status' => 1, 'model'=>4])->limit(0,12)->all()->delete();
$itemoption = ItemHasOption::find()->innerJoin('lv_model_options', '`lv_model_options`.`id` = `lv_item_has_option`.`optionid`')
								->where(['orderid'=>$order->id])
								->andWhere(['optiontype'=>1, 'parent_id'=>0])
								->one();
if($order->ordertype!==1)
	$currentCustomer = $order->customer_id;
else
	$currentCustomer = 4;

$customer = Customer::findOne($currentCustomer);

$instock = Item::find()->where(['status'=>4, 'customer'=>$currentCustomer])->count();
$picked = Item::find()->where(['ordernumber'=>$order->id, 'status' => array_search('Picked', Item::$status)])->count();

$delivercleaningitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
							->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
							->where(['ordernumber'=>$order->id, 'orderid'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'optionid' => [2,3], 'conditionid'=>4])
							->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'preowneditems'=>1, 'conditionid'=>4]);

$cleaningmodels = \yii\helpers\ArrayHelper::getColumn($delivercleaningitems->groupBy('model')->asArray()->all(), 'model');

$_countcleaningitems = $delivercleaningitems->count();

$delivertestingitems = Item::find()->leftJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
							->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')
							->where(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'orderid'=>$order->id, 'optionid' => [47,48]])
							->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'requiretestingreferb'=>1, 'conditionid'=>4])
							->orWhere(['ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status), 'conditionid'=>2])
							->andWhere(['not', ['model'=>$cleaningmodels]]);
							
$testingmodels = \yii\helpers\ArrayHelper::getColumn($delivertestingitems->groupBy('model')->asArray()->all(), 'model');

$_counttestingitems = $delivertestingitems->count();

$merged_cleaning_testing = array_merge($cleaningmodels, $testingmodels);

$delivertoshippingitems = Item::find()
								->where(['conditionid' => [1, 3, 4], 'ordernumber'=>$order->id, 'status'=>array_search('Picked', Item::$status)])
								->andWhere(['not', ['model'=>array_merge($merged_cleaning_testing)]]);
											
$_countshippingitems = $delivertoshippingitems->count();			

$_totalcount = $_countshippingitems + $_countcleaningitems + $_counttestingitems;
?>
<?php //LOAD REORDER FORM --->?>
<?php //echo $order->id;?>
<?= $this->render("_modals/_reorder", ['order'=>$order]);?>
<?= $this->render("_modals/_serials", ['order'=>$order, 'customer'=>$customer]);?>	
<?= $this->render("_modals/_refurbish", ['order'=>$order]);?>	
<?= $this->render("_modals/_editmodel", ['order'=>$order]);?>
<?= $this->render("_modals/_purchaseoptions", ['order'=>$order]);?>
<?= $this->render("_modals/_pickingconfirm");?>
<?= $this->render("_modals/_picklistready", ['order'=>$order]);?>
<div class="order-index">
<!-- Sales Order Dashboard -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-4 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?> <b>(<?php echo Ordertype::findOne(Order::findOne($order->id)->ordertype)->name;?>)</b>
					</h4>
				</div>
				<div class="col-md-8 vcenter text-right">
					<?php if($_totalcount!=0) : ?>
						<?= Html::a('<span class="glyphicon glyphicon-store"></span> Deliver Now', 'javascript:;', ['onClick'=>'confirmPicklistTurning('. $order->id .');', 'class' => 'btn btn-success', 'id'=>'pick-deliver-button']) ?>
					<?php endif;?>
				</div>
			</div>
		</div>
		<div class="panel-body">
		    <?= GridView::widget([
		        'dataProvider' => $dataProvider,
				'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
				'summary'=>'', 
		        'columns' => [
					[
						'label' => 'Thumbnail',
						'format'=>'raw',
						'value' => function($model) {
							$picture_id = Models::findOne($model->model)->image_id;
							$filename = Medias::findOne($picture_id)->filename;
							return Html::img(Yii::getAlias('@web').'/public/images/models/'. $filename, ['alt'=>'logo',  'onClick'=>'ModelsViewer('.$model->model.')',/*'onClick'=>'showPicture("' . Yii::getAlias('@web').'/public/images/models/'. $filename . '");',*/ 'style'=>'max-height:33px;']);
						}
					],
					[
						'class' => 'yii\grid\DataColumn',
						'format'=>'raw',
						'value' => function($model) {
							$_models = Models::findOne($model->model);
							$aei = $_models->aei;
							if(empty($aei))
								$aei = "-";
							return '<div style="line-height:40px;">' . $aei . '</div>';
						},
						'headerOptions' => ['style'=>'text-align:center'],
						'attribute' => 'partnumber',
						'label' => 'AEI#'
					],										
					[
						'label' => 'Item',
						'attribute' => 'model',
						'format' => 'raw',
						'value' => function($model) {
							return '<div style="line-height:40px;">' . Manufacturer::findOne(Models::findOne($model->model)->manufacturer)->name . ' ' . Models::findOne($model->model)->descrip . '</div>';
						}
					],
					[
						'label' => 'Department',
						'attribute' => 'model',
						'format' => 'raw',
						'value' => function($model) {
							return '<div style="line-height:40px;">' . Department::findOne(Models::findOne($model->model)->department)->name . '</div>';
						}
					],
					[
						'label' => 'Qty',
						'format' => 'raw',
						'value' => function($model) {
							$count = Itemsordered::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->one()->qty;
							$color = "#08c";
							if($count==0)
								$color = "red";
							return '<button class="btn btn-default" style="color:' . $color . '">' . $count . '</button>';
						}
					],
					[
						'label' => 'In-Stock',
						'format' => 'raw',
						'value' => function($model) {
							/*$order = Order::findOne($model->ordernumber);
							if($order->ordertype!==1)
								$customer = $order->customer_id;
							else 
								$customer = 4;
							$count = Item::find()->where(['model'=>$model->model, 'status'=>4, 'customer'=>$customer])->count();
							$color = "#08c";
							if($count==0)
								$color = "red";
							return '<button class="btn btn-default" style="color:' . $color . '">' . $count . '</button>';*/
						    $order = Order::findOne($model->ordernumber);
						    if($order->ordertype!=1)
						    	$customer = $order->customer_id;
						    else
						    	$customer = 4;
						    //echo $model->model, ' ', $order->ordertype;
						    $instock = Item::find()->where(['model'=>$model->model, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer])->count();
						    //add reserved items
							$instock += Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Reserved', Item::$status)])->count();
							if($instock==0) {
						    	$class = "btn btn-danger";
						    	$style = "color:#FFF;font-weight:bold;";
						    }else if($instock>0 && $instock<5) {
						    	$class = "btn btn-warning";
						    	$style = "color:#FFF;font-weight:bold;";						    	
						    }else {
						    	$class = "btn btn-default";
						    	$style = "color:#08c;font-weight:bold;";						    	
						    }
						    return '<button class="' . $class . '" style="' . $style . '" id="instock-count-button-' . $model->model . '">' . $instock . '</button>';
						}
					],
					[
						'label' => 'Picked',
						'format' => 'raw',
						'value' => function($model) {
							//$output = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status' => 6])->andWhere(['not', ['serial' => null]])->count();
							$output = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->andWhere(['>=', 'status', array_search('Picked', Item::$status)])->count();
							return '<button class="btn btn-default" id="picked-count-button-' . $model->model . '">' . $output . '</button>';
		    			}
					],
		            		[
								'class' => 'yii\grid\ActionColumn',
								'visibleButtons' => [
									'refurbish' => function ($model, $key, $index) {
										$order = Order::findOne($model->ordernumber);
										return ($order->ordertype!=3) ? true : false;
		    						}
									/*'options' => function ($model, $key, $index) {
										$order = Order::findOne($model->ordernumber);
										return ($order->ordertype==1) ? true : false;
									},								
									'readytopartialship' => function ($model, $key, $index) {
										$order = Order::findOne($model->ordernumber);
										return ($order->ordertype==3 && ) ? true : false;
									},
									'readytoship' => function ($model, $key, $index) {
										$order = Order::findOne($model->ordernumber);
										if($order->ordertype!==1)
											$customer = $order->customer_id;
										else
											$customer = 4;
										$instock = Item::find()->where(['model'=>$model->model, 'status'=>4, 'customer'=>$customer])->count();	
										$picked = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status' => array_search('Picked', Item::$status)])->count();										
										return ($order->ordertype==3 && $instock==0 && $picked > 0) ? true : false;
									}*/
								],
								'template'=>'{reorder} {pickbutton} {refurbish} {readytopartialship} {readytoship}',
								'contentOptions' => ['style' => 'width:200px;'],
								'controller' => 'orders',
								'buttons' => [									
								    'reorder' => function ($url, $model, $key) {
										$qty = Itemsordered::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->one()->qty;
										$_model = Models::findOne($model->model);
										$order = Order::findOne($model->ordernumber);
										if($order->ordertype!=1)
											$customer = $order->customer_id;
										else
											$customer = 4;
										$instock = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer])->count();
										$reorderquantity = 0;
										if($instock < $qty)
											$reorderquantity = $qty - $instock;
										else
											$reorderquantity = $_model->reorderqty;
										return Html::a('', 'javascript://', ['title' => Yii::t('app', 'ReOrder'),
													'class' => 'glyphicon glyphicon-shopping-cart btn btn-primary showreorder','data-toggle'=>'modal', 'data-target'=>'#reorder', 'id'=>$model->id . '||' . Manufacturer::findOne(Models::findOne($model->model)->manufacturer)->name . ' ' . Models::findOne($model->model)->descrip . '||' . $reorderquantity . '||' . $model->ordernumber . '||' . $model->model]);
								    },
								    'pickbutton' => function ($url, $model, $key) {
								    	$_model = Models::findOne($model->model);
								    	$order = Order::findOne($model->ordernumber);
								    	if($order->ordertype!=1)
								    		$customer = $order->customer_id;
								    	else
								    		$customer = 4;
								    	$instock = Item::find()->where(['model'=>$model->model, 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer])->count();
								    	$instock += Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Reserved', Item::$status)])->count();
										$countitemsmustbepicked = Itemsordered::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->one()->qty;
								    	//$countallitems = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->count();
								    	$countpickeditems = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])->andWhere(['>=', 'status', array_search('Picked', Item::$status)])->count();
								    	$countonlyitemsmustbepicked = Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model, 'status'=>array_search('Picked', Item::$status)])->count();
								    	if(($countpickeditems>=$countitemsmustbepicked) || ($countonlyitemsmustbepicked===$countitemsmustbepicked)){
								    		if($model->status > 6)
								    			$_btnstyle = "btn-success";
								    		else if($model->status == 6)
								    			$_btnstyle = "btn-warning";
											return '<button class="glyphicon glyphicon-ok btn '.$_btnstyle.'" id="pickbutton'. $model->id .'"></button>';
										}
								    	else {//pick button for items not yet picked.
								    		$order = Order::findOne($model->ordernumber);
								    		$_model = Models::findOne($model->model);
								    		//if($order->ordertype === 1 || $order->ordertype === 2 || $order->ordertype === 3) {//purchase order
								    			if($_model!==null && $_model->serialized==1)//serialized 
								    			{
								    				if($instock > 0)
								    					return '<button onClick="openSerialWindow(' . $order->id .', ' . $model->model . ');" class="glyphicon glyphicon-equalizer btn btn-info" id="pickbutton'. $model->model .'"></button>';
								    				else
								    					return '<button class="glyphicon glyphicon-equalizer btn btn-info" disabled></button>';
								    			}
								    			else
								    			{
								    				if($instock > 0) {
								    					//$url = \yii\helpers\Url::toRoute(['/orders/pick', 'id'=>$model->model, 'oid'=>$order->id]);
								    					$options = [
															'title' => 'Pick',
															'class' => 'btn btn-info',
															'type'=>'button',
															'onClick'=> "openNonSerializedModal(" . $model->id . ");", 
															'data-method' => 'post'
								    					];
														$url = 'javascript:;';
								    					return Html::a('<span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span>', $url, $options);
								    				}else {
								    					return '<button class="glyphicon glyphicon-equalizer btn btn-info" disabled></button>';
								    				}
								    				//
								    					
								    			}
								    		//}
								    	}
								    }/*,		
									'options' => function ($url, $model, $key) {
										$order = Order::findOne($model->ordernumber); 
										$url = 'javascript:;';
										$options = [
											'title' => 'Options',
											'class' => 'btn btn-warning',
											'type' => 'button',
											'onClick' => 'openOptionsModal('. $order->id .', ' . $model->model . ');'
										];
										return Html::a('<span class="glyphicon glyphicon-check" aria-hidden="true"></span>', $url, $options);											
									}*/,									
									'refurbish' => function ($url, $model, $key) {
										$order = Order::findOne($model->ordernumber); 
										$url = 'javascript:;';
										$options = [
											'title' => 'Refurbish',
											'class' => 'btn btn-warning',
											'type' => 'button',
											'onClick' => 'openRefurbModal('. $order->id .', ' . $model->model . ');'
										];
										return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);											
									},
									/*'readytopartialship' => function ($url, $model, $key) {
										$order = Order::findOne($model->ordernumber);
										$url = \yii\helpers\Url::toRoute(['/orders/markpickedtoship', 'id'=>$order->id, 'model'=>$model->model]);
										$options = [
											'title' => 'Ready To Partial Ship',
											'class' => 'btn btn-success',
											'type'=>'button',
										];
										return Html::a('<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>', $url, $options);											
									},	
									'readytoship' => function ($url, $model, $key) {
										$order = Order::findOne($model->ordernumber);
										$url = \yii\helpers\Url::toRoute(['/orders/markallpickedtoship', 'id'=>$order->id]);
										$options = [
											'title' => 'Ready To Ship',
											'class' => 'btn btn-default',
											'type'=>'button',
										];
										return Html::a('<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>', $url, $options);											
									},*/									
								],
							]
		        ],
		    ]); ?>
		</div>
    </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/pick-serial.js"></script>