<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vendor */

$this->title = $model->vendorname;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Orders', 'url' => ['/purchasing/index']];
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-view">
	<div class="x_panel">
		<div class="x_title">
			<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
			<ul class="nav navbar-right panel_toolbox">
				<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
			</ul>
			<div class="clearfix"></div>
		</div>
		<div class="x_content" style="margin:0;">
			<div class="" role="tabpanel" data-example-id="togglable-tabs">
				<div id="myTabContent" class="tab-content">
				    <?= DetailView::widget([
				        'model' => $model,
				        'attributes' => [
				            'vendorid',
				            'vendorname',
				            'address_line_1',
				            'address_line_2',
				            'city',
				            'zip',
				            'state',
				            'contact',
				            'telephone_1',
				            'telephone_2',
				            'fax',
				            '1099_type',
				            'taxidno',
				            'terms',
				            'active',
				            'usebillpay',
				            'accountno',
				            'email:email',
				            'website',
				            'expense_account_id',
				            'last_inv_amt',
				            'notes:ntext',
				            'date_joined',
				        ],
				    ]) ?>
				 </div>
			</div>
		</div>	
	</div>	
</div>
