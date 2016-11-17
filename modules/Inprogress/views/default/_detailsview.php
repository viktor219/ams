<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\grid\GridView;
	use app\models\Department;
	use app\models\Manufacturer;
	use app\models\Models;
	use app\models\Medias;
	use app\models\Item;
	use app\models\Itemstesting;
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
					return '<div style="line-height:40px;" id="description-item-' . $model['id'] . '">' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
				}
			],
			[
				'attribute'=>'serial',
				'format'=>'raw',
				'value'=>function ($model) {
					return '<div style="line-height:40px;" id="serial-item-' . $model['id'] . '">' . $model['serial'] . '</div>';
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
			'template'=>'{requestreplacement} {cleaning} {testing} {history} {ready}',
			'contentOptions' => ['style' => 'width:350px;'],
			'controller' => 'orders',
			'buttons' => [
				'requestreplacement' => function ($url, $model, $key) {					
					$options = [
						'title' => 'Request replacement',
						'class' => 'btn btn-sm btn-warning historyitem',
						'id' => 'request-item_' . $model['id'],
					];	
					
					$url = 'javascript:;';
								 
					return Html::a('<span class="glyphicon glyphicon-repeat"></span>', $url, $options);
				},
				'cleaning' => function ($url, $model, $key) {
					//check model.preowneditems
					//check Cleaning "full", "partial"
					$_model = Models::findOne($model['model']);
					
					$options = [
						'title' => 'Cleaning',
						'class' => 'btn btn-sm btn-default cleaningitem',
						'onClick' => 'loadConfirmCleaning('. $model['id'] .');',
						'id' => 'cleaning-item-' . $model['id']
					];
					
					$cleaninghasoption = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->where(['ordernumber'=>$model['ordernumber'], 'model'=>$_model->id])
										->andWhere(['orderid'=>$model['ordernumber'], 'itemid'=>$_model->id])
										->andWhere(['status'=>array_search('In Progress', Item::$status)])
										->andWhere('optionid IN (2,3)')
										->count();
					
					if(!$cleaninghasoption && !$_model->preowneditems || $model['status']>=array_search('Cleaned', Item::$status))
						$options['disabled'] = true;					
							
					$url = 'javascript:;';
				 
					return Html::a(($model['status']>=array_search('Cleaned', Item::$status)) ? 'Cleaned' : 'Cleaning', $url, $options);
				},
				'testing' => function ($url, $model, $key) {
					$_model = Models::findOne($model['model']);
					//$status = array_keys(Item::$testingstatus);
					//check models.requiretestingreferb
					//check testing except "as-is"
					$options = [
						'title' => 'Testing',
						'class' => 'btn btn-sm btn-primary testingitem',
						'id' => 'testing-item_' . $model['id'],
						'onClick' => 'loadTestingModal('. $model['id'] .')'
					];
					
					$testinghasoption = Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
													->andWhere(['ordernumber'=>$model['ordernumber'], 'orderid'=>$model['ordernumber'], 'itemid'=>$_model->id])
													->andWhere('optionid IN (47, 48)')
													->count();
					
					if((!$testinghasoption && !$_model->requiretestingreferb) || ($model['status'] != array_search('Cleaned', Item::$status)))
						$options['disabled'] = true;
						
					$url = 'javascript:;';
				
					return Html::a(($model['status'] == array_search('Requested for Service', Item::$status) || ($model['status'] == array_search('Used for Service', Item::$status))) ? 'Tested' : 'Testing', $url, $options);
				},
				'history' => function ($url, $model, $key) {
					$has_history = Itemstesting::find()->where(['itemid'=>$model['id']])->count();
					
					$options = [
						'title' => 'History',
						'class' => 'btn btn-sm btn-info historyitem',
						'id' => 'history-item_' . $model['id'],
					];	
					
					if(!$has_history)
						$options['disabled'] = true;
					
					$url = 'javascript:;';
								
					return Html::a('History', $url, $options);
				},
				'ready' => function ($url, $model, $key) {
					$options = [
						'title' => 'Ready',
						'class' => 'btn btn-sm btn-warning',
						'id' => 'ready-item-' . $model['id']
					];

					if($model['status'] != array_search('Used for Service', Item::$status) && $model['status']!=array_search('Serviced', Item::$status))
						$options['disabled'] = true;
					
					if(in_array($model['status'], array_keys(Item::$readystatus)))
						$options['class'] = 'btn btn-sm btn-success';
					
					//$url = \yii\helpers\Url::toRoute(['/inprogress/turnmodelonship', 'id'=>$model['id']]);
					
					$url = 'javascript:;';
				
					return Html::a('<span class="glyphicon glyphicon-ok-sign"></span>', $url, $options);
				}
			],
			]					
        ],
    ]); ?>