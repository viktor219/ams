<?php 
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Partnumber;
	
	$i = 0;
?>
<?php if($showall) :?>
	<?php foreach($items as $item) :?>
	<?php 
		$_model = Models::findOne($item->model);
		$_manufacturer = Manufacturer::findOne($_model->manufacturer);
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$partnumber = Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$item->model])->one();
	?>
	<div class="item-style">
		<div class="itemname"><?= $_manufacturer->name;?> <?= $_model->descrip;?></div>
		<div class="itempicture"><?= '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($item->serial, $generator::TYPE_CODE_128, 1.4, 50)) . '">';?></div>
		<div class="itemserial">Serial Number : <?= $item->serial;?></div>
		<div class="itempartid"><?php if (!empty($partnumber->partid)) : ?>Part Number : <?= $partnumber->partid;?><?php endif;?></div>
		<div class="itempartdesc"><?php if (!empty($partnumber->partdescription)) : ?>Description : <?= $partnumber->partdescription;?><?php endif;?></div>
	</div>
	<?php $i++;?>
	<?php if($i<count($items)) :?>
		<pagebreak />
	<?php endif;?>
	<?php endforeach;?>
<?php else :?>
	<?php 
		$_model = Models::findOne($item->model);
		$_manufacturer = Manufacturer::findOne($_model->manufacturer);
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$partnumber = Partnumber::find()->where(['customer'=>$customer->id, 'model'=>$item->model])->one();
	?>
	<div class="item-style">
		<div class="itemname"><?= $_manufacturer->name;?> <?= $_model->descrip;?></div>
		<div class="itempicture"><?= '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($item->serial, $generator::TYPE_CODE_128, 1.4, 50)) . '">';?></div>
		<div class="itemserial">Serial Number : <?= $item->serial;?></div>
		<div class="itempartid"><?php if (!empty($partnumber->partid)) : ?>Part Number : <?= $partnumber->partid;?><?php endif;?></div>
		<div class="itempartdesc"><?php if (!empty($partnumber->partdescription)) : ?>Description : <?= $partnumber->partdescription;?><?php endif;?></div>
	</div>
<?php endif;?>