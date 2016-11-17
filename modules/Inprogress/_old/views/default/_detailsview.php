<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\Department;
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Medias;
	use app\models\Item;
?>

	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
		'summary'=>'', 
        'columns' => [
			[
				'attribute' => 'imagepath',
				'contentOptions' => ['style' => 'vertical-align:middle'],
				'label' => 'Thumbnail',
				'format' => 'raw',
				'value' => function($model) {
					$_model = Models::findOne($model['model']);
					$_media = Medias::findOne($_model->image_id);
					if($_media['filename'])
						return Html::img(Yii::getAlias('@web').'/public/images/models/'. $_media['filename'], ['alt'=>'logo', 'onClick'=>'ModelsViewer(' . $_model['id'] . ');', 'height'=>'33px']);
				}
			],
			[
				'attribute'=>'model',
				'label'=>'Description',
    			'format'=>'raw',
				'value'=>function ($model) {
					$_model = Models::findOne($model['model']);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
					return '<div style="line-height:40px;">' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
				}
			],
			[
				'attribute'=>'serial',
				'format'=>'raw',
				'value'=>function ($model) {
					return '<div style="line-height:40px;">' . $model['serial'] . '</div>';
				}
			],
			[
				'attribute'=>'Department',
				'format'=>'raw',
				'value'=>function ($model) {
					$_model = Models::findOne($model['model']);
					$_department = Department::findOne($_model->department);
					return '<div style="line-height:40px;">' . $_department->name . '</div>';
				}
			],			
			[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{cleaning} {testing} {ready}',
			'contentOptions' => ['style' => 'width:320px;'],
			'controller' => 'orders',
			'buttons' => [
				'cleaning' => function ($url, $model, $key) {
					//check model.preowneditems
					//check Cleaning "full", "partial"
					$_model = Models::findOne($model['model']);
					
					$options = [
						'title' => 'Cleaning',
						'class' => 'btn btn-default',
						'onClick' => 'loadConfirmCleaning('. $model['id'] .');',
						'id' => 'cleaning-item-' . $model['id']
					];
					
					$cleaninghasoption = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->where(['ordernumber'=>$model['ordernumber'], 'model'=>$_model->id])
										->andWhere(['orderid'=>$model['ordernumber'], 'itemid'=>$_model->id])
										->andWhere(['status'=>array_search('In Progress', Item::$status)])
										->andWhere('optionid IN (2,3)')
										->count();
					
					if(!$cleaninghasoption && !$_model->preowneditems || $model['status']==array_search('Cleaned', Item::$status))
						$options['disabled'] = true;					
							
					$url = 'javascript:;';
				 
					return Html::a(($model['status']==array_search('Cleaned', Item::$status)) ? 'Cleaned' : 'Cleaning', $url, $options);
				},
				'testing' => function ($url, $model, $key) {
					$_model = Models::findOne($model['model']);
					//check models.requiretestingreferb
					//check testing except "as-is"
					$options = [
						'title' => 'Testing',
						'class' => 'btn btn-primary',
						'onClick' => 'loadTestingModal('. $model['id'] .')'
					];
					
					$testinghasoption = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->where(['ordernumber'=>$model['ordernumber'], 'model'=>$_model->id, 'status'=>array_search('Requested for Service', Item::$status)])
										->orWhere(['ordernumber'=>$model['ordernumber'], 'model'=>$_model->id, 'status'=>array_search('Used for Service', Item::$status)])
										->andWhere(['orderid'=>$model['ordernumber'], 'itemid'=>$_model->id])
										->andWhere('optionid IN (47, 48)')
										->count();
					
					if((!$testinghasoption && !$_model->requiretestingreferb) || ($model['status'] == array_search('Requested for Service', Item::$status) || ($model['status'] == array_search('Used for Service', Item::$status))))
						$options['disabled'] = true;
						
					$url = 'javascript:;';
				
					return Html::a(($model['status'] == array_search('Requested for Service', Item::$status) || ($model['status'] == array_search('Used for Service', Item::$status))) ? 'Tested' : 'Testing', $url, $options);
				},
				'ready' => function ($url, $model, $key) {
					$options = [
						'title' => 'Ready',
						'class' => 'btn btn-warning'
					];

					if(!($model['status'] == array_search('Used for Service', Item::$status) || ($model['status'] != array_search('Used for Service', Item::$status) && $model['status']==array_search('Cleaned', Item::$status))))
						$options['disabled'] = true;
					
					$url = \yii\helpers\Url::toRoute(['/inprogress/turnmodelonship', 'id'=>$model['id']]);
				
					return Html::a('Ready', $url, $options);
				}
			],
			]					
        ],
    ]); ?>