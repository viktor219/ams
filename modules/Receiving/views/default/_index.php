<?php 
	use yii\helpers\Html;
	use yii\helpers\Url;
	use app\modules\Orders\models\Order;
	use app\models\Purchase;
	use app\models\Vendor;
	use app\models\Customer;
	use app\models\Medias;
	use app\models\Item;
	use app\models\Itemsordered;
	use app\models\Models;
	use app\models\Manufacturer;
	use app\models\Itemspurchased;
	use yii\grid\GridView;
	
	$this->title = 'Receiving';
	
	$this->params['breadcrumbs'][] = $this->title;
	
	$_received_so_items = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
	$_received_po_items = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status)])->count();
?>
<?= $this->render("_modals/_receiveqtymodal");?>
<?= $this->render("_modals/_receivingcreatedetails");?>
<?= $this->render("@app/modules/Purchasing/views/default/_modals/_receiveqtymodal");?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_serials", ["order"=>array()]);?>
<?= $this->render("@app/modules/Orders/views/default/_modals/_customerdetails");?>
                <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row vertical-align">
                                <div class="col-md-6 vcenter">
									<h4><span class="glyphicon glyphicon-tags"></span> Receiving Overview</h4>
                                </div>
                                <div class="col-md-6 vcenter text-right">                             
                                        <a href="<?= Url::to(['/receiving/create']) ?>" class="btn btn-success"> 
                                            <span class="glyphicon glyphicon-plus"></span> Receive Inventory
                                        </a>
                                </div>
                            </div>
                        </div>
                    <div class="panel-body" style="padding: 15px 0 0 0"> 
        <div class="panel-body">
		<div class="row row-margin">
            <div class="col-md-12 col-sm-6 col-xs-12">
                 <div class="x_panel">
                                <div class="x_content">
                                    <div class="" role="tabpanel" data-example-id="togglable-tabs" id="receiving-main-gridview">
                                        <ul id="myTab" class="nav nav-tabs bar_tabs right" role="tablist">
                                            <li role="presentation" class="active"><a href="#receivinghome" id="receiving-tab-0" role="tab" data-toggle="tab" aria-expanded="true">All</a>
                                            </li>
                                            <li role="presentation" class=""><a href="#receivingrir" id="receiving-tab-2" role="tab" data-toggle="tab" aria-expanded="true">Received Items(<span id="received-items-count"><?php echo $_received_so_items + $_received_po_items;?></span>)</a>
                                            </li>                                            
                                            <li role="presentation" class=""><a href="#receivingrip" id="receiving-tab-1" role="tab" data-toggle="tab" aria-expanded="true">Incoming Purchases</a>
                                            </li>  
                                            <li role="presentation" class=""><a href="#receivingricr" id="receiving-tab-3" role="tab" data-toggle="tab" aria-expanded="true">Incoming Customer Inventory</a>
                                            </li>                                                                                                                                                                                                                        
                                        </ul>
                         				<div id="loading" style="display:none;"><p><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/FhHRx.gif" /> Please Wait</p></div>               
                                        <div id="myTabContent" class="tab-content">    
                                        	<div role="tabpanel" class="tab-pane fade active in" id="receivinghome" aria-labelledby="receivinghome-tab">                 
												<div id="receivinghome-loaded-content">
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
								                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
																	    <?= GridView::widget([
																	        'dataProvider' => $__purchasedataProvider,
																	    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																	    	'summary'=>'',
																	        'columns' => [
																	            'number_generated',
																				[
																	            	'attribute'=>'vendor_id',
																					'label'=>'Vendor',
																					'value' => function($model) {
																						return Vendor::findOne($model->vendor_id)->vendorname;
																					}
																				],
																				[
																	    			'label'=>'Qty',
																					'format' => 'raw',
																					'value' => function($model) {
																						return Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id])->count();
																					}
																				],
																				[
																					'attribute'=>'estimated_time',
																					'value' => function($model) {
																						if($model->estimated_time){
																							return date('m/d/Y', strtotime($model->estimated_time));
																						}
																					}
																				],
																				'trackingnumber',
																				[
																					'class' => 'yii\grid\ActionColumn',
																					'template'=>'{receive} {delete}',
																					'controller' => 'orders',
																					'buttons' => [
																						'receive' => function ($url, $model, $key) {
																							$options = [
																								'title' => 'Receive',
																								'class' => 'btn btn-info',
																								'type'=>'button',
																								'pid'=>$model->id,
																								'onClick'=>'ReceivePurchaseOrder("'.$model->id.'");'
																							];
																							$url = "javascript:;";
																						
																							return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																						},
																						'update' => function ($url, $model, $key) {
																							$options = [
																								'title' => 'Edit',
																								'class' => 'btn btn-warning',
																								'type'=>'button'
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
								                    <div class="panel-body" style="padding: 15px 0 0 0">
								                        <div class="row row-margin">
								                            <div class="x_panel">
								                                <div class="x_title">
								                                    <h2><i class="fa fa-bars"></i> Incoming Customer Inventory</h2>
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
																		'dataProvider' => $__incominginventoryProvider,
																		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																		'summary'=>'',
																		'columns' => [
																			[
																				'attribute' => 'customer',
																				'label' => Yii::t('app', 'Customer'),
																				'contentOptions' => ['style' => 'width:200px;'],
																				'format' => 'raw',
																				'value' => function($model) {
																					$customer = Customer::findOne($model->customer);
																					
																					$_my_media = Medias::findOne($customer->picture_id);
																					 
																					if(!empty($_my_media->filename)){
																						$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
																						if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) {
																							 
																							return  Html::img($target_file, ['alt'=>$customer->companyname, 'class'=>'viewCustomer', 'style'=>'cursor:pointer;max-width:90px;max-height:35px;', 'cid'=>$customer->id]);
																				
																						}else{
																							return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
																						}
																						 
																					}else {
																						 
																						return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
																					}
																					 
																				}
																			],
																			[
																				'attribute' => 'ordernumber',
																				'label' => 'SO#',
																				'value' => function($model) {
																					return Order::findOne($model->ordernumber)->number_generated;
																				}
																			],
																			[
																			'attribute' => 'ordernumber',
																			'label' => 'PO#',
																			'value' => function($model) {
																				return Order::findOne($model->ordernumber)->customer_po;
																			}
																			],																			
																			[
																				'attribute' => 'model',  
																				'label' => 'Description',
																				'value' => function($model) {
																						$_model = Models::findOne($model->model);
																						$_manufacturer = Manufacturer::findOne($_model->manufacturer);
																						//$qty = Item::find()->where(['ordernumber'=>null, 'status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
																						return $_manufacturer->name . ' ' . $_model->descrip;
																				}
																			],  
																			[
																				'header' => 'Qty',
																				'format' => 'raw',
																				'value' => function($model) {
																					$ordernumber = $model->ordernumber;
																					$items = Itemsordered::find()->where(['ordernumber'=>$ordernumber])->all();
																					$qty = 0;
																					foreach($items as $item)
																					{
																						$qty += $item->qty;
																					}
																					$number_items = $qty;
																					$items = Itemsordered::find()->where(['ordernumber'=>$ordernumber])->all();
																					$content = "";
																					$qty = 0;
																					foreach($items as $item)
																					{
																						$qty += $item->qty;
																						$_model = Models::findOne($item->model);
																						$manufacturer = Manufacturer::findOne($_model->manufacturer);
																						$count_model = Itemsordered::find()->where(['ordernumber'=>$ordernumber, 'model'=>$item->model])->one()->qty;
																						$name = $manufacturer->name . ' ' . $_model->descrip;
																						$findstatus = Item::find()->where(['ordernumber'=>$ordernumber, 'model'=>$item->model])->groupBy('status')->all();
																						$status = array();
																						foreach($findstatus as $stat)
																						{
																							$status[] = Item::$status[$stat->status];
																						}
																						$newline = "($count_model) $name " . "<span style=\"color:#08c;\">(<b>" . implode(', ', $status) . "</b>)</span>";
																						if($name!=="" && strpos($content, $newline) === false)
																							$content .= $newline . "<br/>";
																					}
																					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $ordernumber . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$ordernumber.'" role="button" data-toggle="popover" data-placement="left" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . Html::encode($content) . '" rel="popover" style="color:#08c;">' . $number_items . '</a>';
																				}
																			],          
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{receive} {update} {delete}',
																				'controller' => 'orders',
																				'buttons' => [
																				'receive' => function ($url, $model, $key) {
																					$options = [
																					'title' => 'Receive',
																					'class' => 'btn btn-info',
																					'type'=>'button',
																					'pid'=>$model->id,
																					'onClick'=>'ViewOrderDetails("'.$model->ordernumber.'");'
																							];
																					$url = "javascript:;";
																						
																					return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																				},
																				'update' => function ($url, $model, $key) {
																					$options = [
																					'title' => 'Edit',
																					'class' => 'btn btn-warning',
																					'type'=>'button'
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
												</div>
											</div>    
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingrip" aria-labelledby="receivingrip-tab">                
												<div id="receivingrip-loaded-content">
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
							                                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
																    <?= GridView::widget([
																        'dataProvider' => $__purchasedataProvider,
																    	'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																    	'summary'=>'',
																        'columns' => [
																            'number_generated',
																			[
																            	'attribute'=>'vendor_id',
																				'label'=>'Vendor',
																				'value' => function($model) {
																					return Vendor::findOne($model->vendor_id)->vendorname;
																				}
																			],
																			[
																    			'label'=>'Qty',
																				'format' => 'raw',
																				'value' => function($model) {
																					/*$items = Itemspurchased::find()->where(['ordernumber'=>$model->id])->all();
																					$qty = 0;
																					foreach($items as $item)
																					{
																						$qty += $item->qty;
																					}
																					$number_items = $qty;*/
																					return Item::find()->where(['status'=>array_search('In Transit', Item::$status), 'purchaseordernumber'=>$model->id])->count();
																				}
																			],
																			[
																				'attribute'=>'estimated_time',
																				'value' => function($model) {
																					if($model->estimated_time){
																						return date('m/d/Y', strtotime($model->estimated_time));
																					}
																				}
																			],
																			'trackingnumber',
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{receive} {delete}',
																				'controller' => 'orders',
																				'buttons' => [
																					'receive' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Receive',
																							'class' => 'btn btn-info',
																							'type'=>'button',
																							'pid'=>$model->id,
																							'onClick'=>'ReceivePurchaseOrder("'.$model->id.'");'
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																					},
																					'update' => function ($url, $model, $key) {
																						$options = [
																							'title' => 'Edit',
																							'class' => 'btn btn-warning',
																							'type'=>'button'
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
											</div>  
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingrir" aria-labelledby="receivingrir-tab">                
												<div id="receivingrir-loaded-content">
													<div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Received SO Items(<span id="so-received-items-count"><?php echo $_received_so_items;?></span>)</h2>
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
																		'dataProvider' => $dataProvider2,
																		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																		'summary'=>'',
																		'columns' => [
																			[
																				'attribute' => 'ordernumber',
																				'label' => 'SO#',
																				'value' => function($model) {
																					return Order::findOne($model->ordernumber)->number_generated;
																				}
																			],
																			[
																				'attribute' => 'item_id',  
																				'label' => 'Items',
																				'value' => function($model) {
																						$_model = Models::findOne($model->model);
																						$_manufacturer = Manufacturer::findOne($_model->manufacturer);
																						$qty = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->count();												
																						return $qty . '-' . $_manufacturer->name . ' ' . $_model->descrip;
																				}
																			],  
																			[
																				'attribute' => 'created_at',
																				'label' => 'Received Date',
																				'value' => function($model) {
																						return date('m/d/Y', strtotime($model->created_at));
																				}
																			],          
																			[
																				'label' => 'Receiving',
																				'value' => function($model) {
																					$customer = Order::findOne($model->ordernumber)->customer_id;
																					return Customer::findOne($customer)->companyname . ' Stock';
																				}
																			],
																			[
																				'attribute' => 'qty',
																				'label' => '',
																				'format' => 'raw',
																				'value' => function($model) {
																					$qty = Item::find()->where(['status'=>array_search('Received', Item::$status), 'ordernumber'=>$model->ordernumber, 'model'=>$model->model])->count();
																					return '<button class="btn btn-default" id="so-row-received-items-count-'. $model->model .'">' . $qty . ' </button>';
																				}
																			],								
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{receive}',
																				'controller' => 'receiving',
																				'buttons' => [
																					'receive' => function ($url, $model, $key) {
																						//$purchase = Purchase::findOne($model->purchaseordernumber);
																						$options = [
																							'title' => 'Receive',
																							'class' => 'btn btn-info',
																							'type'=>'button',
																							'onClick'=>'LoadreceiveQtyModal("' . $model->ordernumber . '", 1);'
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																					},
																				],
																			],
																		],
																	]); ?>   
																	</div>
																</div>
															</div>
														</div>													
													<div class="row row-margin">
							                            <div class="x_panel">
							                                <div class="x_title">
							                                    <h2><i class="fa fa-bars"></i> Received PO Items(<span id="po-received-items-count"><?php echo $_received_po_items;?></span>)</h2>
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
																		'dataProvider' => $dataProvider,
																		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																		'summary'=>'',
																		'columns' => [
																			[
																				'attribute' => 'ordernumber',
																				'label' => 'PO#',
																				'value' => function($model) {
																					return Purchase::findOne($model->purchaseordernumber)->number_generated;
																				}
																			],
																			[
																				'attribute' => 'item_id',  
																				'label' => 'Items',
																				'value' => function($model) {
																						$_model = Models::findOne($model->model);
																						$_manufacturer = Manufacturer::findOne($_model->manufacturer);
																						$qty = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
																						return $qty . '-' . $_manufacturer->name . ' ' . $_model->descrip;
																				}
																			],  
																			[
																				'attribute' => 'created_at',
																				'label' => 'Received Date',
																				'value' => function($model) {
																						return date('m/d/Y', strtotime($model->created_at));
																				}
																			],          
																			[
																				//'attribute' => 'salesordernumber',
																				'label' => 'Receiving',
																				'value' => function($model) {
																					$purchase = Purchase::findOne($model->purchaseordernumber);
																					return (empty($purchase->salesordernumber)) ? 'Asset Stock' : $purchase->salesordernumber;
																				}
																			],
																			[
																				'attribute' => 'qty',
																				'label' => '',
																				'format' => 'raw',
																				'value' => function($model) {
																					$qty = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['`lv_items`.`status`'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();
																					return '<button class="btn btn-default" id="po-row-received-items-count-' . $model->model . '">' . $qty . ' </button>';
																				}
																			],								
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{receive}',
																				'controller' => 'receiving',
																				'buttons' => [
																					'receive' => function ($url, $model, $key) {
																						//$purchase = Purchase::findOne($model->purchaseordernumber);
																						$options = [
																							'title' => 'Receive',
																							'class' => 'btn btn-info',
																							'type'=>'button',
																							'onClick'=>'LoadreceiveQtyModal("' . $model->purchaseordernumber . '", 2);'
																						];
																						$url = "javascript:;";
																					
																						return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																					},
																				],
																			],
																		],
																	]); ?>   
																	</div>
																</div>
															</div>
														</div>															
												</div>
											</div>  
                                        	<div role="tabpanel" class="tab-pane fade in" id="receivingricr" aria-labelledby="receivingricr-tab">                                    	                
												<div id="receivingricr-loaded-content">
								                    <div class="panel-body" style="padding: 15px 0 0 0">
								                        <div class="row row-margin">
								                            <div class="x_panel">
								                                <div class="x_title">
								                                    <h2><i class="fa fa-bars"></i> Incoming Customer Inventory</h2>
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
																		'dataProvider' => $__incominginventoryProvider,
																		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
																		'summary'=>'',
																		'columns' => [
																			[
																				'attribute' => 'customer',
																				'label' => Yii::t('app', 'Customer'),
																				'contentOptions' => ['style' => 'width:200px;'],
																				'format' => 'raw',
																				'value' => function($model) {
																					$customer = Customer::findOne($model->customer);
																					
																					$_my_media = Medias::findOne($customer->picture_id);
																					 
																					if(!empty($_my_media->filename)){
																						$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
																						if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) {
																							 
																							return  Html::img($target_file, ['alt'=>$customer->companyname, 'class'=>'viewCustomer', 'style'=>'cursor:pointer;max-width:90px;max-height:35px;', 'cid'=>$customer->id]);
																				
																						}else{
																							return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
																						}
																						 
																					}else {
																						 
																						return '<a href="javascript:;" class="viewCustomer" style="cursor:pointer;line-height: 40px;" cid="' . $customer->id . '">' . $customer->companyname . '</a>';
																					}
																					 
																				}
																			],
																			[
																				'attribute' => 'ordernumber',
																				'label' => 'SO#',
																				'value' => function($model) {
																					return Order::findOne($model->ordernumber)->number_generated;
																				}
																			],
																			[
																			'attribute' => 'ordernumber',
																			'label' => 'PO#',
																			'value' => function($model) {
																				return Order::findOne($model->ordernumber)->customer_po;
																			}
																			],																			
																			[
																				'attribute' => 'model',  
																				'label' => 'Description',
																				'value' => function($model) {
																						$_model = Models::findOne($model->model);
																						$_manufacturer = Manufacturer::findOne($_model->manufacturer);
																						//$qty = Item::find()->where(['ordernumber'=>null, 'status'=>array_search('Received', Item::$status), 'purchaseordernumber'=>$model->purchaseordernumber, 'model'=>$model->model])->count();												
																						return $_manufacturer->name . ' ' . $_model->descrip;
																				}
																			],  
																			[
																				'header' => 'Qty',
																				'format' => 'raw',
																				'value' => function($model) {
																					$ordernumber = $model->ordernumber;
																					$items = Itemsordered::find()->where(['ordernumber'=>$ordernumber])->all();
																					$qty = 0;
																					foreach($items as $item)
																					{
																						$qty += $item->qty;
																					}
																					$number_items = $qty;
																					$items = Itemsordered::find()->where(['ordernumber'=>$ordernumber])->all();
																					$content = "";
																					$qty = 0;
																					foreach($items as $item)
																					{
																						$qty += $item->qty;
																						$_model = Models::findOne($item->model);
																						$manufacturer = Manufacturer::findOne($_model->manufacturer);
																						$count_model = Itemsordered::find()->where(['ordernumber'=>$ordernumber, 'model'=>$item->model])->one()->qty;
																						$name = $manufacturer->name . ' ' . $_model->descrip;
																						$findstatus = Item::find()->where(['ordernumber'=>$ordernumber, 'model'=>$item->model])->groupBy('status')->all();
																						$status = array();
																						foreach($findstatus as $stat)
																						{
																							$status[] = Item::$status[$stat->status];
																						}
																						$newline = "($count_model) $name " . "<span style=\"color:#08c;\">(<b>" . implode(', ', $status) . "</b>)</span>";
																						if($name!=="" && strpos($content, $newline) === false)
																							$content .= $newline . "<br/>";
																					}
																					return '<a tabindex="0" class="btn btn-default popup-marker" id="item-popover_' . $ordernumber . '" data-poload="' . Yii::$app->request->baseUrl . '/ajaxrequest/getorderindexdetails?type=2&idorder='.$ordernumber.'" role="button" data-toggle="popover" data-placement="left" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . Html::encode($content) . '" rel="popover" style="color:#08c;">' . $number_items . '</a>';
																				}
																			],          
																			[
																				'class' => 'yii\grid\ActionColumn',
																				'template'=>'{receive} {update} {delete}',
																				'controller' => 'orders',
																				'buttons' => [
																				'receive' => function ($url, $model, $key) {
																					$options = [
																					'title' => 'Receive',
																					'class' => 'btn btn-info',
																					'type'=>'button',
																					'pid'=>$model->id,
																					'onClick'=>'ViewOrderDetails("'.$model->ordernumber.'");'
																							];
																					$url = "javascript:;";
																						
																					return Html::a('<span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>', $url, $options);
																				},
																				'update' => function ($url, $model, $key) {
																					$options = [
																					'title' => 'Edit',
																					'class' => 'btn btn-warning',
																					'type'=>'button'
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
                </div>    
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/purchasing.js"></script>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/receiving.js"></script>