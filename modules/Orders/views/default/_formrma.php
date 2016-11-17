<?php 
	
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	$this->title = 'New RMA Order';
	
?>

<div class="row row-margin">			
	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id'=>'add-rmaorder-form']]); ?>
		<div class="col-lg-12 col-xs-12">
			<div class="x_panel" style="padding: 10px 10px;">
				<div class="x_title">
					<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content" style="padding:0;margin-top:0;">
					<div class="" role="tabpanel" data-example-id="togglable-tabs">
						<div id="myTabContent" class="tab-content">	
							<div class="row row-margin">
								<div class="panel panel-primary">
								  <div class="panel-heading">
									<h3 class="panel-title">Please Choose A Location:</h3>
								  </div>
								  <div class="panel-body">
									  <div class="form-group">
										<select class="form-control" id="selectlocation">
										  <option>Store# 1</option>
										  <option>Store# 2</option>
										  <option>Store# 3</option>
										  <option>Store# 4</option>
										  <option>Store# 5</option>
										</select>
									  </div>
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