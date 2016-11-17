<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	
	use app\models\Category;
	use app\models\Department;
	use app\models\Location;
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Medias;
	use app\models\Item;
	use app\models\LocationParent;
	use yii\grid\GridView;
	use yii\data\SqlDataProvider;
	use yii\helpers\ArrayHelper;
	
?>
<style>
	.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th
	{
		font-size: 14px;
		text-align: center;
	}
	.table>thead>tr>th:nth-child(2),
	.table>tbody>tr>td:nth-child(2)
	{
		text-align: left;
	}
	.x_content
	{
		padding: 0px;
	}
</style>
<?= $this->render("@app/modules/Customers/views/default/_modals/_editcategorymodal");?>
<div class="row row-margin">
	<div class="col-md-12 col-sm-6 col-xs-12">
		<div class="x_panel" style="padding:0px;border:none;">
			<div class="x_content">
				<?php 
					//var_dump($locations);
					//array_unshift($locations , null);
					//$div_store_locations = ArrayHelper::getColumn(Location::find()->where(['customer_id'=>$customer->id, 'storenum'=>'DIV'])->asArray()->all(), 'id');
					//var_dump($div_store_locations);
				?>
				<?php foreach($locations as $location): ?>
					<div class="row" style="padding: 2px; padding-left: 10px; font-size: 14px; background: #73879C; color: #FFF; border-radius:5px; margin-bottom: 10px;">
						<div class="col-md-5 vcenter col-xs-6" style="line-height: 30px;">
							<span class="glyphicon glyphicon-lock"></span> <b><?php echo LocationParent::findOne($location->parent_id)->parent_name?></b>
						</div>
						<div class="col-md-7 vcenter text-right col-xs-6" style="margin-top:3px;"> 
							<button class="btn btn-xs btn-info glyphicon glyphicon-plus" id="load-models-location-<?php echo (!empty($location)) ? $location->parent_id : '0';?>" lid="<?php echo (!empty($location)) ? $location->parent_id : '0';?>" pid="<?=$customer->id;?>"></button>
							<button class="btn btn-xs btn-info glyphicon glyphicon-minus" id="close-models-location-<?php echo (!empty($location)) ? $location->parent_id : '0';?>" lid="<?php echo (!empty($location)) ? $location->parent_id : '0';?>" style="display: none;"></button>
						</div>
					</div>
					<div id="loaded-content-location-<?php echo (!empty($location)) ? $location->parent_id : '0';?>" style="display: none;">
						<div id="gridview-<?php echo (!empty($location)) ? $location->parent_id : '0'; ?>">
						<?php 
							$sql = "SELECT 
										lv_models.id,
										lv_manufacturers.name,
										lv_models.descrip,
										lv_models.aei,
										lv_models.image_id,
										lv_departements.name as department,
										lv_categories.categoryname,
										SUM(storenum='DIV') as qty_division,
										SUM(storenum<>'DIV') as qty_location,
										SUM(confirmed=1) as qty_confirmed
										FROM lv_models 
										INNER JOIN lv_items 
										ON lv_models.id=lv_items.model
										LEFT JOIN lv_categories
										ON `lv_models`.`category_id` = `lv_categories`.`id`
										LEFT JOIN lv_manufacturers
										ON lv_models.manufacturer=lv_manufacturers.id
										LEFT JOIN lv_departements
										ON lv_models.department=lv_departements.id
										INNER JOIN lv_locations
										ON lv_items.location=lv_locations.id";
							$sql .= (!empty($location)) ? " INNER JOIN lv_locations_classments
										ON lv_items.location=lv_locations_classments.location_id
										WHERE lv_items.customer=". $customer->id ."
										AND lv_locations_classments.parent_id=".$location->parent_id."" : " WHERE lv_items.customer=". $customer->id ." AND location NOT IN (SELECT DISTINCT(location_id) FROM lv_locations_classments)";
							$sql .= "   GROUP BY lv_items.model  
										ORDER BY name, descrip 
										";
							//
							//*echo $sql;
							$dataProvider = new SqlDataProvider([
										'sql' => $sql,
										'pagination' => ['pageSize' => 100],
									]);									
						?>
							<?= 
								GridView::widget([
									'dataProvider' => $dataProvider,
									'summary' => false,
								    'rowOptions' => function ($model, $index, $widget, $grid){
										return ['id'=>'row-models-' . $model['id']];
									},
									'columns' => [	
											[
												'attribute' => 'imagepath',  
												'label' => 'Thumbnail',
												'format' => 'raw',
												'value' => function($model) {
													$picture = Medias::findOne($model['image_id']);
													if($picture!==null)
													return Html::img(Yii::getAlias('@web').'/public/images/models/'.$picture->filename, ['alt'=>'logo', 'onClick'=>'ModelsViewer('. $model['id'] .');', 'style'=>'max-width: 90px;max-height: 35px;']);
												}
											],											
											[
												'format' => 'raw',
												'label' => 'Description',
												'value' => function($model) {
													return '<div style="line-height: 40px;font-weight:bold;">' . $model['name'] . ' ' . $model['descrip'] . '</div>';
												}
											],
											[
												'format' => 'raw',
												'contentOptions' => ['style' => "text-align: center,"],
												'label' => 'Qty At Division',
												'value' => function($model) use ($location, $customer) {
													$_output = $model['qty_division'];
													$_low_stock_style = 'color: #333';
													$_in_stock_style = 'font-weight: bold; color: #08c';
													$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;			
													$_url = Url::to(['/customers/statusdivisionsdetails', 'type'=>1, 'customer'=>$customer->id, 'model'=>$model['id'], 'parent'=>$location->parent_id]);
													return '<a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
												}
											],			            
											[
												'format' => 'raw',
												'label' => 'Qty On Location',
												'contentOptions' => ['style' => "text-align: center,"],
												'value' => function($model) use ($location, $customer){
													$_output = $model['qty_location'];
													$_low_stock_style = 'color: #333';
													$_in_stock_style = 'font-weight: bold; color: #08c';
													$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;	
													$_url = Url::to(['/customers/statusdivisionsdetails', 'type'=>2, 'customer'=>$customer->id, 'model'=>$model['id'], 'parent'=>$location->parent_id]);
													return '<a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';	   
												}
											],
											[
												'format' => 'raw',
												'label' => 'Confirmed Qty',
												'contentOptions' => ['style' => "text-align: center,"],
												'value' => function($model) use ($location, $customer){
													$_output = $model['qty_confirmed'];
													$_low_stock_style = 'color: #333';
													$_in_stock_style = 'font-weight: bold; color: #08c';
													$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;	
													$_url = Url::to(['/customers/statusdivisionsdetails', 'type'=>3, 'customer'=>$customer->id, 'model'=>$model['id'], 'parent'=>$location->parent_id]);
													return '<a <a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
												}
											],
											[
												'format' => 'raw',
												'label' => 'Total',
												'contentOptions' => ['style' => "text-align: center,"],
												'value' => function($model) {
													$instockqty = $model['qty_division'];
													$inprogress_qty = $model['qty_location'];
													$shipped_qty = $model['qty_confirmed'];
													$total = $instockqty + $inprogress_qty + $shipped_qty;
													$_output = $total;
													$_low_stock_style = 'color: #333';
													$_in_stock_style = 'font-weight: bold; color: #08c';
													$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
													return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';		   
												}
											]
											], 
										]);
										?>								
						</div>							
					</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/overview-ownstockpage.js"></script>