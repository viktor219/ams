<?php 
use yii\helpers\Html;
use yii\grid\GridView;
?>
<?php echo  GridView::widget([
        'dataProvider' => $dataProvider,
    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    	'summary'=>'',    		
        'columns' => [
            'vendorid',
            'vendorname',
            'address_line_1',
            'city',
            'zip',
            'state',
            'contact',
            [
				'attribute' => 'telephone_1',
				'label' => 'Telephone',									
			],
            'email:email',					
			[
				'class' => 'yii\grid\ActionColumn',
				'template'=> '{view} {update} {delete}',
				'contentOptions' => ['style' => "width:150px;line-height: 40px;"],
				'buttons' => [
					'view' => function ($url, $model, $key) {
						$options = [
							'title' => 'View',
							'class' => 'btn btn-sm btn-info',
						];
						$url = \yii\helpers\Url::toRoute(['/vendor/view', 'id'=>$model->id]);
					
						return Html::a('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>', $url, $options);
					},
					'update' => function ($url, $model, $key) {
						$options = [
							'title' => 'Edit',
							'class' => 'btn btn-sm btn-warning',
						];
						$url = \yii\helpers\Url::toRoute(['/vendor/update', 'id'=>$model->id]);
					
						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
					},
					'delete' => function ($url, $model, $key) {
						$options = [
							'title' => 'Delete',
							'class' => 'btn btn-sm btn-danger',
							'id' => 'soft_delete_order',
						];
						$url = \yii\helpers\Url::toRoute(['/vendor/delete', 'id'=>$model->id]);
					
						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
					}
				],
			],
        ],
    ]); ?>