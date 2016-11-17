<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Manufacturer;
use app\models\Model;
use app\models\Medias;
use app\models\Partnumber;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Picklist';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['/orders/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			[
				'label' => 'Part Number',
				'value' => function($model) {
					$customer = $model->customer;
					return Partnumber::find()->where(['customer'=>$customer, 'model'=>$model->model])->partid;
				}
			],
			[
				'label' => 'Item',
				'attribute' => 'model',
				'value' => function($model) {
					return Manufacturer::findOne(Model::findOne($model->model)->manufacturer)->name . ' ' . Model::findOne($model->model)->descrip;
				}
			],
			[
				'label' => 'Qty',
				'value' => function($model) {
					return Item::find()->where(['model'=>$model->model])->count();
				}
			],
			[
				'label' => 'Pick Icon',
				'value' => function($model) {
					$picture_id = Model::findOne($model->model)->image_id;
					$filename = Medias::findOne($picture_id)->filename;
					return Html::img(Yii::getAlias('@web').'/public/images/models/'. $filename, ['alt'=>'logo', 'height'=>'60px', 'width'=>'60px']);
				}
			],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
