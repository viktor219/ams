<?php 
	
	/*
	 * 
	 * 
	 *<div class="customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'owner_id')->textInput() ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'companyname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trackserials')->textInput() ?>

    <?= $form->field($model, 'requireordernumber')->textInput() ?>

    <?= $form->field($model, 'picture_id')->textInput() ?>

    <?= $form->field($model, 'vert_picture_id')->textInput() ?>

    <?= $form->field($model, 'defaultreceivinglocation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'defaultshippinglocation')->textInput() ?>

    <?= $form->field($model, 'defaultbillinglocation')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'modified_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
	 */

?>

<?= $this->render('_modals/_addcustomer', ['model'=>$model]);?>