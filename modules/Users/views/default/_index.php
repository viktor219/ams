<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Department;
use app\models\User;
use app\models\Users;

$this->title = Yii::t('app', 'User Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/tabs.css" rel="stylesheet">
<div class="user-index">   
     <?= Yii::$app->session->getFlash('successDepartment'); ?>
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-4 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?>
					</h4>
				</div>
				<div class="col-md-8 vcenter text-right">
						 <?php $form = ActiveForm::begin([
							        'action' => ['index'],
							        'method' => 'get',
							    ]); ?>
							<div id="searchuser-group" class="pull-right top_search">
								<div class="input-group">
									<span class="input-group-btn">
										<button class="btn btn-success" id="searchUserBtn" type="button"><b style="color:#FFF;">?</b></button> 
									</span>
									<input type="text" placeholder="Search" id="searchUser" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
									<span class="input-group-btn">
										<?php if(Yii::$app->user->identity->usertype===User::TYPE_ADMIN) :?>
											<a href="<?= Url::to(['/userlog/index']) ?>" class="btn btn-success" style="margin-left: 5px;border-radius:4px;"><span class="glyphicon glyphicon-user"></span> Users Log</a>
										<?php endif;?>
										<?= Html::a('Departments', 'javascript://', ['class' => 'btn btn-success showDepartments', 'style' => 'margin-left: 1px;border-radius:4px;']) ?>
										<?= Html::a('<span class="glyphicon glyphicon-plus"></span> Add New User', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 1px;border-radius:4px;']) ?>		
									</span>						
								</div>
							</div>
						<?php ActiveForm::end(); ?>
					</div>
				</div>
			</div>
		<div class="panel-body" style="">
		<div class="row row-margin">
            <div class="col-md-12 col-sm-6 col-xs-12">
                 <div class="x_panel" style="padding-left: 0; padding-right:0;">
                                <div class="x_content">
                                <!-- main gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="user-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#userhome" onClick="loadUser('');" id="inventory-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Departments <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu">
												  	<?php foreach ($departments as $department) :?>
														<li><a href="javascript:;" class="loadUsersDepartment" uid="<?php echo $department->id;?>"><?php echo $department->name;?></a></li>										  	
												  	<?php endforeach;?>
												  </ul>                                            
                                            </li>                                              
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">User Type <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu">
												  	<?php foreach ($usertypes as $usertype) :?>
														<li><a href="javascript:;" class="loadUsersType" uid="<?php echo $usertype->id;?>"><?php echo $usertype->name;?></a></li>										  	
												  	<?php endforeach;?>
												  </ul>                                           
                                            </li>  
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Projects <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu">
												  	<?php foreach ($_find_projects->all() as $projectowner) :?>
														<li><a href="javascript:;" class="loadUsersProjects" project_id = "<?php echo $projectowner->id;?>"><?php echo $projectowner->firstname;?> <?php echo $projectowner->lastname;?></a></li>										  	
												  	<?php endforeach;?>
												  	<?php if($_find_projects->count()==0) :?>
												  		<li><a href="javascript:;">No Project Found.</a></li>
												  	<?php endif;?>
												  </ul>                                           
                                            </li>                                                                                                                              
                                        </ul>
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="userhome" aria-labelledby="home-tab">                 
												<div id="user-loaded-content"></div>
											</div>    																																
										</div>
									</div>	
									<!-- search gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="user-search-gridview" style="display:none;">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#usersearch" id="order-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="user-results-count"><b>0</b></span>) </a>
                                            </li>                                                                                                        
                                        </ul>
                         				<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="usersearch" aria-labelledby="home-tab">                 
												<div id="user-loaded-content-search"></div>
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
<div class="modal fade" id='depatmentCreationsPopsUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'update Department'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/editdepartment" method="post" id="departmentCreationFormUpdate" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="departmentCreationFormViewUpdate" >

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


<div class="modal fade" id='depatmentCreationsPops'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create Department'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/createdepartment" method="post" id="departmentCreationForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="departmentCreationFormView" >

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

<div class="modal fade" id='departmentsPops'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'All Departments'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="departmentsPopsView">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>



<div class="modal fade" id='userDetails'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'User Details'); ?><span id="req_id"></span></h4>
    </div>
    <div class="modal-body">
        <div id="detaisOfUser">
            
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>
        
    </div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>






<div class="modal fade" id='userCreation'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Create User'); ?><span id="req_id"></span></h4>
    </div>
    <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/create" method="post" id="userRegisterForm" novalidate="novalidate" enctype="multipart/form-data">

        <div class="modal-body">

            <div id="userCreationForm" >

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






<div class="modal fade" id='userUpdate'>
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo Yii::t('app', 'Update User'); ?><span id="req_id"></span></h4>
    </div>
     <form action="<?php echo Yii::$app->request->baseUrl;?>/users/default/update" method="post" id="userUpdateRegisterForm" novalidate="novalidate" enctype="multipart/form-data">
        <div class="modal-body">

            <div id="userUpdateForm">

            </div>

        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo Yii::t('app', 'Close'); ?></button>
        <input type="submit"  value="<?php echo Yii::t('app', 'Update'); ?>" class="btn btn-primary">
        </div>
     </form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/user.js"></script>
<?php if (Yii::$app->mobileDetect->isMobile()) :?>
	<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">
<?php endif;?>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<script>
    var popupHit = 0;
    popupHit = '<?php if(Yii::$app->session->getFlash('successDepartment')){ echo "1";}else echo "0";?>';

    
 
    $(document).ready(function(){
        
        if(popupHit==1)
        {
            
            $('.showDepartments').trigger('click');
        }
    });
</script>