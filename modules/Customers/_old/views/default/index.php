<?php

use common\helpers\CssHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Location;
use app\models\Medias;
use yii\widgets\ActiveForm;
//use app\vendor\TesseractOCR;

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;

//echo (new TesseractOCR('../testocr.png'))->executable('../vendor/tesseract')
//->run();
?>
<style> 
	td:nth-child(2) {text-align:left;}
	#customer-search-gridview thead {border-top: 1px solid rgb(221, 221, 221);}
	#customer-search-gridview .table-bordered {border:none;}
</style>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/customer.js"></script>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<div class="user-index">
<!-- Sales Order Dashboard -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-6 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?>
					</h4>
				</div>
				<div class="col-md-6 vcenter text-right">
					    <?php $form = ActiveForm::begin([
						        'action' => ['index'],
						        'method' => 'get',
						    ]); ?>
						<div id="searchcustomer-group" class="pull-right top_search">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-success" id="searchCustomerBtn" type="button"><b style="color:#FFF;">?</b></button> 
								</span>
								<input type="text" placeholder="Search" id="searchCustomer" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
								<span class="input-group-btn navigate-buttons">
									<?= Html::a('<span class="glyphicon glyphicon-user"></span> New Customer', ['/customers/create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
								</span>
							</div>
						</div>
					<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div id="main-gridview">
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
			            'value' => function($model) {
			            	 
			            	return "<div style='line-height: 40px; word-break: break-all'>" . str_replace(';', '</br>', $model['email']) . "</div>";
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
			                'template' => '{update} {delete} {viewlocations}',
			                'contentOptions' => ['style' => 'width:200px;', 'class' => 'action-buttons'],
			                'buttons' => [
			                   /* 'view' => function ($url, $model, $key) {
			                        return Html::a('', 'javascript://', ['title' => Yii::t('app', 'View Customer'),
			                                    'class' => 'glyphicon glyphicon-eye-open viewCustomer','cid'=>$model->id]);
			                    },*/
			                            'update' => function ($url, $model, $key) {
												$options = [
													'title' => Yii::t('app', 'Manage Customer'),
													'class' => 'btn btn-warning',
													'data-content'=>'Edit Order',
													'data-placement'=>'top',
													'data-toggle'=>'popover',
													'data-trigger'=>'hover',
													'type'=>'button'
												];
												$url = \yii\helpers\Url::toRoute(['/customers/update', 'id'=>$model['id']]);
												
												return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
			                    },
			                            'delete' => function ($url, $model, $key) {
			                        
			                        return Html::a('', "javascript://", ['title' => Yii::t('app', 'Delete Customer'),
			                                    'class' => 'glyphicon glyphicon-trash btn btn-danger deleteCustomer','cid'=>$model['id']]);
			                       
			                    },
			                            
			                            'addprojct' => function ($url, $model, $key) {
			
			                                    $url = "javascript://";
			                                    $options = [
			                                        'title' => Yii::t('app', 'Add Project'),
			                                        'id' => $model['id'],
			                                        'class' => 'btn btn-primary glyphicon glyphicon-plus btnAddProject'
			                                    ];
			                                   return Html::a('', $url, $options);
			                             },
			                             'showallproject' => function ($url, $model, $key) {
			
			                                    $url = "javascript://";
			                                    $options = [
			                                        'title' => Yii::t('app', 'Show All Project'),
			                                        'id' => $model['id'],
			                                        'class' => 'btn btn-info glyphicon glyphicon-eye-open btnShowAllProject',
			                                    ];
			                                     return Html::a('', $url, $options);
			                             },

			                             'viewlocations' => function ($url, $model, $key) {
			                             
			                             	$url = \yii\helpers\Url::toRoute(['/customers/locations', 'customer'=>$model['id']]);
			                             	$options = [
			                             	'title' => Yii::t('app', 'View Locations'),
			                             	'id' => $model['id'],
			                             	'class' => 'btn btn-info glyphicon glyphicon-eye-open',
			                             	];
			                             	return Html::a('', $url, $options);
			                             }			                             
			                                     
			                        ]
			                    ], // ActionColumn
			                ], // columns
			            ]);
			            ?>
					</div>
					<div id="search-gridview" style="display:none;">
            			<div class="col-md-12 col-sm-6 col-xs-12">
                 			<div class="x_panel">
                                <div class="x_content">
                                <!-- 
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="customer-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#customerhome" onClick="loadCustomers();" id="customer-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                        </ul>
                         				<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="customerhome" aria-labelledby="home-tab">                 
												<div id="customer-loaded-content"></div>
											</div>    
										</div>
									</div>	main gridview -->
									<!-- search gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="customer-search-gridview" style="display:none;">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#customersearch" id="order-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="customer-results-count"><b>0</b></span>) </a>
                                            </li>                                                              
                                        </ul>
                         				<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="customersearch" aria-labelledby="home-tab">                 
												<div id="customer-loaded-content-search"></div>
											</div>																																									
										</div>
									</div>									
								</div>
							</div>
						</div>						
					</div>
		</div>
    </div>
</div>
<div class="modal fade" id='assignUser'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Assign User'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/assignuser" method="post" id="assignUserRegister" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="assignUserForm" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>


<div class="modal fade" id='projectAdd'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Add Project'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/addproject" method="post" id="projectAddRegister" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="projectAddForm" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>




<div class="modal fade" id='showAllProjects'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'All Projects'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfshowAllProjects">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>



<div class="modal fade" id='customerDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Customer Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfCustomer">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='customerCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Customer'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/create" method="post" id="customerRegisterForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="customerCreationForm" >

            </div>

        </div>
        <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit" value="<?php echo Yii::t('app', 'Save'); ?>" class="btn btn-primary">
        </div>
        
    </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='customerUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Update Customer'); ?><span id="req_id"></span></h4>
    </div>
     <form action="<?php echo Yii::$app->request->baseUrl;?>/customers/default/update" method="post" id="customerUpdateRegisterForm" novalidate="novalidate" enctype="multipart/form-data">
        <div class="modal-body">

            <div id="customerUpdateForm">

            </div>

        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit" style="margin-bottom: 5px;" value="<?php echo Yii::t('app', 'Update'); ?>" class="btn btn-primary">
        </div>
     </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>