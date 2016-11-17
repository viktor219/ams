<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Item;
use app\models\Models;
use app\models\Manufacturer;
use app\models\User;
use app\modules\Orders\models\Order;
use app\models\Purchase;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Itemlogs';
$this->params['breadcrumbs'][] = ['label' => 'In Progress', 'url' => ['inprogress/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="itemlog-index">
                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-9 vcenter">
									<h4><span class="glyphicon glyphicon-tags"></span> <?= Html::encode($this->title) ?></h4>
                                </div>
                            </div>
                        </div>
                    <div class="panel-body" style="padding: 15px 0 0 0">
						<div class="row row-margin">
						    <?= GridView::widget([
						        'dataProvider' => $dataProvider,
						    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
						        'columns' => [
						    		[
						    			'label' => 'Order',
						    			'format' => 'raw',
						    			'value' => function ($model) {
						    				$_item = Item::findOne($model->itemid);
						    				if(!empty($_item->shipmentnumber))
						    					$_order = Order::findOne($_item->shipmentnumber);
						    				else 
						    					$_order = Purchase::findOne($_item->purchaseordernumber);
						    				if(!empty($_order))
						    					$numbergenerated = $_order->number_generated;
						    				else
						    					$numbergenerated = "Not from any order";
						    				return '<div style="line-height:40px;">' . $numbergenerated . '</div>';
						   	 			}
						    		],
									[
										'label' => 'Item',
										'format' => 'raw',
						            	'attribute' => 'itemid',
										'value' => function ($model) {
											$_item = Item::findOne($model->itemid);
											$_model = Models::findOne($_item->model);
											$_manufacturer = Manufacturer::findOne($_model->manufacturer);
											//
											return '<div style="line-height:40px;">' . $_manufacturer->name . ' ' . $_model->descrip . '</div>';
										}
									],
									[
						            	'attribute' => 'status',
										'format' => 'raw',
										'value' => function ($model) {
											return '<div style="color:#08c;font-weight:bold;line-height:40px;">' . Item::$status[$model->status] . '</div>';
										}
									],
									[
						            	'attribute' => 'userid',
						            	'format' => 'raw',
										'value' => function ($model) {
											$_user = User::findOne($model->userid);
											return '<div style="line-height:40px;">' . $_user->firstname . ' ' . $_user->lastname. '</div>';
										}
									],
						            [
						            	'attribute' => 'created_at',
						            	'format' => 'raw',
						            	'value' => function ($model) {
						            		return '<div style="line-height:40px;">' . $model->created_at . '</div>';
						            	}						            	
						            ],					
									[
										'class' => 'yii\grid\ActionColumn',
										'template'=>'{view}',
										'controller' => 'itemlog',
										'buttons' => [
											'view' => function ($url, $model, $key) {
												$options = [
													'title' => 'View',
													'class' => 'btn btn-info',
													'type'=>'button'
												];
												$url = \yii\helpers\Url::toRoute(['/itemlog/view', 'id'=>$model->id]);
				
												return Html::a('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>', $url, $options);
											}
										],
									]
						        ],
						    ]); ?>
						    
						</div>
					</div>
				</div>

</div>