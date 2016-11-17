<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	use yii\widgets\DetailView;
	
	use app\models\Item;
	use app\models\Manufacturer;
	use app\models\Medias;
	
	$this->title = $customer->companyname . ' Item Log Details';
	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['ownstockpage', 'id' => $customer->id]];
	$this->params['breadcrumbs'][] = $this->title;
	
	$_my_media = Medias::findOne($customer->picture_id);
	if(!empty($_my_media->filename)){
		$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
		if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) 
			$_output_picture = Html::img($target_file, ['class'=>'showCustomer', 'alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:220px;max-height:80px;border: 1px solid silver;']);						 
		else
			$_output_picture = $customer->companyname;					
	}else 
		$_output_picture = $customer->companyname;	
?>

<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-5 vcenter">
				<h4>
					<span class="glyphicon glyphicon-list-alt"></span>
					<?= $this->title; ?>
				</h4>
			</div>
			<div class="col-md-7 vcenter text-right"> </div>                
		</div>
	</div>
	<div class="panel-body">
		<div class="row row-margin">
			<div class="col-md-12 col-sm-6 col-xs-12">
				<div class="x_panel" style="padding:0px;border:none;">
					<div class="x_content">
						<div class="row row-margin" style="text-align: center;"><?= $_output_picture ?></div>
						<div class="row row-margin">
							<?= DetailView::widget([
								'model' => $model,
								'attributes' => [
									[
										'label'=>'',
										'value'=>Manufacturer::findOne($_model->manufacturer)->name . ' ' . $model->descrip
									],							
									/*[
										'label' => '',
										'format' => 'raw',
										'value'=>
									],*/
								],
							]) ?>		
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>