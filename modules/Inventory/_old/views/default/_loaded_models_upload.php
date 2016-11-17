<?php 
	use app\models\Medias;
	use app\models\ModelsPicture;
	
	$session = Yii::$app->session;
?>
<style>
	.loaded-image-s
	{
	    max-width: 120px;
	    max-height: 80px;
	    border: 2px solid #BBB;
	    border-radius: 8px;
	    box-shadow: 0 0 10px #999;
	    padding: 5px;
	}
</style>
<?php if(isset($session['__new_model_key'])) :?>
	<?php foreach (ModelsPicture::find()->where(['_key'=>$session['__new_model_key']])->all() as $picture):?>
		<div class="col-md-3">
			<img src="<?php echo Yii::$app->request->baseUrl . '/public/images/models/'. Medias::findOne($picture->mediaid)->filename;?>" class="loaded-image-s"/>
		</div>
	<?php endforeach;?> 
<?php endif;?>