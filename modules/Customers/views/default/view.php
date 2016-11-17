<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Location;
use app\models\Medias;
?>
<div class="user-view">
    <?php $_t_locations =  Location::find()->where(['customer_id' => $model->id])->count();?>
<div style="text-align:center">
    <?php 
    	$m=$model->picture_id;
		$picture = Medias::findOne($m);
		if($picture!==null)
			echo Html::img(Yii::getAlias('@web').'/public/images/customers/'.$picture->filename, ['alt'=>'logo', 'height'=>'120px', 'width'=>'240px']);
	?>
</div>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
    			'attribute' => 'companyname',
                'label' => Yii::t('app', 'Customer'),
            ],
            ['attribute' => 'contactname',
                'label' => Yii::t('app', 'Locations'),
                'value' =>$_t_locations,
            ],
            ['attribute' => 'firstname',
                'label' => Yii::t('app', 'First Name'),
            ],
            ['attribute' => 'lastname',
                'label' => Yii::t('app', 'Last Name'),
            ],
            ['attribute' => 'phone',
                'label' => Yii::t('app', 'Phone'),
            ],
            ['attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
            ],
            ['attribute' => 'trackincomingserials',
                'label' => Yii::t('app', 'Serials'),
                'value' =>!empty($model->trackincomingserials) ? 'Yes' : 'No',
            ],
        ],
    ])
    ?>


</div>
