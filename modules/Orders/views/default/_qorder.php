<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Customer;
	use app\models\Location;
	use app\models\Medias;
	use app\models\Item;
	use app\models\QItemsordered;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Department;
	use app\models\User;
	use app\models\Ordertype;
	use app\models\QsalesorderMail;
	
?>
<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}
</style>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,					
				'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
				'summary'=>'', 
				'emptyText'=>'No orders available',
				'columns' => [
					[
						'attribute' => 'customer_id',
						'label' => 'Customer',
						'format' => 'raw',
						'value' => function($model) {
							$customer = Customer::findOne($model->customer_id);
							$m=$customer->picture_id;
							$picture = Medias::findOne($m);
							$link_picture = Yii::getAlias('@web').'/public/images/customers/'.$picture->filename;
							if($picture!==null && file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$picture->filename))
								return Html::img($link_picture, ['alt'=>'logo', 'style'=>'cursor:pointer;max-height:33px;', 'class'=>'showCustomer', 'uid'=>$model->customer_id]);
							else 
								return '<div style="line-height:40px;">' . $customer->companyname . '</div>';
							}
					],
					[
						'label' => 'Order Number',
						'format' => 'raw',
						'attribute' => 'number_generated',
						'value' => function($model) {
							return '<div style="line-height:40px;">' . $model->number_generated . '</div>';
						}
					],
					[
						'header' => 'Total Quantity',
						'format' => 'raw',
						'value' => function($model) {
							$qty = QItemsordered::find()->where(['ordernumber'=>$model->id])->sum('qty');
							$output = '<a tabindex="0" class="btn btn-sm btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/qgetorderindexdetails?type=2&idorder='.$model->id.'" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $qty .')" data-content="' . Html::encode($content) . '" style="color:#08c;">' . $qty . '</a>';
							return '<div style="line-height:40px;">' . $output. '</div>';
						}
					],
					[
						'attribute' => 'shipby',
						'format' => 'raw',
						'label' => 'Ship By',
						'value' => function($model) {
							if($model->shipby){
								$shipby = strtotime($model->shipby);
								return '<div style="line-height:40px;">' . date('m/d/Y', $shipby) . '</div>';
							}
						}
					],
					[
						'attribute' => 'created_at',
						'label' => 'Created',
						'format' => 'raw',
						'value' => function($model) {
							if(!empty($model->created_at) && $model->created_at != '0000-00-00 00:00:00')
								$created_at = date('m/d/Y', strtotime($model->created_at));
							else 
								$created_at = '-';
							return '<div style="line-height:40px;">' . $created_at . '</div>';
						}
					],
					(Yii::$app->user->identity->usertype===User::TYPE_ADMIN) ? 
					[
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{viewpdf} {sendmail} {conversion} {update} {delete}',
						'contentOptions' => ['style' => 'width:240px;line-height: 40px;', 'class' => 'action-buttons'],
						'controller' => 'orders',
						'buttons' => [
							'viewpdf' => function ($url, $model, $key) {
								$options = [
									'title' => 'View PDF',
									'class' => 'btn btn-sm btn-info',
									'target' => '_blank'
								];
								$url = \yii\helpers\Url::toRoute(['/orders/qogenerate', 'id'=>$model->id]);

								return Html::a('<i class="fa fa-file-pdf-o" aria-hidden="true"></i>', $url, $options);
							},		
							'sendmail' => function ($url, $model, $key) {//1=>order, 2=>purchasing order, 3=>quoteorder
								$is_mail_already_sent = QsalesorderMail::find()->where(['orderid'=>$model->id])->count();
								$options = [
									'title' => 'Send Email',
									'class' => (!$is_mail_already_sent) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-success',
									'id' => 'q-s-mail-button-' . $model->id,
									'onClick' => 'openMailer('. $model->id .', 3)'
								];
								$url = 'javascript:;';

								return Html::a('<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>', $url, $options);
							},							
							'conversion' => function ($url, $model, $key) {
								$options = [
									'title' => 'Convert to Order',
									'class' => 'btn btn-sm btn-success qoconvert',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/quotetoorder', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-share" aria-hidden="true"></span>', $url, $options);
							},
							'update' => function ($url, $model, $key) {
								$options = [
									'title' => 'Edit',
									'class' => 'btn btn-sm btn-warning',
									'data-content'=>'Edit Order',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/qupdate', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
							},
							'delete' => function ($url, $model, $key) {
								$options = [
									'title' => 'Delete',
									'class' => 'btn btn-sm btn-danger',
									//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
									'id' => 'soft_delete_qorder',
								];
								$url = \yii\helpers\Url::toRoute(['/orders/qsdelete', 'id'=>$model->id]);

								return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
							}
						],
					]:[],
				],
			]); ?>