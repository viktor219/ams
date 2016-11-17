 <?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    	'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
    		[
            	//'attribute'=>'categoryname',
    			'label'=>'Category',
    			'format'=>"raw",
    			'value' => function ($model) {    			
    				return $model->categoryname;
    			}
    		],
            [
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{view}',
				'buttons' => [
					'view' => function ($url, $model, $key) {
						$options = [
							'title' => 'View',
							'class' => 'btn btn-info',
							'data-content'=>'View Details',
							'onClick' => 'loadCategoryModels('. $model->id .', "")'
						];
						$url = 'javascript:;';
					
						return Html::a('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>', $url, $options);
					}
				]
			],
        ],
    ]); ?>