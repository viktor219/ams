<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Medias;
use app\models\Manufacturer;
use app\models\Category;
use app\models\Department;

/* @var $this yii\web\View */
/* @var $model app\models\Inventory */

$this->title = Manufacturer::findOne($model->manufacturer)->name . ' ' . $model->descrip;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inventories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'aei',
			[
				'label' => 'Image',
				'format' => 'raw',
				'value' => Html::img(Yii::getAlias('@web').'/public/images/models/'.Medias::findOne($model->image_id)->filename, ['alt'=>'logo', 'height'=>'100px', 'width'=>'100px']),
			],
            [
				'label' => 'Model',
				'value' => $this->title
			],
            [
				'label' => 'Departement',
				'value' => Department::findOne($model->department)->name
			],
            [
				'label' => 'Category',
				'value' => Category::findOne($model->category_id)->categoryname
			],
            'palletqtylimit',
            'stripcharacters',
            'checkit',
            'frupartnum',
            'manpartnum',
            'serialized',
            'storespecific',
            'quote',
            'created_at',
            'modified_at',
        ],
    ]) ?>

</div>
