<?php 
	use yii\helpers\Url;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\grid\GridView;
	use app\models\Itemspurchased;
	use app\models\Users;
	use app\models\Item;
	use app\models\Models;
	use app\models\Manufacturer;	
	use app\modules\Orders\models\Order;
	use app\models\Customer;
	use app\models\Vendor;
	
	$this->title = 'Purchase Orders';
	
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
} 
</style>
<?php /*<?= $this->render("_modals/_purchasingdetails");?>*/?>
<?= $this->render("_modals/_receiveqtymodal");?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_sendmail");?>
<?= \Yii::$app->view->render("@app/views/layouts/_modals/_deleteconfirm", ['page' => 'Item']); ?>
                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-5 vcenter">
                                    <h4>
                                        <span class="glyphicon glyphicon-equalizer"></span>
                                        Purchase Orders
                                    </h4>
                                </div>
                                <div class="col-md-7 vcenter text-right">
								    <?php $form = ActiveForm::begin([
									        'action' => ['index'],
									        'method' => 'get',
									    ]); ?>
										<div id="searchpurchasing-group" class="pull-right top_search">
											<div class="input-group">
												<span class="input-group-btn">
													<button class="btn btn-success" id="searchPurchasingBtn" type="button"><b style="color:#FFF;">?</b></button> 
												</span>
												<input type="text" placeholder="Search" id="searchPurchasing" class="form-control" style="font-weight:bold;border: 1px solid #ddd;"/>
												<span class="input-group-btn">
													<?= Html::a('<span class="glyphicon glyphicon-user"></span> Vendors', ['/vendor/index'], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px;border-radius:4px;']) ?>
													<?= Html::a('<span class="glyphicon glyphicon-plus"></span> Create A Purchase Order', ['create'], ['class' => 'btn btn-success', 'style' => 'margin-left: 1px;border-radius:4px;']) ?>								
												</span>
											</div>						
										</div>
									<?php ActiveForm::end(); ?>                                
                                </div>
                            </div>
                        </div>
                    <div class="panel-body">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="purchasing-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="" style="display:none;" id="purchasing-search-tab"><a href="#purchasingsearch" id="purchasing-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Search Results (<span id="order-results-count"><b>0</b></span>) </a>
                                            </li> 
                                            <li role="presentation" class="active"><a href="#purchasinghome" onclick="loadPurchasing('')" id="purchasing-tab-1" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class=""><a href="#purchasingservice" id="purchasing-tab-2" role="tab" data-toggle="tab" aria-expanded="true">Service</a>
                                            </li>                                               
                                            <li role="presentation" class=""><a href="#purchasingintegration" id="purchasing-tab-3" role="tab" data-toggle="tab" aria-expanded="true">Integration</a>
                                            </li>                                            
                                            <li role="presentation" class=""><a href="#purchasingwarehouse" id="purchasing-tab-4" role="tab" data-toggle="tab" aria-expanded="true">Warehouse</a>
                                            </li> 
                                            <li role="presentation" class=""><a href="#purchasingdeleted" onclick="deletePurchasing('')" id="purchasing-tab-5" role="tab" data-toggle="tab" aria-expanded="true">Deleted(<span class="total_delete_count"><?= ($deletedIncomingPurchases + $deletedItemsRequested); ?></span>)</a>
                                            </li>                                              
                                        </ul>         
                         				<div id="loading-search" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>                                                       
                                        <div id="myTabContent" class="tab-content">     
                                        	<div role="tabpanel" class="tab-pane fade in" id="purchasingsearch" aria-labelledby="home-tab">                 
												<div id="purchasing-loaded-content-search"></div>
											</div>	                                        
                                        	<div role="tabpanel" class="tab-pane fade active in" id="purchasinghome" aria-labelledby="purchasinghome-tab">                                            
                        <div class="row row-margin">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="items-load-content">
                                        <?= $this->render('_items_requested', ['dataProvider' => $__requesteddataProvider]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
							                        <div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Incoming Purchases</h2>
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
																<div class="" role="tabpanel" data-example-id="togglable-tabs" id="incoming-purchase-gridview">
																	<ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
																		<li role="presentation" class="active"><a href="#incomingpurchasingall" id="incoming-purchasing-tab-0" role="tab" data-toggle="tab" aria-expanded="true">Actives</a>
																		</li> 
																		<li role="presentation" class=""><a href="#incomingpurchasingexhausted" id="purchasing-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Inactives</a>
																		</li>                                                                                                                                                                                                                         
																	</ul>         
																	<div id="myTabContent" class="tab-content">     
																		<div role="tabpanel" class="tab-pane fade active in" id="incomingpurchasingall" aria-labelledby="home-tab">                 
																			<?= $this->render('_active_purchase', ['dataProvider' => $__purchasedataProvider]); ?>
																		</div>
																		<div role="tabpanel" class="tab-pane fade in" id="incomingpurchasingexhausted" aria-labelledby="purchasinghome-tab">
                                                                            <?= $this->render('_inactive_purchase', ['dataProvider' => $__ipurchasedataProvider]); ?>
																		</div>
																	</div>
																</div>
							                                </div>
							                            </div>
							                        </div>    
							                     </div>
							                     <div role="tabpanel" class="tab-pane fade in" id="purchasingintegration" aria-labelledby="purchasingintegration-tab">    
                        <div class="row row-margin">
                            <div class="x_panel">
                                <div class="x_title">
                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                        <?= $this->render('_items_requested', ['dataProvider' => $__integrationdataProvider]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>							                     
							                     </div>
							                     <div role="tabpanel" class="tab-pane fade in" id="purchasingwarehouse" aria-labelledby="purchasingwarehouse-tab">    
							                        <div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
							                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
																    <?= GridView::widget([
																        'dataProvider' => $__warehousedataProvider,
																    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																    	'summary'=>'',
																        'columns' => [
																            /*[
																				'attribute'=>'owner_id',
																				'label'=>'Request By',
																				'value'=> function($model) {
																					return Users::findOne($model->owner_id)->firstname . ' ' . Users::findOne($model->owner_id)->lastname;
																				}													
																			],*/
																			[
																				'attribute'=>'qty',
																    			'format'=>'raw',
																				'label'=>'Quantity',
																				'value'=> function($model) {
																					$number_items = Item::find()->where(['ordernumber'=>$model->ordernumber, 'status'=>1, 'model'=>$model->model])->count();
																					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getitemsrequestedindexdetails?&idorder='.$model->ordernumber.'&itemid='.$model->id.'" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>';
																				}
																			], 
																			[
																				'attribute'=>'model',
																				'label'=>'Description',
																				'value'=> function($model) {
																					//$item = Item::findOne($model->item);
																					$_model = Models::findOne($model->model);
																					$_man = Manufacturer::findOne($_model->manufacturer);
																					return $_man->name . ' ' . $_model->descrip;
																				}
																			],
																			[//sum of all of that item on all sales orders for the past 90 days
																				'label'=>'Total Recently Sold',
																				'value'=> function($model) {
																					$now = date('Y-m-d');
																					$MonthsAgo = date("Y-m-d", strtotime("-3 month"));
																					return Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])
																								->andWhere("created_at between '$MonthsAgo' and '$now'")
																								->count();
																				}
																			],
																			[//sum of price from all purchase orders with that item in the last 90 days divided by the quantity of purchase orders with that item in the last 90 days
																				'label'=>'Average Cost',
																				'value'=> function($model) {
																					$now = date('Y-m-d');
																					$MonthsAgo = date("Y-m-d", strtotime("-3 month"));													
																					$sumprice = (new \yii\db\Query())->from('{{%itemspurchased}}')
																									->where(['model'=>$model->model])
																									->andWhere("created_at between '$MonthsAgo' and '$now'")
																									->sum('price');
																					$sumqty = (new \yii\db\Query())->from('{{%itemspurchased}}')
																					->where(['model'=>$model->model])
																					->andWhere("created_at between '$MonthsAgo' and '$now'")
																					->sum('qty');		
							
																					return ($sumqty != 0) ? number_format(($sumprice / $sumqty), 2) : '-';
																				}
																			],																																									
																			[
																				'attribute'=>'created_at',
																				'label'=>'Requested Date',
																				'value'=> function($model) {
																					return date('m/d/Y', strtotime($model->created_at));
																				}
																			],												
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{create} {update} {delete}',
																				'contentOptions' => ['style' => 'width:180px;'],
																				'controller' => 'orders',
																				'buttons' => [
																					'create' => function ($url, $model, $key) {
																							$requestid = Item::find()->where(['model'=>$model->model, 'ordernumber'=>$model->ordernumber, 'customer'=>$model->customer, 'created_at'=>$model->created_at])->one()->id;
																						$options = [
																							'title' => 'Create PO',
																							'class' => 'btn btn-info',
																							'type'=>'button',
																						];
																						$url = Url::toRoute(['/purchasing/create', 'request'=>base64_encode($requestid)]);
																					
																						return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', $url, $options);
																					},
																					'update' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Edit',
																							'class' => 'btn btn-warning',
																							'type'=>'button',
																							'onClick'=>'EditItemPurchased("'.$model->id.'");'
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
																					},
																					'delete' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Delete',
																							'class' => 'btn btn-danger',
																							'data-content'=>'Delete Order',
																							'type'=>'button',
																							//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
																					}
																				],
																			]
																        ],
																    ]); ?>                                  
							                                    </div>
							                                </div>
							                            </div>
							                        </div>							                     
							                     </div>	
							                     <div role="tabpanel" class="tab-pane fade in" id="purchasingservice" aria-labelledby="purchasingservice-tab">  
							                        <div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Items Requested</h2>
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
							                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
																    <?= GridView::widget([
																        'dataProvider' => $__servicedataProvider,
																    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																    	'summary'=>'',
																        'columns' => [
																            /*[
																				'attribute'=>'owner_id',
																				'label'=>'Request By',
																				'value'=> function($model) {
																					return Users::findOne($model->owner_id)->firstname . ' ' . Users::findOne($model->owner_id)->lastname;
																				}													
																			],*/
																			[
																				'attribute'=>'qty',
																    			'format'=>'raw',
																				'label'=>'Quantity',
																				'value'=> function($model) {
																					$number_items = Item::find()->where(['ordernumber'=>$model->ordernumber, 'status'=>1, 'model'=>$model->model])->count();
																					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $model->id . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getitemsrequestedindexdetails?&idorder='.$model->ordernumber.'&itemid='.$model->id.'" role="button" data-toggle="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="" rel="popover" style="color:#08c;">' . $number_items . '</a>';
																				}
																			], 
																			[
																				'attribute'=>'model',
																				'label'=>'Description',
																				'value'=> function($model) {
																					//$item = Item::findOne($model->item);
																					$_model = Models::findOne($model->model);
																					$_man = Manufacturer::findOne($_model->manufacturer);
																					return $_man->name . ' ' . $_model->descrip;
																				}
																			],
																			[//sum of all of that item on all sales orders for the past 90 days
																				'label'=>'Total Recently Sold',
																				'value'=> function($model) {
																					$now = date('Y-m-d');
																					$MonthsAgo = date("Y-m-d", strtotime("-3 month"));
																					return Item::find()->where(['ordernumber'=>$model->ordernumber, 'model'=>$model->model])
																								->andWhere("created_at between '$MonthsAgo' and '$now'")
																								->count();
																				}
																			],
																			[//sum of price from all purchase orders with that item in the last 90 days divided by the quantity of purchase orders with that item in the last 90 days
																				'label'=>'Average Cost',
																				'value'=> function($model) {
																					$now = date('Y-m-d');
																					$MonthsAgo = date("Y-m-d", strtotime("-3 month"));													
																					$sumprice = (new \yii\db\Query())->from('{{%itemspurchased}}')
																									->where(['model'=>$model->model])
																									->andWhere("created_at between '$MonthsAgo' and '$now'")
																									->sum('price');
																					$sumqty = (new \yii\db\Query())->from('{{%itemspurchased}}')
																					->where(['model'=>$model->model])
																					->andWhere("created_at between '$MonthsAgo' and '$now'")
																					->sum('qty');		
							
																					return ($sumqty != 0) ? number_format(($sumprice / $sumqty), 2) : '-';
																				}
																			],																																									
																			[
																				'attribute'=>'created_at',
																				'label'=>'Requested Date',
																				'value'=> function($model) {
																					return date('m/d/Y', strtotime($model->created_at));
																				}
																			],												
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{create} {update} {delete}',
																				'contentOptions' => ['style' => 'width:180px;'],
																				'controller' => 'orders',
																				'buttons' => [
																					'create' => function ($url, $model, $key) {
																							$requestid = Item::find()->where(['model'=>$model->model, 'ordernumber'=>$model->ordernumber, 'customer'=>$model->customer, 'created_at'=>$model->created_at])->one()->id;
																						$options = [
																							'title' => 'Create PO',
																							'class' => 'btn btn-info',
																							'type'=>'button',
																						];
																						$url = Url::toRoute(['/purchasing/create', 'request'=>base64_encode($requestid)]);
																					
																						return Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>', $url, $options);
																					},
																					'update' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Edit',
																							'class' => 'btn btn-warning',
																							'type'=>'button',
																							'onClick'=>'EditItemPurchased("'.$model->id.'");'
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>', $url, $options);
																					},
																					'delete' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Delete',
																							'class' => 'btn btn-danger',
																							'data-content'=>'Delete Order',
																							'type'=>'button',
																							//'onClick'=> 'return confirm(\'are you sure to delete this order ?\');',
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, $options);
																					}
																				],
																			]
																        ],
																    ]); ?>                                  
							                                    </div>
							                                </div>
							                            </div>
							                        </div>							                     
							                     </div>						                     
                                            <div role="tabpanel" class="tab-pane fade in" id="purchasingdeleted" aria-labelledby="deleting-tab">                 
												<div role="tabpanel" class="tab-pane fade in" id="orderdelete" aria-labelledby="order-backet-tab">        
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
						                                    <h2><i class="fa fa-bars"></i> Items Requested Deleted (<span class="items_delete_count"></span>)</h2>
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
						                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">                                        	         
																<div id="items-deleted-content"></div>
															</div>
														</div>
													</div>
												</div>
						                        <div class="row row-margin">
						                            <div class="x_panel">
						                                <div class="x_title">
                                                                                    <h2><i class="fa fa-bars"></i> Incoming Purchases Deleted (<span class="purchase_delete_count"></span>)</h2>
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
						                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">                                        	         
																<div id="purchasing-deleted-content"></div>
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
            <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing.js"></script>