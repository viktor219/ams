<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	
	use yii\grid\GridView;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Item;
	use app\models\Department;
?>
<div id="gridview-<?= $category->id; ?>">
	<?=
		GridView::widget([
			'dataProvider' => $dataProvider,
			'summary' => false,
			'columns' => [
				[
					'format' => 'raw',
					'label' => 'Description',
					'value' => function($model, $key, $index, $column) {
						return '<div style="line-height: 40px;font-weight:bold;">' . $model['name'] . ' ' . $model['descrip'] . '</div>';
					}
				],
				[
					'format' => 'raw',
					'label' => 'In Stock',
					'value' => function($model) use ($customer, $category){
						$_output = $model['instock_qty'];
						$_low_stock_style = 'color: #333';
						$_in_stock_style = 'font-weight: bold; color: #08c';
						$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
						return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
					}
				],			            
				[
					'format' => 'raw',
					'label' => 'In Progress',
					'value' => function($model) use ($customer, $category){
						$_output = $model['inprogress_qty'];
						$_low_stock_style = 'color: #333';
						$_in_stock_style = 'font-weight: bold; color: #08c';
						$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
						return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';	   
					}
				],
				[
					'format' => 'raw',
					'label' => 'Shipped',
					'value' => function($model) use ($customer, $category){
						$_output = $model['shipped_qty'];
						$_low_stock_style = 'color: #333';
						$_in_stock_style = 'font-weight: bold; color: #08c';
						$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
						return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';
					}
				],
				[
					'format' => 'raw',
					'label' => 'Total',
					'value' => function($model) use ($customer, $category){
						$_output = $model['total'];
						$_low_stock_style = 'color: #333';
						$_in_stock_style = 'font-weight: bold; color: #08c';
						$_button_style = ($_output > 10) ? $_in_stock_style : $_low_stock_style;															   
						return '<a tabindex="0" class="btn btn-default" style="'. $_button_style .'">' . $_output . '</a>';		   
					}
				],
				[
					'format' => 'raw',
					'label' => 'Department',
					'value' => function($model) {
						return '<div style="line-height: 40px;font-weight:bold;">' . $model['department']. '</div>';
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
										'id' => 'receive-btn-' . $model->id,
										'onClick' => 'receiveModels(' . $model->id . ')',
										'class' => 'btn btn-info',
									];
									return Html::a('Receive', $url, $options);
								}			                             
							]
						], // ActionColumn
					], // columns
				]);
				?>								
</div>