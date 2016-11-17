<?php 
	use yii\helpers\Html;
	use app\models\Medias;
	use app\models\Customer;
	use app\models\Item; 

?>
<?php foreach($inventories as $inventory) :?>
	<div class="animated flipInY col-md-2 col-sm-4 col-xs-4 tile_stats_count" style="text-align:center;border-right: 1px solid #BBB;">
		<div class="" style="min-height: 100px">
			<div class="count_top" style="min-height: 32px">
				<?php 
					$date = date('Y-m-d');
					$nbDay = date('N', strtotime($date));
					//$monday = new DateTime($date);
					$sunday = new DateTime($date);
					//$monday->modify('-'.($nbDay-1).' days');
					$sunday->modify('+'.(7-$nbDay).' days');
					$lastweekday = $sunday->format('Y-m-d H:i:s');					
					//
					$count = Item::find()
								->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory->customer])
								->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory->customer])
								->count();	
					//
					$lastweekcount = Item::find()
								->where(['status'=>array_search('In Stock', Item::$status), 'customer'=>$inventory->customer])
								->orWhere(['status'=>array_search('Ready to ship', Item::$status), 'customer'=>$inventory->customer])
								->andWhere("(DATE(lastupdated) = date_sub(date('$lastweekday'), INTERVAL 1 week))")
								->count();						
					$percentage = (1 - $lastweekcount / $count) * 100; 
					
					$_output = "";
					$customer = Customer::findOne($inventory->customer);
					$_my_media = Medias::findOne($customer->picture_id);
					if(!empty($_my_media->filename)){
						$target_file = Yii::getAlias('@web').'/public/images/customers/'.$_my_media->filename;
						if (file_exists(dirname(__FILE__) . '/../../../../public/images/customers/'.$_my_media->filename)) 
							$_output = Html::img($target_file, ['alt'=>$customer->companyname, 'style'=>'cursor:pointer;max-width:100px;max-height:32px;']);						 
						else
							$_output = $customer->companyname;					
					}else 
						$_output = $customer->companyname;
				?> 
				<?= Html::a($_output, ['/customers/ownstockpage', 'id'=>$inventory->customer]) ?>
			</div>
			<div class="count"><?php echo number_format($count);?></div>
			<span class="count_bottom"><i class="green"><?php if($percentage > 0):?><i class="fa fa-sort-asc"></i><?php else :?><i class="fa fa-sort-desc"></i><?php endif;?><?php echo $percentage;?>% </i> From last Week</span>
		</div>
	</div>
<?php endforeach;?>