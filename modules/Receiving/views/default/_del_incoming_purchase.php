<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Vendor;
use app\models\Item;
use app\models\Manufacturer;
?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        'number_generated',
        [
            'attribute' => 'vendor_id',
            'label' => 'Vendor',
            'value' => function($model) {
                return Vendor::findOne($model->vendor_id)->vendorname;
            }
        ],
        [
            'label' => 'Qty',
            'format' => 'raw',
            'value' => function($model) {
                return Item::find()->where(['status' => array_search('In Transit', Item::$status), 'purchaseordernumber' => $model->id])->count();
            }
                ],
                [
                    'attribute' => 'estimated_time',
                    'value' => function($model) {
                        if ($model->estimated_time) {
                            return date('m/d/Y', strtotime($model->estimated_time));
                        }
                    }
                ],
                'trackingnumber',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{revert} {delete}',
                    'controller' => 'orders',
                    'contentOptions' => ['class' => 'action-buttons'],
                    'buttons' => [
                         'revert' => function ($url, $model, $key) {
                            $options = [
                                    'title' => 'Revert',
                                    'class' => 'btn btn-info revertPurchase',
                            ];
                            $url = \yii\helpers\Url::toRoute(['/receiving/revertreceive', 'id'=>$model->id]);

                            return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
			},
                                'delete' => function ($url, $model, $key) {
                            $options = [
                                'title' => 'Delete',
                                'class' => 'btn btn-danger',
                                'data-content' => 'Delete Order',
                                'id' => 'delete_purchase',
                                'type' => 'button',
                                    //'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
                            ];
                            $url = \yii\helpers\Url::toRoute(['/receiving/rdelpurchase', 'id' => $model->id]);

                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                        }
                            ],
                        ]
                    ],
                ]);
?>