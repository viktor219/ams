<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Location;
	use app\models\User;
	use app\models\Item;
	use app\models\Itemlog;
	use yii\grid\GridView;
	
	$this->title = Item::$status[$status] . ' ' . $customer->companyname . ' Details';
	if(Yii::$app->user->identity->usertype!=User::REPRESENTATIVE && Yii::$app->user->identity->usertype!=User::TYPE_CUSTOMER)
		$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['ownstockpage', 'id' => $customer->id]];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-5 vcenter">
				<h4>
					<span class="glyphicon glyphicon-list-alt"></span>
					<?php echo $customer->companyname . ' Details';?> (<b><?php echo Item::$status[$status];?></b>)
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
						<?= GridView::widget([
								'dataProvider' => $dataProvider,
								'summary' => '',
								'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => 'No Results to display'],
								'columns' => [
									[
										'attribute' => 'model',
										'format' => 'raw',
										'value' => function($model) {
											$_model = Models::findOne($model->model);
											$_manufacturer = Manufacturer::findOne($_model->manufacturer);
											return '<div style="line-height: 40px;"><b>' . $_manufacturer->name . ' ' . $_model->descrip . '</b></div>';
										}
									],
									[
										'attribute' => 'serial',
										'format' => 'raw',
										'value' => function($model) {
											$_url = Html::a($model->serial, ['/customers/serialdetails', 'id'=>$model->id], ['style' => 'color: #08c']);
											return '<div style="line-height: 40px;">' . $_url . '</div>';
										}
									],
									[
										'label' => 'In Stock',
										'format' => 'raw',
										'value' => function($model) {
											//$_createdate = Itemlog::find()->where(['itemid'=>$model->id, 'status'=>array_search('In Stock', Item::$status)])->one()->created_at;
											$_return = (!empty($model->received) && $model->received != "0000-00-00 00:00:00") ? date("M d g:ia", strtotime($model->received)) : "Not Received";
											return '<div style="line-height: 40px;">' . $_return . '</div>';
										}										
									],
									[
										'label' => 'In Progress',
										'format' => 'raw',
										'value' => function($model) {
											//$_createdate = Itemlog::find()->where(['itemid'=>$model->id, 'status'=>array_search('In Progress', Item::$status)])->one()->created_at;
											$_return = (!empty($model->picked) && $model->picked != "0000-00-00 00:00:00") ? date("M d g:ia", strtotime($model->picked)) : "Not In Progress";
											return '<div style="line-height: 40px;">' . $_return . '</div>';
										}										
									],
									[
										'label' => 'Shipped',
										'format' => 'raw',
										'value' => function($model) {
											//$_createdate = Itemlog::find()->where(['itemid'=>$model->id, 'status'=>array_search('Shipped', Item::$status)])->one()->created_at;
											$_return = (!empty($model->shipped) && $model->shipped != "0000-00-00 00:00:00") ? date("M d g:ia", strtotime($model->shipped)) : "Not Shipped";
											return '<div style="line-height: 40px;">' . $_return . '</div>';
										}										
									],	
									[
										'attribute' => 'location',
										'label' => 'Current Location',
										'contentOptions' => ['style' => 'max-width:275px;'],
										'format' => 'raw',
										'value' => function($model) use ($customer) {
											$location = Location::findOne($model->location);
											if(!empty($location->storenum))
												$output .= "Store#: " . $location->storenum . " - ";
											if(!empty($location->storename))
												$output .= $location->storename  . ' - '; 
											//
											$output .= $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;	
											return '<div style="line-height: 40px;">' . $customer->companyname . ' - ' . $output . '</div>';
										}										
									],																				
								],
							]) ?>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>