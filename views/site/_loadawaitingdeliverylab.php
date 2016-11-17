<?php
use yii\helpers\Html;
use app\models\Item;
use app\models\Manufacturer;
use app\models\Models;
use app\models\Itemlog;
use app\models\User;
use yii\grid\GridView;
use app\models\Department;

?>
<?php if ($dataProvider->getTotalCount()): ?>
        <?php /*foreach ($_awaiting_delivery_items as $_awaiting_delivery_item) : ?>
            <?php
            //$item = Item::findOne($_awaiting_delivery_item->itemid);
            $model = Models::findOne($_awaiting_delivery_item->model);
            $manufacturer = Manufacturer::findOne($model->manufacturer);
            $itemlog = Itemlog::find()->where(['itemid' => $_awaiting_delivery_item->id])->one();
            $user = User::findOne($itemlog->userid);
            ?>
            <li>
                <p><input type="checkbox" class="flat awaiting_delivery_item" id="awaiting-delivery-item_<?php echo $_awaiting_delivery_item->id; ?>"/> <?php echo (isset($model->aei)) ? $model->aei . ' -' : ''; ?> <?php echo $manufacturer->name . ' ' . $model->descrip; ?> for <?php echo $user->firstname; ?> <?php echo strtoupper($user->lastname[0]); ?></p>
            </li>							
        <?php endforeach;*/ ?>
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
		'summary'=>'', 
        'columns' => [
			[
				'attribute'=>'model',
				'label'=>'Description',
    			'format'=>'raw',
				'value'=>function ($model) {
					$_model = Models::findOne($model['model']);
					$_manufacturer = Manufacturer::findOne($_model->manufacturer);
					$output = $_manufacturer->name . ' ' . $_model->descrip;
					//
					if(!empty($model['serial']))
						$output .= '(' . $model['serial'] . ')';
					//
					if(isset($model['aei']))
						$output = $model['aei'] . ' - ' . $output;
					return '<div>' . $output . '</div>';
				}
			],
			/*[
				'attribute'=>'Department',
				'format'=>'raw',
				'value'=>function ($model) {
					$_model = Models::findOne($model['model']);
					$_department = Department::findOne($_model->department);
					return '<div style="line-height:40px;">' . $_department->name . '</div>';
				}
			],*/			
			[
			'class' => 'yii\grid\ActionColumn',
			'template'=>'{markused} {picking} {delivering}',
			'contentOptions' => ['style' => 'width:175px;'],
			'visibleButtons' => [
				'picking' => function ($model, $key, $index) {
					return ($model['status']==array_search('Requested for Service', Item::$status) && empty($model->picked)) ? true : false;
				},
				'delivering' => function ($model, $key, $index) {
					return (empty($model->picked) || empty($model->received)) ? true : false;
				}				
			],
			'buttons' => [
				'markused' => function ($url, $model, $key) {					
					$options = [
						//'title' => 'Request replacement',
						'class' => 'btn btn-xs btn-warning awaiting_delivery_item',
						'id' => 'awaiting-delivery-item_' . $model['id'],
					];	

					if($model['status']==array_search('Used for Service', Item::$status))
					{
						$options['class'] = 'btn btn-xs btn-success';
						$options['id'] = 'awaiting-delivery-used-item_' . $model['id'];
					}
					
					$url = 'javascript:;';
								 
					return Html::a('<span class="glyphicon glyphicon-ok-sign"></span>', $url, $options);
				},
				'picking' => function ($url, $model, $key) {
					$options = [
					'title' => 'Pick this item',
					'class' => 'btn btn-xs btn-info',
					'id' => 'picking-item_' . $model['id'],
					];
						
					$url = 'javascript:;';
						
					return Html::a('<span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span>', $url, $options);
				},
				'delivering' => function ($url, $model, $key) {
					$options = [
					'title' => 'Deliver this item',
					'class' => 'btn btn-xs btn-primary',
					'id' => 'delivering-item_' . $model['id'],
					];
						
					$url = 'javascript:;';
						
					return Html::a('<span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>', $url, $options);
				}
			],
			]				
        ],
    ]); ?>        
        <?php /*foreach ($_delivered_items as $_delivered_item) : ?>
            <?php
	            $model = Models::findOne($_delivered_item->model);
	            $manufacturer = Manufacturer::findOne($model->manufacturer);
	            $itemlog = Itemlog::find()->where(['itemid' => $_delivered_item->id])->one();
	            $user = User::findOne($itemlog->userid);
            ?>
            <li>
                <p><input type="checkbox" class="flat awaiting_delivered_item" checked="checked" id="awaiting-delivery-item_<?php echo $_delivered_item->id; ?>"/> <?php echo (isset($model->aei)) ? $model->aei . ' -' : ''; ?> <?php echo $manufacturer->name . ' ' . $model->descrip; ?> for <?php echo $user->firstname; ?> <?php echo strtoupper($user->lastname[0]); ?></p>
            </li>							
        <?php endforeach;*/ ?>
<?php else: ?>
    <div class="items-delivered text-center">
        All items have been delivered!
    </div>
    <div class="text-center">
        <span class="glyphicon glyphicon-ok" style="color: #1abb9c; font-size: 25px;"></span>
    </div>
<?php endif; ?>