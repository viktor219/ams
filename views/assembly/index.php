<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Models;
use app\models\Manufacturer;
use app\models\ModelAssembly;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Assemblies';
$this->params['breadcrumbs'][] = ['label' => 'Inventory', 'url' => ['/inventory/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}
</style>
<div class="model-assembly-index">

    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row vertical-align">
                <div class="col-md-9 vcenter">
                    <h4>
                        <span class="glyphicon glyphicon-list-alt"></span>
                        <?= Html::encode($this->title) ?>
                    </h4>
                </div>
				<div class="col-md-3 vcenter text-right">			
					<div class="col-md-3">
						<?= Html::a('Create Model Assembly', ['create'], ['class' => 'btn btn-success']) ?>
					</div>				
				</div>                
            </div>
        </div>
        <div class="panel-body">
		    <?= GridView::widget([
		        'dataProvider' => $dataProvider,
	    		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
	    		'summary'=>'',		    		
		        'columns' => [
					[
			            'attribute'=>'modelid',
						'format' => 'raw',
						'value'=> function ($model) {
							$_output = Models::findOne($model->modelid)->descrip;
							return "<div style='line-height: 40px;'>" . $_output . "</div>";
						}
					],
					[
		            	'label'=>'Quantity',
						'format' => 'raw',
						'value'=> function ($model) {
							//$number_items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->count();
							$number_items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->sum('quantity');
							$items = ModelAssembly::find()->where(['modelid'=>$model->modelid])->all();
							$content = "";
							foreach($items as $item)
							{
								$_model = Models::findOne($item->partid);
								$_manufacturer = Manufacturer::findOne($_model->manufacturer);
								$newline = '(' . $item->quantity . ') ' . $_manufacturer->name . ' ' . $_model->descrip;		
								if($name!=="" && strpos($content, $newline) === false)
									$content .= $newline . "<br/>";														
							}
							return '<a tabindex="0" class="btn btn-default" id="assembly-popover_' . $model->id . '" role="button" data-toggle="popover" rel="popover" data-html="true" data-animation="true" data-trigger="focus" title="Items ('. $number_items .')" data-content="' . Html::encode($content) . '" style="color:#08c;">' . $number_items . '</a>';
						}
					],
					/*[
						'attribute'=>'quantity',
						'format' => 'raw',
						'value'=> function ($model) {
							$_output = $model->quantity;
							return "<button class='btn btn-default'>" . $_output . "</button>";
						}
					],	*/				
					[
						'attribute'=>'created_at',
						'format' => 'raw',
						'value'=> function ($model) {
							$_output = $model->created_at;
							return "<div style='line-height: 40px;'>" . $_output . "</div>";
						}
					],
		
		            //['class' => 'yii\grid\ActionColumn'],
		        ],
		    ]); ?>
		</div>
	</div>

</div>
