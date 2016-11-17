<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Location;

$this->title =  'Shipments';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['/customers/ownstockpage', 'id'=>$customer->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-5 vcenter">
				<h4>
					<span class="glyphicon glyphicon-list-alt"></span>
					<?= Html::encode($this->title) ?> (<b><?php echo $customer->companyname;?></b>)
				</h4>
			</div>
			<div class="col-md-7 vcenter text-right"> 
				<?= Html::a('<span class="glyphicon glyphicon-plus"></span> New Shipment', 'javascript:;', ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
			</div>                
		</div>
	</div>
	<div class="panel-body">
		<div class="row row-margin">
			<div class="col-md-12 col-sm-6 col-xs-12">
				<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
					'summary'=>'', 
					'emptyText'=>'No shipments',					
					'columns' => [
						[
							'attribute' => 'dateshipped',
							'label' => 'Date Created',
							'value' => function ($model) {
								return date("M d g:ia", strtotime($model->dateshipped));
							}
						],
						[
							'attribute' => 'locationid',
							'label' => 'Shipping To',
							'value' => function ($model) {
								$location = Location::findOne($model->locationid);
								if(!empty($location->storenum))
									$output .= "Store#: " . $location->storenum . " - ";
								if(!empty($location->storename))
									$output .= $location->storename  . ' - '; 
								//
								$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;									
								return $output;
							}
						],	
						[
							'label' => 'Status',
							'format' => 'raw',
							'value' => function ($model) {
								$trackingnumber = $model->trackingnumber;
								$trackinglink = $model->trackinglink;
								$_output = "";
								if (!empty($trackingnumber))
								{
									if ($trackinglink == 1)
										$_output = Html::a($trackingnumber, 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $trackingnumber, ['target' => '_blank']);
									else if ($trackinglink == 2)
										$_output = Html::a($trackingnumber, 'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=' . $trackingnumber, ['target' => '_blank']);
									else if ($trackinglink == 3)
										$_output = Html::a($trackingnumber, 'http://www.estes-express.com/cgi-dta/edn419.mbr/output?search_criteria=' . $trackingnumber, ['target' => '_blank']);
									else 
										$_output = $trackingnumber;
								}		
								return $_output;
							}
						],						
						[
							'class' => 'yii\grid\ActionColumn',
							//'template'=> '{pdf_report} {update}',
							'template'=> '{pdf_report}',
							'controller' => 'shipments',
							'buttons' => [
								'pdf_report' => function ($url, $model, $key) {
									$options = [
										'title' => 'PDF Report', 
										'class' => 'btn btn-info', 
										'target' => '_blank'
									];
									$url = \yii\helpers\Url::toRoute(['/shipments/generate', 'id'=>$model->id]);

									return Html::a('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', $url, $options);
								},
								'update' => function ($url, $model, $key) {
									$options = [
										'title' => 'Edit',
										'class' => 'btn btn-warning',
									];
									$url = \yii\helpers\Url::toRoute(['/shipments/update', 'id'=>$model->id]);

									return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
								}								
							]
						],
					],
				]) ?>
			</div>
		</div>
	</div>	
</div>