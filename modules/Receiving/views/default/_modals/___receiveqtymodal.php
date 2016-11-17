<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Itemspurchased;
use app\models\Item;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Receive;

if($type==1) {
	$_quantity = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('In Stock', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
} else if($type==2){
	$_quantity = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('In Stock', Item::$status)])->count();	
}
?>			
						<form action="" id="add-instock-form-serialized">
							<?php foreach ($items as $item) :?>
								<?php 
									if($type==1) {
										$_quantity_to_received = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
										$_quantity_instock = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('In Stock', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
									}
									else if($type==2) {
										$_quantity_to_received = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->count();							
										$_quantity_instock = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('In Stock', Item::$status)])->count();
									}
								?>
									<?php $_linemodel = Models::findOne($item); //var_dump($customer);?>
								<?php if($_linemodel->serialized && (!empty($customer) && $customer->trackincomingserials)) :?>
								<?php 
									$_model = Models::findOne($item);
									$_man = Manufacturer::findOne($_model->manufacturer);								
								?>
									<div class="row row-margin" >
										<div class="col-md-10" id="serial-group-<?php echo $item;?>">
											<label><b><?php echo $_man->name . ' ' . $_model->descrip;?></b> <?php /*>span id="serialized-quantity-instock-<?php echo $item;?>"><?php echo $_quantity_instock;?></span> of <span id="serialized-quantity-received-<?php echo $item;?>"><?php echo $_quantity_to_received;?></span> In Stock*/?></label>										
											<div class="input-group">
												<input type="text" name="serialnumber_<?php echo $item;?>" class="form-control qserialnumber" placeholder="Enter your serial number..." value=""/>											
												<span class="input-group-btn">
													<button type="button" id="saveSerialBtn_<?php echo $item;?>" onClick="saveSerializedReceivedItem(<?php echo $model->id;?>, <?php echo $item;?>, <?php echo $type;?>)" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></button>
												</span>
											</div>
										</div>
									</div>
								<?php endif;?>
							<?php endforeach;?>
						</form>
						<form action="<?php echo Yii::$app->request->baseUrl;?>/receiving/savereceiveqty?id=<?php echo $model->id?>&type=<?php echo $type?>" id="add-instock-qty-form" method="post">
							<?php foreach ($items as $item) :?>
								<?php $_linemodel = Models::findOne($item);?>
									<?php if(!$_linemodel->serialized || ($_linemodel->serialized && !empty($customer) && !$customer->trackincomingserials)) :?>
										<?php 
												if($type==1)
													$_quantity_to_received = Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->andWhere(['purchaseordernumber' => null])->count();
												else if($type==2)
													$_quantity_to_received = Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, 'model'=>$item, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->count();
												$_model = Models::findOne($item);
												$_man = Manufacturer::findOne($_model->manufacturer);
										?>
								<div class="row row-margin">
									<div class="purchasing-qty-group">
										<label><b><?php echo $_man->name . ' ' . $_model->descrip;?> : <?php /*(><?php if($type==1) echo Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('Received', Item::$status), 'model'=>$item])->andWhere(['purchaseordernumber' => null])->count(); else if($type==2) echo Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('Received', Item::$status), 'model'=>$item])->count();?> received)</b>*/?></label>
										<input type="number" name="instockqty[]" class="form-control instockqty" placeholder="Quantity In Stock" value="0" min="0" max="<?php echo $_quantity_to_received;?>"/>	
										<input type="hidden" name="items[]" value="<?php echo $item;?>" />									
									</div>
								</div>
								<?php endif;?>
							<?php endforeach;?>
							<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>"/>
						</form>
						<input type="hidden" id="order-received-items-type" value="<?php echo $type?>" />
						<div class="row row-margin" style="font-weight:bold;">
							Confirmed <span id="total-quantity-instock"><b><?php echo $_quantity;?></b></span>
							 of 
							 <span id="total-quantity-received">
							 	<?php if($type==1) echo Item::find()->innerJoin('lv_salesorders', '`lv_salesorders`.`id` = `lv_items`.`ordernumber`')->where(['ordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->andWhere(['purchaseordernumber' => null])->count() + $_quantity; else if($type==2) echo Item::find()->innerJoin('lv_purchases', '`lv_purchases`.`id` = `lv_items`.`purchaseordernumber`')->where(['purchaseordernumber'=>$model->id, '`lv_items`.`status`'=>array_search('Received', Item::$status)])->count() + $_quantity;?>
							 </span></div>		
						