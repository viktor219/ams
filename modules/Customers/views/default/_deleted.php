<?php 
	use common\helpers\CssHelper;
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Location;
	use app\models\Medias;
	use yii\widgets\ActiveForm;
	
?>
			<?=
			    GridView::widget([
			        'dataProvider' => $dataProvider,
			        //'filterModel' => $searchModel,
			        'summary' => false,
			        'columns' => [
			           // ['class' => 'yii\grid\SerialColumn'],
			            [
			    			'attribute' => 'companyname',
			                'label' => Yii::t('app', 'Customer'), 
			    			'contentOptions' => ['style' => 'width:200px;'],
			    			'format' => 'raw',
			                'value' => function($model) {
			                    $_my_media = Medias::findOne($model['picture_id']);
			                    
			                    if(!empty($_my_media->filename)){
			                    	$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
			                    	if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) {
			                    
			                    		return  Html::img($target_file, ['alt'=>$model['companyname'], 'class'=>'viewCustomer', 'style'=>'cursor:pointer;max-width:90px;max-height:35px;', 'cid'=>$model['id']]);
			                    		 
			                    	}else{
			                    		return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $model['id'] . '">' . $model['companyname'] . '</a>';
			                    	}
			                    
			                    }else {
			                    
			                    	return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $model['id'] . '">' . $model['companyname'] . '</a>';
			                    }
			                    
			                }
			            ],
			            ['attribute' => 'code',
			            'label' => Yii::t('app', 'Code'),
			            'format' => 'raw',
			            'value' => function($model) {
			            	 
			            	return "<div style='line-height: 40px;'>" . $model['code'] . "</div>";
			            }
			            ],			            
			            ['attribute' => 'companyname',
			            'label' => Yii::t('app', 'Name'),
			            'contentOptions' => ['style' => 'width:150px;'],
			            'format' => 'raw',
			            'value' => function($model) {
			            	 
			            	return "<div style='line-height: 40px;'>" . $model['firstname'] . " " . $model['lastname'] . "</div>";
			            }
			            ],	
			            ['attribute' => 'phone',
			            'label' => Yii::t('app', 'Phone'),
			            'contentOptions' => ['style' => 'width:150px;'],
			            'format' => 'raw',
			            'value' => function($model) {
			            	 
			            	return "<div style='line-height: 40px;'>" . $model['phone'] . "</div>";
			            }
			            ],	
			            ['attribute' => 'email',
			            'label' => Yii::t('app', 'Email'),
			            'format' => 'raw',
                                    'contentOptions' => ['style' => 'width:250px;'],
			            'value' => function($model) {
			            	 
			            	return "<div style='line-height: 40px;'>" . str_replace(';', '</br>', $model['email']) . "</div>";
			            }
			            ],			            		          		            
			            /*['attribute' => 'totalLocation',
			                'label' => Yii::t('app', 'Locations'),
			                'format'=>'html',
			                'value' => function($model) {
			                    $count = Location::find()->where(['customer_id' => $model->id])->count();
			                    $_btn_location = '&nbsp;<a class="glyphicon glyphicon-eye-open btn btn-primary viewAllLocations" href="'.Yii::$app->request->baseUrl.'/customers/default/locations/?customer='.$model->id.'"></a>';
			                    
			                    $_extra = '';
			                    if(($model->defaultshippinglocation == $model->defaultbillinglocation) && $model->defaultshippinglocation  > 0){
			                        
			                        $_dlocationOne = Location::findOne($model->defaultshippinglocation);
			                        if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
			                            $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address." ".$_dlocationOne->city." ".$_dlocationOne->state." ".$_dlocationOne->zipcode;
			                        }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
			                            $_extra = $_extra." ".$_dlocationOne->address." ".$_dlocationOne->city." ".$_dlocationOne->state." ".$_dlocationOne->zipcode;
			                        }
			                        
			                    }else{ 
			                        
			                        if($model->defaultshippinglocation  > 0){ 
			                            
			                            $_dlocationOne = Location::findOne($model->defaultshippinglocation);
			                            if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
			                                $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address;
			                            }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
			                                $_extra = $_extra." ".$_dlocationOne->address." ".$_dlocationOne->city." ".$_dlocationOne->state." ".$_dlocationOne->zipcode;
			                            }
			                        }
			                        if($model->defaultbillinglocation  > 0){
			                            
			                            $_dlocationOne = Location::findOne($model->defaultbillinglocation);
			                            if(isset($_dlocationOne->storenum) && !empty($_dlocationOne->storenum)){
			                                $_extra = $_extra." Store#: ".$_dlocationOne->storenum.$_dlocationOne->address;
			                            }else if(isset($_dlocationOne->address) && !empty($_dlocationOne->address)){
			                                $_extra = $_extra." ".$_dlocationOne->address." ".$_dlocationOne->city." ".$_dlocationOne->state." ".$_dlocationOne->zipcode;
			                            }
			                        }
			                    }
			                    return "" . Yii::$app->formatter->asHtml($_btn_location). " $_extra";
			                }
			            ], */      
			            ['class' => 'yii\grid\ActionColumn',
			                'header' => '',
			                'template' => '{revert} {delete}',
			                'contentOptions' => ['style' => 'width:220px;', 'class' => 'action-buttons'],
			                'buttons' => [
			                            'revert' => function ($url, $model, $key) {
                                                            $options = [
                                                                    'title' => 'Revert',
                                                                    'class' => 'btn btn-info revertCustomer',
                                                            ];
                                                            $url = \yii\helpers\Url::toRoute(['/customers/revert', 'id'=>$model->id]);
                                                            return Html::a('<span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>', $url, $options);
                                                    },
                                                    'delete' => function ($url, $model, $key) {
                                                            $options = [
                                                                    'title' => 'Delete',
                                                                    'class' => 'btn btn-danger deleteCustomer',
                                                                    //'data-method' => 'post'
                                                            ];
                                                            $url = \yii\helpers\Url::toRoute(['/customers/delete', 'id'=>$model->id]);

                                                            return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
                                                    }     
			                        ]
			                    ], // ActionColumn
			                ], // columns
			            ]);
			            ?>