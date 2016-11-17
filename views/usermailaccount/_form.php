<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Users;

/* @var $this yii\web\View */
/* @var $model app\models\UserMailAccount */
/* @var $form yii\widgets\ActiveForm */

?>
 <?php $form = ActiveForm::begin(['options' => ['id'=>'user-mail-account-form']]); ?>
	<div class="col-lg-12 col-xs-12">
		<div class="x_panel" style="padding: 10px 10px;">
			<div class="x_title">
				<h2><i class="fa fa-bars"></i> <?= Html::encode($this->title) ?></h2>
				<ul class="nav navbar-right panel_toolbox">
					<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="padding:0;margin-top:0;">
				<div class="form-group col-md-6">
					<?= $form->field($model, 'userid')
					     ->dropDownList(ArrayHelper::map(\app\models\Users::find()->asArray()->all(), 'id',
							function($model, $defaultValue) {
								return $model['firstname'] . ' ' . $model['lastname'];
							}
						), ['prompt'=>'Select User','class'=>'select2_user','onchange'=>"loadUserInfo(this.value)"])->label(false)
					?>    
					<label>Username: </label>
					<input type="text" value="" readonly="readonly" class="form-control" id="username"/>
			
				    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
			
			        <?= Html::submitButton($model->isNewRecord ? '<span class="glyphicon glyphicon-save"></span> Create' : '<span class="glyphicon glyphicon-edit"></span> Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-warning']) ?>
			    </div>
			</div>
		</div>
	</div>
<?php ActiveForm::end(); ?>