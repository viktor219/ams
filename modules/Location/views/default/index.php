<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
	
	use app\models\LocationParent;
	
	$this->title = 'Locations';
	
	$this->params['breadcrumbs'][] = $this->title;
?>

<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet">

<?= $this->render("_modals/_reallocate");?>
<?= $this->render("_modals/_deleteconfirm");?>

<div class="location-index">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align">
				<div class="col-md-7 vcenter">
					<h4>
						<span class="glyphicon glyphicon-globe"></span>
						<?= Html::encode($this->title) ?> 
					</h4>
				</div>
				<div class="col-md-5 vcenter text-right">
					    <?php $form = ActiveForm::begin([
						        'action' => ['index'],
						        'method' => 'get',
					    		'options' => ['onkeypress'=>"return event.keyCode != 13;"]
						    ]); ?>
						<div id="searchorder-group" class="pull-right top_search">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-success" id="searchLocationBtn" type="button"><b style="color:#FFF;">?</b></button> 
								</span>
								<input type="search" placeholder="Search" id="searchLocation" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
								<span class="input-group-btn navigate-buttons">
									<?= Html::a('<span class="glyphicon glyphicon-plus"></span>New Location', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
								</span>
							</div>
						</div>
					<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
		<div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12">  
                 <div class="x_panel">
                       <div class="x_content">
                                <!-- main gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="location-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
                                            <li role="presentation" class="active"><a href="#locationhome" onClick="loadLocations('');" id="inventory-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class="dropdown">
                                            	<a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Divisions <span class="caret"></span></a>
												  <ul class="dropdown-menu" role="menu" style="min-width:80px;">
												  	<?php foreach ($_divisions as $_division) :?>
												  		<?php $_division_name = (!empty($_division)) ? LocationParent::findOne($_division->parent_id)->parent_name : 'Uncategorized';?>
												  		<li style="	float: none;text-align: center;"><a href="javascript:;" onClick="loadLocations('<?php echo \yii\helpers\Url::toRoute(['/location/dload', 'id'=>$_division->parent_id])?>');" divisionid = "<?php echo $_division_name;?>" class="loadDivionLocations"><?php echo $_division_name;?></a></li>
												  	<?php endforeach;?>
												  </ul>                                            
                                            </li>                                                
                                            <li role="presentation" class="left"><a href="#locationdelete" onClick="deleteLocations('');" id="order-tab-6" role="tab" data-toggle="tab" aria-expanded="true">Deleted (<span class="total_delete_count"><?=$_count_deleted;?></span>)</a>
	                                            </li>												
                                        </ul>
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="locationhome" aria-labelledby="home-tab">   
                        						<div class="x_panel">
							                          <div class="x_title">
                                                        <h2><i class="fa fa-bars"></i> <span style="color: #73879C">All</span></h2>
				                                    <ul class="nav navbar-right panel_toolbox">
				                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
				                                        </li>
				                                        <li class="dropdown">
				                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
				                                            <ul class="dropdown-menu" role="menu">
				                                                <li><a href="#">Settings 1</a>
				                                                </li>
				                                                <li><a href="#">Settings 2</a>
				                                                </li>
				                                            </ul>
				                                        </li>
				                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
				                                        </li>
				                                    </ul>
				                                    <div class="clearfix"></div>
				                                </div>
				                                <div class="x_content">  
				                                	<div id="location-loaded-content"></div>
				                                </div>
				                             </div>							                                                                  	              
										</div>    	
                                       <div role="tabpanel" class="tab-pane fade in" id="locationdelete" aria-labelledby="location-delete-tab">                 
											<div id="location-deleted-content"></div>
										</div>												
									</div>
								</div>	
								<!-- search gridview -->
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="location-search-gridview" style="display:none;">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#usersearch" id="order-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="location-results-count"><b>0</b></span>) </a>
                                            </li>                                                                                                        
                                        </ul>
                         				<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="usersearch" aria-labelledby="home-tab">                 
												<div id="location-loaded-content-search"></div>
											</div>																																									
										</div>
									</div>	                       
                       </div>
                  </div>
            </div>
         </div>
      </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/admin_location.js"></script>