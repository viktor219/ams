<?php 
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Department;
use yii\helpers\ArrayHelper;
?>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'summary' => '',
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'Model',
            'format' => 'raw',
            'value' => function ($model) {
                $_model = Models::findOne($model['id']);
                $_manufacturer = Manufacturer::findOne($_model->manufacturer);
                return '<div>' . $_manufacturer['name'] . ' ' . $model['descrip'] . '</div>';
            }
        ],
        [
            'attribute' => 'department',
            'format' => 'raw',
            'value' => function ($model) {
                $departments = Department::find()->where('id <=7')->all();
                $departmentList = ArrayHelper::map($departments, 'id', 'name');
                return yii\bootstrap\Html::dropDownList('department', $model['department'], $departmentList, ['class' => 'select_department form-control', 'style' => 'width: 150px', 'modelid' => $model['id'], 'prompt'=>'-Select Department-']);
            }
                ]
            ],
        ]);
?>	