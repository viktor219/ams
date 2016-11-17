<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Item;
use app\models\Itemspurchased;
use app\models\Models;
use app\models\Manufacturer;
use app\models\Receive;

$total_received = Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('Received', Item::$status)])->count() + Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('In Transit', Item::$status)])->count();
?>
<div class="modal fade" id='ReceivePQtyDetails'>
	<div class="modal-dialog">
		<div class="modal-content">
		    <div class="modal-header"> 
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Save quantity to Receive (<?php echo $model->number_generated?>)</h4>
		    </div>
		    <div class="modal-body">
		        <div id="detaisOfPurchasingReceive">
					<div class="row row-margin">			
						<form action="<?php echo Yii::$app->request->baseUrl;?>/orders/savereceiveqty?id=<?php echo $model->id?>" id="add-receive-qty-form" method="post">
							<?php foreach ($items as $item) :?>
							<?php
								$max_to_received = Item::find()->where(['ordernumber'=>$model->id, 'model'=>$item, 'status'=>array_search('In Transit', Item::$status)])->count();
								$_quantity = Item::find()->where(['ordernumber'=>$model->id, 'status'=>array_search('Received', Item::$status)])->count();
								$_model = Models::findOne($item);
								$_man = Manufacturer::findOne($_model->manufacturer);
							?>
								<div class="row row-margin">
									<div class="purchasing-qty-group">
										<label><b><?php echo $_man->name . ' ' . $_model->descrip;?> :</b></label>
										<input type="number" name="receivingqty[]" class="form-control purchasingqty" placeholder="Quantity to Receive" value="1" min="1" max="<?php echo $max_to_received;?>"/>	
										<input type="hidden" name="items[]" value="<?php echo $item;?>" />
									</div>
								</div>
								<?php //$i++;?>
							<?php endforeach;?>
							<input type="hidden" name="_csrf" />
						</form>
					</div>		
					<div class="row row-margin" style="font-weight:bold;"><span style="color:red;"><?php echo $_quantity;?></span> of <span style="color:blue;"><?php echo $total_received;?></span> received</div>		
				</div>		
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close'); ?></button>		        
		        <button type="button" class="btn btn-primary" id="SavePReceiveQtyModal"><?php echo Yii::t('app', 'Save'); ?></button>		        
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>