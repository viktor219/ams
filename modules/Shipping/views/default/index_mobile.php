<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Location;
use app\models\Medias;
$this->title = Yii::t('app', 'Shipping');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
      
     <?= Yii::$app->session->getFlash('error'); ?>
     <?= Yii::$app->session->getFlash('success'); ?>
    <h1>

        <?= Html::encode($this->title) ?>      

    </h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'showHeader'=>false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            ['attribute' => 'picture_id',
                'label' => Yii::t('app', 'Picture'),
                'format'=>'html',
                'value' => function($model) {
            
                    $_my_media = Medias::findOne($model->picture_id);
                    
                    if(isset($_my_media->filename) && !empty($_my_media->filename)){
                        $target_file = Yii::getAlias('@webroot').'/'.$_my_media->path.'/'.$_my_media->filename;
                        if (file_exists($target_file)) {
                            
                             $_my_image = '<img src="'.Yii::$app->request->baseUrl.'/'.$_my_media->path.'/'.$_my_media->filename.'" alt="picture" style="width:124px;height:128px;">';
                   
                        }else{
                           $_my_image = '<img src="'.Yii::$app->request->baseUrl.'/'.'uploads/customers'.'/'.'asset_logo.jpg'.'" alt="picture" style="width:124px;height:128px;">';
                        }
                        
                    }else {
                        
                        $_my_image = '<img src="'.Yii::$app->request->baseUrl.'/'.'uploads/customers'.'/'.'asset_logo.jpg'.'" alt="picture" style="width:124px;height:128px;">';
                    }
                    return Yii::$app->formatter->asHtml($_my_image);
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'template' => '{showallproject}',
                'buttons' => [
                   
                       
                         'showallproject' => function ($url, $model, $key) {

                                $url = Yii::$app->request->baseUrl."/shipping/default/shipments/?customer=".$model->id;
                                $options = [
                                    'title' => Yii::t('app', 'Shipments'),
                                    'id' => $model->id,
                                    'class' => 'btn btn-info btn-xs btnShowAllProject',
                                    'style' => 'margin-top:10px;float:right;'
                                ];
                                 return Html::a('Shipments', $url, $options);
                         }   
                             
                             
                                     
                        ]
                    ], // ActionColumn
                ], // columns
            ]);
            ?>

        </div>




