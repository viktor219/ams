<?php 
	use yii\helpers\Html;
	use yii\grid\GridView;
	use app\modules\Orders\models\Order;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Medias;
	use app\models\Item;
	use app\models\Location;
	use yii\widgets\ActiveForm;
	
	$this->title = 'Details';
	$this->params['breadcrumbs'][] = ['label' => 'In Progress', 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	
	$showcleaningbutton = true;
	$showtestingbutton = true;
	
	$_cleaninghasoption = (bool) Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')
										->where(['ordernumber'=>$model->id])
										->andWhere(['orderid'=>$model->id])
										->andWhere(['status'=>array_search('In Progress', Item::$status)])
										->andWhere('optionid IN (2,3)')
										->count();
	
	$count_preowneditems = (bool) Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')->where(['ordernumber'=>$model->id, 'status'=>array_search('In Progress', Item::$status), 'preowneditems'=>1])->count();
	
	//$count_cleaningitems = (bool) Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('Cleaned', Item::$status)])->count();
		
	if($_cleaninghasoption==false && $count_preowneditems==false)
		$showcleaningbutton = false;
	// 
/*	$_testinghasoption = (bool) Item::find()->innerJoin('lv_item_has_option', '`lv_item_has_option`.`itemid` = `lv_items`.`model`')								
										->andWhere(['orderid'=>$model->id])
										->andWhere('optionid IN (47, 48)')
										->count();
	
	$count_requiretestingreferb = (bool) Item::find()->innerJoin('lv_models', '`lv_models`.`id` = `lv_items`.`model`')->where(['ordernumber'=>$model->id, 'status'=>array_search('In Progress', Item::$status), 'requiretestingreferb'=>1])->count();
	
	$count_testingitems = (bool) Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('Requested for Service', Item::$status)])
											->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('Used for Service', Item::$status)])
											->orWhere(['ordernumber'=>$model->id, 'status'=>array_search('Cleaned', Item::$status)])
											->count();
	
	var_dump($_testinghasoption, $count_requiretestingreferb, $count_testingitems);
	
	if($_testinghasoption==false && $count_requiretestingreferb==false && $count_testingitems==false)
		$showtestingbutton = false;
*/	
	$_hasready = (bool) Item::find()->where(['ordernumber'=>$model->id])
									->andWhere(['status'=>[array_search('Used for Service', Item::$status), array_search('Serviced', Item::$status)]])
									->count();
	//var_dump($showcleaningbutton, $showtestingbutton);
	
	$location = Location::findOne($model->location_id);
	
	if(empty($model->number_generated))
	{
		if(!empty($location->storenum))
			$name = "Store#" . $location->storenum;
		else if(!empty($location->storename))
			$name = $location->storename;
		else
			$name = $location->address . " " . $location->address2 . " " . $location->city . " " . $location->state . " " . $location->zipcode;
	}else
		$name = $model->number_generated;
?>
<?= $this->render("_modals/_itemhistory");?>
<?= $this->render("_modals/_cleaningconfirm");?>
<?= $this->render("_modals/_cleaningallconfirm");?>
<?= $this->render("_modals/_readytoshipallconfirm");?>
<?= $this->render("_modals/_requestreplaceconfirm");?>
<?= $this->render("_modals/_newtestingreview");?>
<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/stack_index.css" rel="stylesheet" />
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/stacktable.js"></script>
<div class="inprogres-index">
<!-- Sales Order Dashboard -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="row vertical-align"> 
				<div class="col-md-8 vcenter">
					<h4>
						<span class="glyphicon glyphicon-equalizer"></span>
						<?= Html::encode($this->title) ?> <b>SO# : <?php echo $name;?></b>
					</h4>
				</div>
				<div class="col-md-4 vcenter text-right">
					<?php //echo ($showcleaningbutton) ? Html::a('<span class="glyphicon glyphicon-store"></span> Cleaning Completed', ['/inprogress/endcleaning', 'id'=>$model->id], ['class' => 'btn btn-success']) : '';?>				
					<?php echo ($_hasready) ? Html::a('<span class="glyphicon glyphicon-ok-sign"></span> Ready To Ship All', 'javascript:;'/*['/inprogress/turnonship', 'id'=>$model->id]*/, ['class' => 'btn btn-success', 'id'=>'ready-to-ship-all']) : '';?>										
				</div>
			</div>
		</div>
		<div class="panel-body">
                                        	<div role="tabpanel" class="tab-pane fade active in" id="inventoryhome" aria-labelledby="home-tab">
                                                    <div class="x_panel" style="padding: 10px 10px;" id="inventory-panel"> 
                                                        <div class="x_title">
                                                        	<div class="col-md-3">
                                                            	<h2><i class="fa fa-bars"></i><span style="color: #73879C"> Items</span> (<span><?php echo $query_count;?></span>)</h2>
                                                            </div>
                                				<div class="col-md-4"></div>
                                				<div class="col-md-1" style="padding:0;">
                                					<?= ($showcleaningbutton) ? Html::a('<span class="glyphicon glyphicon-store"></span> All Cleaned', /*['/inprogress/markallcleaned', 'id'=>$model->id]*/ 'javascript:;', ['class' => 'btn btn-success', 'id'=>'all-cleaned-button']) : "";?>	
                                				</div>
												<div class="col-md-3">
													<?php $form = ActiveForm::begin([
														        'action' => ['index'],
														        'method' => 'get',
													    		//'options' => ['onkeypress'=>"return event.keyCode != 13;"]
														    ]); ?>
														<div id="searchorder-group" class="pull-right top_search">
															<div class="input-group">
																<span class="input-group-btn">
																	<button class="btn btn-success" id="searchSerialBtn" type="button"><b style="color:#FFF;">?</b></button> 
																</span>
																<input type="search" placeholder="Search serial number..." id="searchSerial" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
															</div>
														</div>
													<?php ActiveForm::end(); ?>				
												</div>                            
                                                                <ul class="nav navbar-right panel_toolbox">
                                                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                                                        <li class="dropdown">
                                                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                                <li><a href="#">Settings 1</a>
                                                                                </li>
                                                                                <li><a href="#">Settings 2</a>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                                                                </ul>
                                                                <div class="clearfix"></div>
                                                        </div>
                                                        <div class="x_content" style="padding:0;margin-top:0;">		
															<div class="" role="tabpanel" data-example-id="togglable-tabs" id="rma-main-gridview">
																<ul id="myTab" class="nav nav-tabs bar_tabs right hide-mobile" role="tablist">
																	<li role="presentation" class="active" id="inprogress-details-all"><a href="#inprogressdetailsall" id="inprogress-details-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
																	</li>														
																	<li role="presentation" class="" style="display:none;" id="search-serial-overview-title"><a href="#serialitemssearch" id="inprogress-details-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Search(<span id="serial-search-count"></span>)</a>
																	</li>                                                                                                                                                                                                                                                                  
																</ul>
																<div id="myTabContent" class="tab-content">   
																	<div role="tabpanel" class="tab-pane fade active in" id="inprogressdetailsall" aria-labelledby="rmacustomerinventoryhome-tab">
																		<?= $this->render('_detailsview', ['dataProvider'=>$dataProvider]) ?>
																	</div>
																	<div role="tabpanel" class="tab-pane fade in" id="serialitemssearch" aria-labelledby="rmacustomerinventorysearch-tab">
																		<div id="loaded-serial-search-content"></div>
																	</div>
																</div>
															</div>                                                        
		    </div>
		 </div>
		 </div>
		</div>
    </div>
</div>
<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/inprogress.js"></script>