<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\Orders\models\Order;
use app\models\Ordertype;
use app\models\Models;
use app\models\Manufacturer;
use app\models\ModelOption;
use app\models\OrderPackageOptoin;
use app\models\Purchase;
use app\models\Customer;
use app\models\Location;
use app\models\Medias;
use app\models\Item;
use app\models\Itemsordered;
use app\models\Itemspurchased;
use app\models\Vendor;
use app\models\ShippingCompany;
use app\models\ShipmentMethod;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */

$_shipping_method = array();
$_shipping_company = array();
if(!$model->isNewRecord)
{
	$_shipping_method = ShipmentMethod::findOne($model->shipping_deliverymethod);
}

?>
<style>
	.tooltip
	{
		width: 200px;
	}
	#addCustomer .help-block
	{
		margin-bottom : 0;
	}
	.modal-footer {
	    padding: 0;
	    padding-top: 5px;
	    text-align: right; 
	    border-top: 1px solid #E5E5E5;
	}
</style>
<?= $this->render('_modals/_addcustomer', ['model'=>$model]);?>
<?= $this->render("_modals/_pdfmodal");?>
<?php //LOAD ADD MODEL FORM --->?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_newmodel");?>
<div class="row row-margin">			
<?php $form = ActiveForm::begin(['options' => ['action'=>['/orders/create'], 'id'=>'add-purchase-form']]); ?>
	<div class="col-lg-12 col-sm-6 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
						<div class="x_panel">
							<div class="x_title">
								<h2><i class="fa fa-level-down"></i><small> Step #1 : Items Details</small></h2>
								<ul class="nav navbar-right panel_toolbox">
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
								</ul>
								<div class="clearfix"></div>
							</div>
							<div class="x_content" style="margin:0;">
								<div class="" role="tabpanel" data-example-id="togglable-tabs">
									<div id="myTabContent" class="tab-content">					
						<?php if($items_requested!==null) :?>
								<select class="select2_purchase form-control" tabindex="-1" id="selectPurchase" name="purchase">
									<option selected="selected" value="">Select A Purchase</option>
							  		<?php foreach(Purchase::find()->all() as $purchase) :?>	
								  		<?php 
								  			$itemspurchased = Itemspurchased::find()->where(['ordernumber'=>$purchase->id])->one();
								  			$itemsoutput = "";
								  			$qty = (new \yii\db\Query())->from('{{%itemspurchased}}')
								  			->where(['ordernumber'=>$purchase->id])
								  			->sum('qty');
								  			$itemsoutput = (!empty($qty)) ? "Items: ($qty)" : "";	
								  			$___model = Models::findOne($itemspurchased->model);
								  			$___manufacturer = Manufacturer::findOne($___model->manufacturer);		
								  			$itemsoutput .= " " . $___manufacturer->name . ' ' . $___model->descrip;
								  			/*foreach ($itemspurchased as $itempurchased) 
								  			{
												 
								  			}*/
								  		?>
										<option value="<?php echo $purchase->id;?>">PO#: <?php echo $purchase->number_generated;?><?php ($purchase->vendor_id !==null) ? " Vendor: " . Vendor::findOne($purchase->vendor_id)->vendorname : "";?> <?php echo $itemsoutput;?></option>											
							  		<?php endforeach;?>
								</select>						
						<?php endif;?>
							<div class="form-group">
								<?php if($items_requested!==null) :?>
									<?php 
										//find model informations
										$__model = Models::findOne($item_requested->model);
										$__manufacturer = Manufacturer::findOne($__model->manufacturer);
										//find Order
										$order = Order::findOne($item_requested->ordernumber);
										//remember pricing
										$itemordered = Itemsordered::findOne(['customer'=>$item_requested->customer, 'ordertype'=>$order->ordertype, 'model'=>$item_requested->model]);
										$_lastpurchaseorder = Purchase::find()->where(['vendor_id'=>$__model->prefered_vendor])->orderBy('id DESC')->one();
										$_itempurchased = Itemspurchased::find()->where(['ordernumber'=>$_lastpurchaseorder->id, 'model'=>$__model->id])->orderBy('id DESC')->one();
									?>
										<div class="">
											<label for="selectItem"></label>
											<div id="entry1" class="clonedInput">
												<div class="row form-group">
													<div class="col-sm-1 edit-row-group">
														<button class="btn btn-warning edit_row" type="button"><span class="glyphicon glyphicon-edit"></span></button>
													</div>												
													<div class="col-sm-1 qty-group">
														<input class="select_ttl form-control" type="text" name="quantity[]" id="quantity" value="<?php echo $count_itemsrequested;?>" placeholder="Qty" readonly="readonly">
													</div>
													<div class="col-sm-8 desc-group">
														<input class="typeahead form-control input_fn" type="text" name="description[]" id="autocompleteitem_1" value="<?php echo $__manufacturer->name . " " . $__model->descrip;?>" placeholder="Select an Item" data-provide="typeahead" autocomplete="off" type="search" readonly="readonly">
														<input class="form-control input_h" type="hidden" name="modelid[]" id="autocompletevalitem_1" value="<?php echo $item_requested->model;?>"/>
													</div>
													<div class="col-sm-2 price-group">
														<div class="input-group">
															<div class="input-group-addon">$</div>
															<input class="form-control priceorder" type="text" name="price[]" id="price_1" value="<?php echo $_itempurchased->price;?>" placeholder="3.77">
														</div> 
													</div>
												</div>
											</div>
										</div>								
								<?php else : ?> 
									<?php if($model->isNewRecord) :?>
										<div class="">
											<label for="selectItem"></label>
											<div id="entry1" class="clonedInput">
												<div class="row form-group">
													<div class="col-sm-1 qty-group">
														<input class="select_ttl form-control" type="text" name="quantity[]" id="quantity" value="" placeholder="Qty">
													</div>
													<div class="col-sm-9 desc-group">
														<div class="input-group">
															<input class="typeahead form-control input_fn" type="text" name="description[]" id="autocompleteitem_1" value="" placeholder="Select an Item" data-provide="typeahead" autocomplete="off" type="search">
															<input class="form-control input_h" type="hidden" name="modelid[]" id="autocompletevalitem_1" />
															<span class="input-group-btn">
																<button class="btn btn-success input_sr" id="showRequestItem_1" data-toggle="tooltip" title="Having trouble finding your item? Request help." type="button" disabled>?</button>
															</span>
														</div>
													</div>
													<div class="col-sm-2 price-group">
														<div class="input-group">
															<div class="input-group-addon">$</div>
															<input class="form-control priceorder" type="text" name="price[]" id="price_1" value="" placeholder="3.77">
														</div> 
													</div>
												</div>
											</div>
										</div>
									<?php endif;?>
								<?php endif;?>
							</div>
						<?php if($items_requested===null) :?>
							<div class="row">
								<div class="col-sm-12">
									<div class="actions text-right">
										<button class="btn btn-success btn-xs" id="pbtnAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
										<button class="btn btn-success btn-xs" id="pbtnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
									</div>
								</div>
							</div>
						<?php endif;?>										
				</div>
			</div>
		</div>
	</div>
	<div class="row row-margin"></div>		
	<div class="x_panel">
		<div class="x_title">
			<h2><i class="fa fa-level-down"></i><small> Step #2: Purchase Details</small></h2>
			<ul class="nav navbar-right panel_toolbox">
				<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
			</ul>
			<div class="clearfix"></div>
		</div>
		<div class="x_content" style="margin:0;">
			<div class="" role="tabpanel" data-example-id="togglable-tabs">
				<div id="myTabContent" class="tab-content">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group" id="vendor-group">
								<div class="input-group">
									<span class="input-group-btn">
										<button type="button" class="btn btn-success btn-md" onClick="openVendorModal();"><span class="glyphicon glyphicon-plus"></span></button>
									</span>
									<input type="hidden" id="requiredvendor" class="form-control" value="0" >
									<select class="select2_vendor form-control" tabindex="-1" id="selectVendor" name="vendor">
										<option selected="selected" value="">Select A Vendor</option>
										<?php $vendors = Vendor::find()->all();?>
								  		<?php foreach($vendors as $vendor) :?>	
											<option value="<?php echo $vendor->id;?>" <?php // echo (!$model->isNewRecord && ($vendor->id==$model->vendor_id)) ? 'selected' : '';?><?php if(!empty($__model) && ($__model->prefered_vendor==$vendor->id)):?>selected<?php endif;?>><?php echo $vendor->vendorname;?></option>											
								  		<?php endforeach;?>
									</select>
								</div>
							</div>						
						</div>
						<div class="col-md-6">
							<div class="form-group" id="estimatedtime-group">	
								<div class="xdisplay_inputx form-group has-feedback" style="margin-left: 0;">
									<input class="form-control has-feedback-left shipbydatef" id="single_cal3" aria-describedby="inputSuccess2Status3" type="text" name="estimatedtime" placeholder="Estimated Time of Arrival" aria-describedby="inputSuccess2Status3">
									<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true" style="left:5px;"></span>
									<span id="inputSuccess2Status3" class="sr-only">(success)</span>
								</div>															
							</div>						
						</div>
					</div>		
					<div class="row">
						<div class="col-md-6">
							<div class="form-group" id="shippingcompany-group">					
								<select class="form-control" id="shippingcompany" name="shippingcompany">
									<option selected="selected" disabled="disabled" value="">Select Shipping Company</option>
									<?php foreach(ShippingCompany::find()->all() as $shippingcompany) :?>	
										<option value="<?php echo $shippingcompany->id;?>" <?php echo ($_shipping_method->shipping_company_id===$shippingcompany->id) ? 'selected' : '';?>><?php echo $shippingcompany->name;?></option>							  		
									<?php endforeach;?>
								</select>							
							</div>						
						</div>
						<div class="col-md-6">
							<div class="form-group" id="trackingnumber-group">					
								<input type="text" id="trackingnumber" class="form-control" name="trackingnumber" placeholder="Tracking#" >
							</div>						
						</div>
					</div>													
				</div>
			</div>
		</div>
	</div>							
	<!-- End Location -->
			<div class="row row-margin">
				<div class="col-md-12 text-right">
					<button onClick="redirectPurchase();" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
					<button class="btn btn-success" type="button" id="submitPurchaseOrder"><span class="glyphicon glyphicon-save"></span> Create</button>
				</div>
			</div>			
			</div>
		</div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
</div>
	<input type="hidden" name="_csrf" id="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
<?php //LOAD LOCATION ADD FORM --->?>
<?= $this->render("_modals/_addlocation");?>
<?php //LOAD REQUEST ITEM FORM --->?>
<?= $this->render("_modals/_requestitem");?>

<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing_create.js"></script>