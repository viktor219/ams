<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Partnumber;

/* @var $this yii\web\View */
/* @var $model app\models\ModelAssembly */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="model-assembly-form">

    <?php $form = ActiveForm::begin(['options' => ['action'=>['/assembly/create'], 'id'=>'add-assembly-form', 'enctype' => 'multipart/form-data']]); ?>
    
	<div class="col-lg-12 col-sm-6 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="" role="tabpanel" data-example-id="togglable-tabs">
					<div id="myTabContent" class="tab-content">	
						<div class="col-md-6">
							<div class="row row-margin" id="customer-group">
								<input type="text" id="autocomplete-assembly-customer" class="form-control col-md-4" name="customer" placeholder="Choose Customer" autocomplete="off" value="<?php echo $customer->companyname;?>">
								<input type="hidden" id="customer_Id" name="customerId" value="<?php echo (!$model->isNewRecord) ? $customer->id : '';?>"/>							
							</div>
						    <div class="row row-margin" id="modelId-group">
								<input id="autocompleteitem_1" class="form-control" type="text" name="assembly_name" value="<?php echo (!$model->isNewRecord) ? $model->descrip : '';?>" placeholder="Tape Assembly name">
						    </div>
						    <div class="row row-margin">
							   	<?php if($model->isNewRecord) :?>				    
									<div id="entry1" class="clonedInput">
										<div class="row form-group">
											<div class="col-sm-2 qty-group">
												<input class="select_ttl form-control" type="text" name="quantity[]" id="quantity_1" value="" placeholder="Qty" required>
											</div>
											<div class="col-sm-6 desc-group">
												<input class="typeahead form-control input_fn" type="text" name="description[]" id="autocompleteitem_1" value="" placeholder="Select an Item of Assembly" data-provide="typeahead" autocomplete="off" type="search" >
												<input class="form-control input_h" type="hidden" name="partid[]" id="autocompletevalitem_1" />													
											</div>
										    <div class="col-sm-4 partNumber-group">
												<input class="form-control input_partnum" type="text" name="partnumber[]" id="partnumber_1" value="" placeholder="Part Number">
										    </div>
										</div>
									</div>
								<?php else :?>
									<?php foreach ($assemblies as $assembly) :?>
									<?php 
										$_model = Models::findOne($assembly->partid);
										$_manufacturer = Manufacturer::findOne($_model->manufacturer);
										$partnumber = Partnumber::findOne(['model'=>$_model->id, 'customer'=>$customer->id]);
									?>
										<div id="entry1" class="clonedInput">
											<div class="row form-group">
												<div class="col-sm-2 qty-group">
													<input class="select_ttl form-control" type="text" name="quantity[]" id="quantity_1" value="<?php echo $assembly->quantity;?>" placeholder="Qty" required>
												</div>
												<div class="col-sm-6 desc-group">
													<input class="typeahead form-control input_fn" type="text" name="description[]" id="autocompleteitem_1" value="<?php echo $_manufacturer->name . ' ' . $_model->descrip;?>" placeholder="Select an Item of Assembly" data-provide="typeahead" autocomplete="off" type="search" >
													<input class="form-control input_h" type="hidden" name="partid[]" id="autocompletevalitem_1" value="<?php echo $assembly->partid;?>" />													
												</div>
											    <div class="col-sm-4 partNumber-group">
													<input class="form-control input_partnum" type="text" name="partnumber[]" id="partnumber_1" value="<?php echo $partnumber->partid;?>" placeholder="Part Number">
											    </div>
											</div>
										</div>
									<?php endforeach;?>
								<?php endif;?>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="actions text-right">
										<button class="btn btn-success btn-xs" id="btnAAdd" type="button"><span class="glyphicon glyphicon-plus"></span></button>
										<button class="btn btn-success btn-xs" id="btnDel" type="button"><span class="glyphicon glyphicon-minus"></span></button>
									</div>
								</div>
							</div>
							<div class="row-margin"></div>
							<div class="row">
								<div class="col-md-12 text-right">
									<button onClick="redirectAssembly();" class="btn btn-primary"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
									<button class="btn btn-success" type="button" id="submitAssembly"><span class="glyphicon glyphicon-save"></span> Create</button>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
    <?php ActiveForm::end(); ?>

</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/assembly.js"></script>