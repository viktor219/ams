<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Users;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mail Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-mail-account-index">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-2 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?>
					</h4>
				</div>
				<div class="col-md-10 vcenter text-right">
					 <?= Html::a('<span class="glyphicon glyphicon-plus"></span> New Mail Account', ['create'], ['class' => 'btn btn-success']) ?>
				</div>
			</div>
		</div>
		<div class="panel-body" >
		    <?= GridView::widget([
		        'dataProvider' => $dataProvider,
		    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
		    	'summary'=>'',
		        'columns' => [
		           // ['class' => 'yii\grid\SerialColumn'],
		
					[
						'label'=>'Name',
						'value'=> function($model) {
							$user = Users::findOne($model->userid);
							return $user->firstname . ' ' . $user->lastname;
						}
					],
					[
						'label'=>'Email',
						'value'=> function($model) {
							$user = Users::findOne($model->userid);
							return $user->email;
						}
					],					
		            'password',
		
				[
					'class' => 'yii\grid\ActionColumn',
					'template'=>'{view} {update} {delete}',
					'controller' => 'orders',
					'buttons' => [
						'view' => function ($url, $model, $key) {
							$options = [
								'title' => 'View',
								'class' => 'btn btn-primary'
							];
							$url = \yii\helpers\Url::toRoute(['/usermailaccount/view', 'id'=>$model->userid]);
								
							return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, $options);
						},
						'update' => function ($url, $model, $key) {
							$options = [
								'title' => 'Edit',
								'class' => 'btn btn-warning',
								'type'=>'button'
							];
							$url = Url::toRoute(['/usermailaccount/update', 'id'=>$model->userid]);
								
							return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
						},
						'delete' => function ($url, $model, $key) {
							$options = [
								'title' => 'Delete',
								'class' => 'btn btn-danger',
								'data-content'=>'Delete Order',
								'type'=>'button',
								'onClick'=> 'return confirm(\'are you sure to delete this item ?\');',
							];
							$url = Url::toRoute(['/usermailaccount/delete', 'id'=>$model->userid]);
								
							return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
						}
					],
					]
		        ],
		    ]); ?>		
		</div>
	</div>
</div>
