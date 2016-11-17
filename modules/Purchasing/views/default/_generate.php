<?php 
	use yii\helpers\Html;
	use app\models\Ordertype;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\SystemSetting;
	use app\models\Item;
	
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

$vendorcity = $vendor->city;
$vendorstate = $vendor->state;
?>
<section style="overflow-y:auto;">
	<table id="header_pdf">
		<tr>
			<th style="width: 1000px;" class="align_left">
				<span style="cursor:pointer;line-height: 40px;"><?php echo $vendor->vendorname;?></span>		
			</th>
			<th class="align_right" scope="col" colspan="2"><b>Purchase Order</b><th>
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
			<td style="padding-right:25px;">P.O. Number</td>
			<td style="background: #eee;" class="align_right"><?php echo $model->number_generated;?></td>
		</tr>
		<tr style="border-bottom:4px solid white;">
			<td></td>
			<td style="padding-right:25px;">Vendor ID</td>
			<td style="background: #eee;" class="align_right"><?php echo $vendor->vendorid;?></td>
		</tr>		
	</table>
	<table id="sr_addresses">
		<tr>
			<th class="align_left">Vendor</th>
			<th style="width:500px;background:white"></th>
			<th style="width:400px;" class="align_left">Ship To</th>
		</tr>	
		<tr>
			<td><?php echo $vendor->vendorname;?></td>
			<td></td>
			<td><?php echo $assetCustomer->companyname;?></td>
		</tr>	
		<tr>
			<td><?php echo $vendor->address_line_1;?></td>
			<td></td>
			<td>3431 N. Industrial Dr.</td>
		</tr>		
		<tr>
			<td>
			<?php if(isset($vendorcity)) : ?> 
				<?php echo $vendorcity;?>,
			<?php endif;?>
			<?php if(isset($vendorstate)) : ?> 
				<?php echo $vendorstate;?>,
			<?php endif;?>
			United States
			<?php echo $vendor->zip;?></td>
			<td></td>
			<td>Simpsonville, SC 29681</td>			
		</tr>	
		<tr>
			<td><?php echo $vendor->telephone_1;?></td>
			<td></td>
			<td>info@assetenterprises.com</td>
		</tr>				
		<tr>
			<td></td>
			<td></td>
			<td>864.331.8678</td>
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
			<td><?php echo date_format(date_create($model->created_at), "m/d/Y");?></td>
		</tr>	
	</table>
	<table id="products" style="margin-bottom:120px;">
		<tr>
			<th style="width:600px;text-align:left;">Product Name / Description</th>
			<th>Qty</th>
			<th>Unit Price</th>
			<th>Total</th>
		</tr>
			<?php foreach ($itemspurchased as $itempurchased) :?>
				<?php 
					$_model = Models::findOne($itempurchased->model);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
					$total_price = $itempurchased->qty * $itempurchased->price;
				?>
				<tr <?php echo ($_itemrow % 2 != 0) ? 'class="pair-row"' : '';?>>
					<td style="padding-left:10px;text-align:left;"><?php echo $_manufacturer->name . ' ' . $_model->descrip;?></td>
					<td style="padding-left:20px"><?php echo $itempurchased->qty;?></td>
					<td style="padding-left:20px"><?php echo $itempurchased->price;?></td>
					<td style="padding-left:20px"><?php echo number_format($total_price, 2);?></td>
				</tr>
				<?php $sum+=$total_price;$_itemrow++;?>
		<?php endforeach;?>
	</table>
	<table id="products_resume"  style="margin-bottom:210px;">
		<tr style="border:none;">
			<th style="width:600px;" class="align_left">Notes and Instructions<div><?php //echo $model->notes;?></div></th>
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
</section>