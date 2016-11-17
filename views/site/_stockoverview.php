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
	use yii\grid\GridView;
	use yii\data\SqlDataProvider;
	
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
				<?php foreach($categories as $category): ?>
					<div class="row" style="padding: 2px; padding-left: 10px; font-size: 14px; background: #73879C; color: #FFF; border-radius:5px; margin-bottom: 10px;">
						<div class="col-md-5 vcenter col-xs-10" style="line-height: 30px;">
							<span class="glyphicon glyphicon-lock"></span> <b><?php echo $category->categoryname;?></b>
						</div>
						<div class="col-md-7 vcenter col-xs-2 text-right" style="margin-top:3px;"> 
							<button class="btn btn-xs btn-info glyphicon glyphicon-plus" id="load-models-category-<?=$category->id;?>" cid="<?=$category->id;?>" pid="<?=$customer->id;?>"></button>
							<button class="btn btn-xs btn-info glyphicon glyphicon-minus" id="close-models-category-<?=$category->id;?>" cid="<?=$category->id;?>" style="display: none;"></button>
						</div>
					</div>
					<div id="loaded-content-category-<?php echo $category->id;?>" style="display: none;">
						<div id="gridview-<?= $category->id; ?>">
						<?php 
							$sql = "SELECT 
											SUM(lv_items.status='". array_search('In Stock', Item::$status)."') as instock_qty,
											SUM(lv_items.status='". array_search('In Progress', Item::$status)."') as inprogress_qty,
									";
							$sql .= (isset($_location)) ? "SUM(lv_items.status='". array_search('Complete', Item::$status)."' AND location='".$_location->id."') as shipped_qty," : "SUM(lv_items.status='". array_search('Complete', Item::$status)."') as shipped_qty,";
							$sql .= (isset($_location)) ? "
											SUM(lv_items.status='". array_search('In Stock', Item::$status)."' OR lv_items.status='".array_search('In Progress', Item::$status)."' OR (lv_items.status='".array_search('Complete', Item::$status)."' AND location='".$_location->id."')) as total,
									": "
											SUM(lv_items.status='". array_search('In Stock', Item::$status)."' OR lv_items.status='".array_search('In Progress', Item::$status)."' OR lv_items.status='".array_search('Complete', Item::$status)."') as total,
									";
							$sql .= "
											lv_models.id,
											lv_manufacturers.name,
											lv_models.descrip,
											lv_models.aei,
											lv_models.image_id,
											lv_departements.name as department,
											lv_categories.categoryname
											FROM lv_models 
											INNER JOIN lv_items 
											ON lv_models.id=lv_items.model
											INNER JOIN lv_categories
											ON `lv_models`.`category_id` = `lv_categories`.`id`
											LEFT JOIN lv_manufacturers
											ON lv_models.manufacturer=lv_manufacturers.id
											LEFT JOIN lv_departements
											ON lv_models.department=lv_departements.id
											WHERE lv_items.customer=". $customer->id ."
											AND lv_models.category_id=".$category->id."
											GROUP BY lv_items.model 
											ORDER BY name, descrip 
											";
							//
							//echo $sql;
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
											'value' => function($model) use ($customer){
												//return '<div style="line-height: 40px;font-weight:bold;">' . Html::a($model['description'], ['/customers/statuslog', 'model' => $model['id'], 'customer' => $customer->id], []) . '</div>';
												return '<div style="line-height: 40px;font-weight:bold;">' . $model['name'] . ' ' . $model['descrip'] . '</div>';
											}
										],
										[
											'format' => 'raw',
											'contentOptions' => ['style' => "text-align: center,"],
											'label' => 'In Stock',
											'value' => function($model) use ($customer, $category){
												$_output = $model['instock_qty'];
												$_low_stock_style = 'color: #333';
												$_in_stock_style = 'font-weight: bold; color: #08c';
												$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;			
												$_url = Url::to(['/customers/statusdetails', 'status'=>array_search('In Stock', Item::$status), 'customer'=>$customer->id, 'model'=>$model['id']]);
												return '<a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
											}
										],			            
										[
											'format' => 'raw',
											'label' => 'In Progress',
											'contentOptions' => ['style' => "text-align: center,"],
											'value' => function($model) use ($customer, $category){
												$_output = $model['inprogress_qty'];
												$_low_stock_style = 'color: #333';
												$_in_stock_style = 'font-weight: bold; color: #08c';
												$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;	
												$_url = Url::to(['/customers/statusdetails', 'status'=>array_search('In Progress', Item::$status), 'customer'=>$customer->id, 'model'=>$model['id']]);
												return '<a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';	   
											}
										],
										[
											'format' => 'raw',
											'label' => 'On-Site',
											'contentOptions' => ['style' => "text-align: center,"],
											'value' => function($model) use ($customer, $category){
												$_output = $model['shipped_qty'];
												$_low_stock_style = 'color: #333';
												$_in_stock_style = 'font-weight: bold; color: #08c';
												$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;	
												$_url = Url::to(['/customers/statusdetails', 'model'=>$model['id'], 'customer'=>$customer->id, 'status'=>array_search('Shipped', Item::$status)]);
												return '<a <a href="'.$_url.'" tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
											}
										],
										[
											'format' => 'raw',
											'label' => 'Total',
											'contentOptions' => ['style' => "text-align: center,"],
											'value' => function($model) use ($customer, $category){
												$_output = $model['total'];
												$_low_stock_style = 'color: #333';
												$_in_stock_style = 'font-weight: bold; color: #08c';
												$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
												return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';		   
											}
										],
										/*[
											'format' => 'raw',
											'label' => 'Department',
											'contentOptions' => ['style' => "text-align: center;"],
											'value' => function($model) {
												$url = 'javascript:;';
												$options = [
													'title' => Yii::t('app', 'Edit Category'),
													'onClick' => 'editCategory(' . $model['id'] . ')',
													'class' => 'glyphicon glyphicon-edit',
													'style' => 'line-height:16px;'
												];														
												return '<div style="font-weight:bold;"><div id="department-'. $model['id'] .'">' . strtoupper($model['department']). '</div><div>' .Html::a('', $url, $options) . ' <span id="category-'. $model['id'] .'">' . ucfirst(strtolower($model['categoryname'])). '</span></div></div>';
											}
										],											
										[
											'class' => 'yii\grid\ActionColumn',
											'template' => '{receive}',
											'buttons' => [
														'receive' => function ($url, $model, $key) {	
															//$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model->id]);
															$url = 'javascript:;';
															$options = [
																'title' => Yii::t('app', 'Receive'),
																'id' => 'receive-btn-' . $model['id'],
																'onClick' => 'receiveModels(' . $model['id'] . ')',
																'class' => 'btn btn-info glyphicon glyphicon-tasks',
															];
															return Html::a('', $url, $options);
														}																
													]
												],*/
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