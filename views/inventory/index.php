<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Department;
use app\models\Category;
use app\models\Inventory;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = "Inventory Models";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
    <!-- Sales Order Dashboard -->
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="col-md-9 vcenter">
                    <h4>
                        <span class="glyphicon glyphicon-list-alt"></span>
                        <?= Html::encode($this->title) ?>
                    </h4>
                </div>
                <div class="col-md-3 vcenter text-right">
                    <div class="col-md-6">
                        <?= Html::a('<span class="glyphicon glyphicon-plus"></span>New Model', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'summary'=>'',
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'modelname',
                        
                    ],
                    [
                        'attribute' => 'aeino',
                        'filter'=>false,
                    ],
                    [
                        'attribute' => 'manufacturer',
                        'value' => function($model) {
                            return Manufacturer::findOne($model->manufacturer)->name;
                        },
                        'filter'=>false,
                    ],
                    [
                        'attribute' => 'department',
                        'value' => function($model) {
                            return Department::findOne($model->department)->name;
                        },
                        'filter'=>false,
                    ],
                    [
                        'attribute' => 'category',
                        'value' => function($model) {
                            return Category::findOne($model->category)->categoryname;
                        },
                        'filter'=>false,
                    ],
                    [
                        'attribute' => 'istrackserial',
                        'value' => function($model) {
                            return $model->istrackserial == 1 ? 'Yes' : 'No';
                        },
                        'filter'=>false,
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        //'template'=>'{view}{update}{delete}',
                        'template'=>'{view}{update}{delete}',
                        'controller' => 'inventory',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                $options = [
                                    'title' => 'View',
                                    'class' => 'btn btn-info'
                                ];
                                $url = \yii\helpers\Url::toRoute(['inventory/view', 'id'=>$model->id]);

                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Details', $url, $options);
                            },
                            'update' => function ($url, $model, $key) {
                                $options = [
                                    'title' => 'Update',
                                    'class' => 'btn btn-primary'
                                ];
                                $url = \yii\helpers\Url::toRoute(['inventory/update', 'id'=>$model->id]);

                                return Html::a('<span class="glyphicon glyphicon-pencil"></span> Edit', $url, $options);
                            },
                            'delete' => function ($url, $model, $key) {
                                $options = [
                                    'title' => 'Delete',
                                    'class' => 'btn btn-warning',
                                    'data-method' => 'post',                                    

                                ];
                                $url = \yii\helpers\Url::toRoute(['inventory/delete', 'id'=>$model->id]);
                                //$url = \yii\helpers\Url::toRoute("#");

                                return Html::a('<span class="glyphicon glyphicon-trash"></span> Delete', $url, $options);
                            }
                        ],
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>
<!-- End -->
