<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\models\UserLogLocationTracking;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Login Trackings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-tracking-index">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-6 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?>
					</h4>
				</div>
			</div>
		</div>
		<div class="panel-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
		'summary'=>'', 
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'userid',
				'value' => function($model) {
					if($model->userid===null)
						return 'Anonymous';
					else
						return User::findOne($model->userid)->firstname . ' ' . User::findOne($model->userid)->lastname;
				}
			],
            'real_ip_address',
            'browser:ntext',
			[
				'attribute' => 'using_proxy',
				'format' => 'raw',
				'value' => function($model) {
					if ($model->using_proxy)  
						$output = '<span class="red-small">yes</span>';
					else
						$output = '<span class="green-small">no</span>';
					return $output;
				}
			],
            'device_type',
			[
				'attribute' => 'status',
				'format' => 'raw',
				'value' => function($model) {
					if ($model->status)  
						$output = '<span class="green-small">Success</span>';
					else
						$output = '<span class="red-small">Failed</span>';
					return $output;
				}
			],
            'created_at',
            [
				'class' => 'yii\grid\ActionColumn',
				'template'=>'{viewlocation} {delete}',
                'buttons' => [
                    'viewlocation' => function ($url, $model, $key) {      
                        return Html::a('', Yii::$app->request->baseUrl . "/userloglocation/view/?id=" . $model->location_id , ['title' => Yii::t('app', 'View User Location'), 'class' => 'glyphicon glyphicon-globe']);                 
                    }, 
                    /*'view' => function ($url, $model, $key) {
                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View Log #' . $model->id), 'class' => 'glyphicon glyphicon-eye-open viewTracking','tid'=>$model->id]);
                    },*/
                    'delete' => function ($url, $model, $key) {
                        return Html::a('', Yii::$app->request->baseUrl . "/userlog/delete/?id=" . $model->id, ['data-method'=>'post', 'title' => Yii::t('app', 'Remove Log #' . $model->id), 'onClick' => "return confirm('Are you sure to delete this record ?');", 'class' => 'glyphicon glyphicon-trash']);
                    },
                ]
			],
        ],
    ]); ?>

		</div>
	</div>
</div>