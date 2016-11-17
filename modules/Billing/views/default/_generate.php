<?php 
	use yii\helpers\Html;
	use app\models\Ordertype;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\SystemSetting;
	use app\models\Item;
	use yii\helpers\ArrayHelper;
	
$_itemrow = 0;
$sum = 0;
$systemsetting = SystemSetting::find()->one();
//
if($shipping_method->shipping_company_id===1)
{
	$ups = new \Ups\Entity\Service;
	$ups->setCode($shipping_method->_value);
	$__shipping_method = $ups->getName();
}
else if($shipping_method->shipping_company_id===3) //Waiting DHL issues solved
{}
else
{
	$__shipping_method = $shipping_method->_value;
}

?>
<section style="overflow-y:auto;">
	<table id="header_pdf">
		<tr>
			<th style="width: 1000px;" class="align_left">
				<?php if(!empty($_media_customer->filename)) : ?>
					<?php $target_file = Yii::$app->request->baseUrl.'/public/images/customers/'.$_media_customer->filename; ?>
					<?php if (file_exists(\Yii::getAlias('@webroot'). '/public/images/customers/'.$_media_customer->filename)) : ?>		 
						<?= Html::img($target_file, ['alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:250px;max-height:100px;']); ?>	
					<?php else : ?>
						<span style="cursor:pointer;line-height: 40px;"><?php echo $customer->companyname;?></span>
					<?php endif;?>		 
				<?php else : ?>		 
					<span style="cursor:pointer;line-height: 40px;"><?php echo $customer->companyname;?></span>
				<?php endif;?>			
			</th>
			<!--<th class="align_right" scope="col" colspan="2"><b><?php //echo Ordertype::findOne($model->ordertype)->name;?> Order</b><th>-->
		</tr>
		<tr><td></td><td></td></tr>
		<tr><td></td><td></td></tr>
		<tr style="border-bottom:4px solid white;">
			<td></td>
			<td style="padding-right:25px;">Date</td>
			<td style="background: #eee;" class="align_right"><?php echo date_format(date_create($model->created_at), "m/d/Y");?></td>
		</tr>
		<tr style="border-bottom:4px solid white;">
			<td></td>
			<td style="padding-right:25px;">S.O. Number</td>
			<td style="background: #eee;" class="align_right"><?php echo $model->number_generated;?></td>
		</tr>
		<tr style="border-bottom:4px solid white;">
			<td></td>
			<td style="padding-right:25px;">Customer ID</td>
			<td style="background: #eee;" class="align_right"><?php echo $customer->code;?></td>
		</tr>		
	</table>
	<table id="sr_addresses">
		<tr>
			<th class="align_left">Vendor</th>
			<th style="width:500px;background:white"></th>
			<th class="align_left">Ship To</th>
		</tr>
		<tr>
			<!--<td><?php //echo $assetCustomer->firstname;?> <?php //echo $assetCustomer->lastname;?></td>-->
                        <td><?php echo $assetCustomer->companyname;?></td>
			<td></td>
			<td><?php echo $customer->firstname;?> <?php echo $customer->lastname;?></td>
		</tr>		
		<tr>
                        <td>3431 N. Industrial Dr.</td>
			<td></td>
			<td><?php echo $customer->companyname;?></td>
		</tr>	
		<tr>
			<td>Simpsonville, SC 29681</td>
			<td></td>
			<td><?php echo $location->address;?></td>
		</tr>		
		<tr>
			<td>info@assetenterprises.com</td>
			<td></td>
			<td><?php echo $location->city;?>, <?php echo $location->state;?>, <?php echo $location->country;?> <?php echo $location->zipcode;?></td>
		</tr>	
		<tr>
			<td>864.331.8678</td>
			<td></td>
			<td><?php echo $location->phone;?></td>
		</tr>							
	</table>
	<table id="shipping_methods">
		<tr>
			<th>Ship Via</th>
			<th>Shipping Method</th>
			<th>Payment Terms</th>
			<th>Delivery Date</th>
		</tr>
		<tr>
			<td><?php echo (!empty($shipping_company)) ? $shipping_company->name : '-';?></td>
			<td><?php echo (!empty($__shipping_method)) ? $__shipping_method : '-';?></td>
			<td>-</td>
			<td><?php echo date_format(date_create($model->shipby), "m/d/Y");?></td>
		</tr>	
	</table>
	<table id="products" style="margin-bottom:120px;">
		<tr>
                    <th class="align_left">Qty</th>
			<th class="align_left"></th>
			<th class="align_left">Product Name / Description</th>
<!--			<th class="align_left">Shipped</th>
                        <th style="text-align: center">This Shipment</th>
			<th class="align_left">Shipping</th>-->
			<th class="align_left">Unit Price</th>
			<th class="align_left">Total</th>
		</tr>
		<?php foreach($items as $index => $item) :?>
                            <?php if($item->model): ?>
				<?php 
					$_model = Models::findOne($item->model);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
                                        $isSerialized = false;
					if($_model->serialized)
					{
                                            $isSerialized = true;
						//find all serialized items 
//						$items = Item::find()->where(['ordernumber'=>$model->id, 'model'=>$_model->id])->asArray()->all();
					}
                                        $itemordered = app\models\Itemsordered::find()->where(['model' => $_model->id, 'ordernumber' => $model->id])->one();
					$total_price = $itemordered->qty * $itemordered->price;
                                        $itemModels = Item::find()->where(['model' => $_model->id, 'status' => array_search('Ready to Invoice', Item::$status), 'ordernumber' => $model->id]);
				?>
				<tr <?php echo ($index % 2 != 0) ? 'class="pair-row"' : '';?>>
					<td style="text-align: left; padding-left: 15px"><?php echo $itemModels->count(); ?></td>
					<td></td>
					<td style="text-align: left;padding-left:15px;"><?php echo $_manufacturer->name . ' ' . $_model->descrip;?> <?php if($isSerialized) :?>(<b><?php echo implode(', ', ArrayHelper::getColumn($itemModels->all(), 'serial'));?></b>)<?php endif;?></td>
<!--					<td style="text-align: center;padding-left: 15px"><?php //echo Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('Shipped', Item::$status), 'model'=>$_model->id])->count();?></td>
                                        <td style="text-align: center;padding-left: 15px">
                                           <?php /* <?= app\models\ShipmentsItems::find()->innerJoin('lv_items', 'lv_items.id = lv_shipments_items.itemid')->where(['shipmentid' => $shipment->id, 'lv_items.model' => $_model->id])->count(); ?> */ ?>
                                        </td>
					<td style="text-align: center;padding-left: 15px">-</td>-->
					<td style="text-align: left;padding-left: 15px"><?php echo $itemordered->price;?></td>
					<td style="text-align: left;padding-left: 15px"><?php echo number_format($total_price, 2);?></td>
				</tr>
				<?php $sum+=$total_price;?>
                            <?php endif; ?>
		<?php endforeach;?>
	</table>
	<table id="products_resume"  style="margin-bottom:210px;">
		<tr style="border:none;">
			<th style="width:600px;" class="align_left">Notes and Instructions<div><?php echo $model->notes;?></div></th>
			<th style="background: none;width:350px;"></th>
			<th style="background: none;width:60px;"></th>
			<th style="background: none;width:100px;"></th>
		</tr>
		<tr class="no_border">
			<td rowspan="7" valign="top"></td>
			<td></td>
			<td>Subtotal</td>
			<td>$ <span><?php echo number_format($sum, 2);?></span></td>
		</tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>Discount</td>
		    <td style="text-align: center;">-</td>
		 </tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>Sales Tax Rate</td>
		    <td>% <span><?php echo number_format($systemsetting->sales_taxerate, 2);?></span></td>
		 </tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>Sales Tax</td>
		    <td>$ <span><?php echo number_format(($sum*$systemsetting->sales_taxerate)/100, 2);?></span></td>
		 </tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>Other Cost</td>
		    <td style="text-align: center;">-</td>
		 </tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>S & H</td>
		    <td style="text-align: center;">-</td>
		 </tr>
		 <tr class="no_border">
		 	<td></td>
		    <td>Sub Total</td>
		    <td>$ <span><?php echo sprintf('%0.2f', (($sum*$systemsetting->sales_taxerate)/100 + $sum));?></span></td>
		 </tr>		 		 		 		 
	</table>
	<?php if($shipping_method->id==52) :?>
		<table id="doc_signature" style="border-collapse:separate;margin-top:10px;">
			 <tr>
			    <td style="border:2px solid silver;color:#BBB;padding-top:15px;" valign="bottom">Date</td>
			    <td style="width:500px;"></td>
			    <td style="border:2px solid silver;color:#BBB;padding-top:15px;" valign="bottom">Authorized Signature</td>
			 </tr>		
		</table>
	<?php endif;?>
<!--	<div class="footer" style="font-size: 10px; text-align: center;margin-top:15px;">
		<div class="line_one" style="border-bottom: 1px solid silver;">Should you have any enquiries concerning this quote, please contact John Doe on 0-000-000-0000</div>
		<div class="line_two">3431 N. Industrial Dr., Simpsonville, SC 29681</div>
		<div class="line_three">Tel: 864.331.8678 E-mail: info@assetenterprises.com Web: www.assetenterprises.com</div>
	</div>-->
</section>