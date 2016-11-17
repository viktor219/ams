<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Medias;
	use app\models\Location;
	use app\models\User;
	use app\models\Item;
	use app\models\Itemlog;
	
	use yii\grid\GridView;
	
	$this->title = 'Serial Details';
	
	$this->params['breadcrumbs'][] = (Yii::$app->user->identity->usertype!==User::REPRESENTATIVE) ? ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['ownstockpage', 'id' => $customer->id]] : ['label' => Yii::t('app', $customer->companyname . ' Overview'), 'url' => ['/overview/index#rma-main-gridview']];
	$this->params['breadcrumbs'][] = $this->title;
	
	$_my_media = Medias::findOne($customer->picture_id);
	if(!empty($_my_media->filename)){
		$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
		if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) 
			$_output_picture = Html::img($target_file, ['class'=>'showCustomer', 'alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:220px;max-height:80px;margin-top: 10px;margin-bottom: 10px;']);						 
		else
			$_output_picture = $customer->companyname;					
	}else 
		$_output_picture = $customer->companyname;	
	
//
/* $_receiveddate = Itemlog::find()->where(['itemid'=>$item->id, 'status'=>array_search('In Stock', Item::$status)])->one()->created_at;
$_pickeddate = Itemlog::find()->where(['itemid'=>$item->id, 'status'=>array_search('In Progress', Item::$status)])->one()->created_at;
$_shippeddate = Itemlog::find()->where(['itemid'=>$item->id, 'status'=>array_search('Shipped', Item::$status)])->one()->created_at; */

$_receiveddate = $item->received;
$_pickeddate = $item->picked;
$_shippeddate = $item->shipped;

//var_dump($item->id, $_receiveddate);

$received = strtotime($_receiveddate);
$received = date("M d g:ia",$received);
//
$picked = strtotime($_pickeddate);
$picked = date("M d g:ia",$picked);
//
$shipped = strtotime($_shippeddate);
$shipped = date("M d g:ia",$shipped);

$_my_media = Medias::findOne($model->image_id);

$location = Location::findOne($item->location);

/*var_dump($location);

$address = $location->address;

$storenum = $location->storenum;

$storename = $location->storename;

$city = $location->city;

$state = $location->state;

$zipcode = $location->zipcode;*/

$_output_location = $location->storename;
if(!empty($location->storenum))
	$_output_location = "Store#: " . $location->storenum;
//
if(!empty($location->address))
	$_output_location = "Store#: " . $location->address . ", " . $location->city . ", " . $location->state . ", " . $location->zipcode;
?>
<div class="panel panel-info">
	<div class="panel-heading">
		<div class="row vertical-align">
			<div class="col-md-5 vcenter">
				<h4>
					<span class="glyphicon glyphicon-list-alt"></span>
					<?= $this->title;?>
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
						<div class="panel-body" style="padding: 15px 0 0 0">
							<div class="row row-margin">
								<div class="x_panel">
									<div class="x_title">
										<h2><i class="fa fa-bars"></i> SERIAL NUMBER FOOTPRINT</h2>
										<ul class="nav navbar-right panel_toolbox">
											<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
											</li>
											<li class="dropdown">
												<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
												<ul class="dropdown-menu" role="menu">
													<li><a href="#">Settings 1</a>
													</li>
													<li><a href="#">Settings 2</a>
													</li>
												</ul>
											</li>
											<li><a class="close-link"><i class="fa fa-close"></i></a>
											</li>
										</ul>
										<div class="clearfix"></div>
									</div>
									<div class="x_content">
										<div class="" role="tabpanel" data-example-id="togglable-tabs">
											<div class="row row-margin">
												<div class="col-md-6"><?= $_output_picture;?></div>
												<div class="col-md-6">
													<div class="col-md-9" style="text-align: center;">
														<div><h2><b><?php echo $manufacturer->name . ' ' . $model->descrip;?></b></h2></div>
														<div><h2>Serial Number: <b><?= $item->serial;?></b></h2></div>
														<div><h2>Location: <b><?= $_output_location;?></b></h2></div>
													</div>
													<div class="col-md-3">
														<?php if($_my_media->filename) :?>
															<?= Html::img(Yii::getAlias('@web').'/public/images/models/'. $_my_media->filename, ['alt'=>'logo', 'onClick'=>'ModelsViewer(' . $model->id . ');', 'style'=>'cursor:pointer;max-width:220px;max-height:80px;']);?>
														<?php endif;?>	
													</div>
												</div>
											</div>
											<div class="row row-margin"></div>
											<div class="row row-margin"></div>
											<div class="row row-margin">
												<?= GridView::widget([
														'dataProvider' => $dataProvider,
														'summary' => '',
														'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => 'No Results to display'],
														'columns' => [
															[
																'label' => 'Status',
																'format' => 'raw',
																'value' => function($model) {
																	$_return = Item::$status[$model->status];
																	return '<div style="line-height: 40px;">' . $_return . '</div>';
																}										
															],
															[
																'label' => 'Date & Time',
																'format' => 'raw',
																'value' => function($model) {
																	$_return = (!empty($model->created_at)) ? date("F d, Y g:ia", strtotime($model->created_at)) . " EST" : "Not Set";
																	return '<div style="line-height: 40px;">' . $_return . '</div>';
																}										
															],
															[
																'label' => 'Location',
																'format' => 'raw',
																'value' => function($model) {
																	if(!empty($model->locationid))
																	{
																		$location = Location::findOne($model->locationid);
																		$_output = $location->storename;
																		if(!empty($location->storenum))
																			$_output = "Store#: " . $location->storenum;
																		//
																		if(!empty($location->address))
																			$_output = "Store#: " . $location->address . ", " . $location->city . ", " . $location->state . ", " . $location->zipcode;
																	}
																	else 
																		$_output = "-";
																	return '<div style="line-height: 40px;">' . $_output . '</div>';
																}
															],
															[
																'label' => 'Action Taken By:',
																'format' => 'raw',
																'value' => function($model) {
																	$user = User::findOne($model->userid);
																	return '<div style="line-height: 40px;">' . $user->firstname . ' ' . $user->lastname . '</div>';
																}										
															]
														]
													])?>
											</div>
											<?php /*<table width="100%" class="pure-table pure-table-horizontal">
												<tr>
													<td align="center">
													<br>
													
													</td>
													<td align="center">
													<h1></h1>
													<h3>
													<strong>Received into warehouse:</strong> <?= $received;?>
													<br>
													<br>
													<?php echo (!empty($_pickeddate)) ? "<strong>Picked for order fulfillment:</strong> " . $picked : '';?>
													<br>
													<br>
													<?php  echo (!empty($_shippeddate)) ? "<strong>Shipped to location:</strong> " . $shipped : '';?>
													<?php  echo (!empty($_pickeddate) && empty($_shippeddate)) ? ", preparing to ship to: " : ""; ?>
													</h3><h2>
													<?php  echo (!empty($_pickeddate) && !empty($_shippeddate)) ? "<br>Current Location: " : ""; ?>
													<?php  echo (empty($_pickeddate) && empty($_shippeddate)) ? "<br>Current Location: " : ""; ?>
													<strong>
													<?= $storename;?>
													<?php echo (!empty($storenum)) ? "Store#: $storenum" : ""; ?>
													</strong><br>
													<?php echo (!empty($address)) ? "Store#: $address, $city, $state, $zipcode" : ""; ?>
													</h2>
													</td>
												</tr>
											</table>
											*/?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>