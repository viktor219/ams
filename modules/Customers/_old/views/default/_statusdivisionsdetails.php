<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	use app\models\LocationParent;
	
	use app\models\Item;
	
	$_parent_name = LocationParent::findOne($parent)->parent_name;
	
	$this->title =   $_parent_name . ' ' . $customer->companyname . ' Details';
	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['ownstockpage', 'id' => $customer->id]];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-5 vcenter">
				<h4>
					<span class="glyphicon glyphicon-list-alt"></span>
					<?php echo $customer->companyname . ' Details';?> (<b><?php echo $_parent_name;?></b>)
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
						<?= $this->render('_statusdetailsview', ['dataProvider' => $dataProvider, 'customer'=>$customer]); ?>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>