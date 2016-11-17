<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	
	$this->title ='Deliver SO#' . $order->number_generated . ' Items';	
?>

<div class="">
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
						<?=  $this->render('_picklistreadyform', [
								'order'=>$order,
								'delivertoshippingitems' => $delivertoshippingitems,
								'_countshippingitems' => $_countshippingitems,
								'delivercleaningitems' => $delivercleaningitems,
								'_countcleaningitems' => $_countcleaningitems,
								'delivertestingitems' => $delivertestingitems,
								'_counttestingitems' => $_counttestingitems,
								'_totalcount' => $_totalcount
						]); ?>
					</div>
					<div class="row row-margin text-right">
						<a href="<?= Url::to(['/orders/viewpicklist', 'id'=>$order->id]);?>" class="btn btn-default"><?php echo Yii::t('app', 'Close'); ?></a>	
		         		<button type="button" class="btn btn-primary" id="main-delivery-items" onClick="confirmReadyButton(0, 0, <?php echo $order->id;?>);"><?php echo Yii::t('app', 'Deliver All'); ?></button>	
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>