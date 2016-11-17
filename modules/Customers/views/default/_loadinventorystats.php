<?php 
	use yii\helpers\Html;
	use app\models\Medias;
	use app\models\Customer;
	use app\models\Item; 

?>
<div id="loading" style="background : transparent;position:absolute;top:20%;left: 48%;display:none;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"></div>
<?php foreach($inventories as $inventory) :?>
	<div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count" style="text-align:center;border-right: 1px solid #BBB;">
		<div class="" style="min-height: 100px">
			<div class="count_top" style="min-height: 32px">
				<?php 
						if($inventory['percent'] > 1)
							$percentage = $inventory['percent'] - 1;
						else 
							$percentage = 1 - $inventory['percent'];
                        $percentage = round($inventory['percent'],2); 
					$_output = "";
					$customer = Customer::findOne($inventory['customer']);
					$_my_media = Medias::findOne($customer->picture_id);
					if(!empty($_my_media->filename)){
						$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
						if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) 
							$_output = Html::img($target_file, ['alt'=>$customer['companyname'], 'style'=>'cursor:pointer;max-width:100px;max-height:32px;']);
						else
							$_output = $customer['companyname'];					
					}else 
						$_output = $customer['companyname'];
				?> 
				<?= Html::a($_output, ['/customers/ownstockpage', 'id'=>$customer['id']]) ?>
			</div>
			<div class="count"><?php echo number_format($inventory['count']);?></div>
			<span class="count_bottom">
                            <?php $class = ($percentage > 0)?'green':(($percentage < 0)?"red":"");?><i class="<?= $class; ?>"><?php if($percentage > 0):?><i class="fa fa-sort-asc"></i><?php elseif($percentage < 0):?><i class="fa fa-sort-desc"></i><?php endif;?><?php if($percentage != 0): ?><?php echo abs($percentage);?>% From last Week<?php else: ?>No Activity This Week<?php endif; ?>
                            </i>
                        </span>
		</div>
	</div>
<?php endforeach;?>