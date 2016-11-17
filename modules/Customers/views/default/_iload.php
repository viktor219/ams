<?php 
	use common\helpers\CssHelper;
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\models\Location;
	use app\models\Medias;
	use app\models\Customer;
	use yii\widgets\ActiveForm;
?>

				<?php /*=
			    GridView::widget([
			        'dataProvider' => $dataProvider,
			        //'filterModel' => $searchModel,
			        'summary' => false,
			        'columns' => [
			            [
			    			'attribute' => 'companyname',
			                'label' => Yii::t('app', 'Customer'), 
			    			'format' => 'raw',
			                'value' => function($model) {
			                    $_my_media = Medias::findOne($model->picture_id);
			                    
			                    if(!empty($_my_media->filename)){
			                    	$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
			                    	if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) {
			                    
			                    		return  Html::img($target_file, ['alt'=>''.$model->companyname.'', 'height'=>'50px', 'class'=>'viewCustomer', 'style'=>'cursor:pointer;', 'cid'=>$model->id]);
			                    		 
			                    	}else{
			                    		return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;" cid="' . $model->id . '">' . $model->companyname . '</a>';
			                    	}
			                    
			                    }else {
			                    
			                    	return '<a href="javascript:;" class="viewCustomer" logo="'.$target_file.'" style="cursor:pointer;" cid="' . $model->id . '">' . $model->companyname . '</a>';
			                    }
			                    
			                }
			            ],
			            ['attribute' => 'totalLocation',
			                'label' => Yii::t('app', 'Location'),
			                'format'=>'html',
			                'value' => function($model) {
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
			                    return $_extra;
			                }
			            ],
			            ['attribute' => 'firstname',
			                'label' => Yii::t('app', 'Name'),
			                'format' => 'raw',
			                'value' => function($model) {
			                
			                    return "<div style='line-height: 40px;'>" .$model->firstname . ' ' . $model->lastname."</div>";
			                }
			            ],        
			            ['class' => 'yii\grid\ActionColumn',
			                'header' => Yii::t('app', 'Actions'),
			                'template' => '{view}',
			                'buttons' => [
			                             'view' => function ($url, $model, $key) {
			
			                                    $url = "javascript://";
			                                    $options = [
			                                        'title' => Yii::t('app', 'View Details'),
			                                        'class' => 'btn btn-info glyphicon glyphicon-eye-open',
			                                        'onClick' => 'loadCustomerModels('. $model->id .', "")'
			                                    ];
			                                     return Html::a('', $url, $options);
			                             }            
			                                     
			                        ]
			                    ], // ActionColumn
			                ], // columns
			            ]);*/
			            ?>