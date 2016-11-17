<?php 
	use app\models\Medias;
	use app\models\ModelsPicture;
	
	$session = Yii::$app->session;
?>
<style>
	.bxslider li {list-style-type: none;text-align: center;}
	.bxslider li img {}
</style>
<ul class="bxslider">
	<?php foreach (ModelsPicture::find()->where(['modelid'=>$model->id])->all() as $picture):?>
		<li><img src="<?php echo Yii::$app->request->baseUrl . '/public/images/models/'. Medias::findOne($picture->mediaid)->filename;?>" style="max-height:300px;"/></li>
	<?php endforeach;?> 
</ul><?php /*
       <div id="bx-pager">
       		<?php foreach (ModelsPicture::find()->where(['modelid'=>$model->id])->all() as $key => $picture):?>
          		<a data-slide-index="<?php echo $key;?>" href=""><img src="<?php echo Yii::$app->request->baseUrl . '/public/images/models/'. Medias::findOne($picture->mediaid)->filename;?>"/></a>
          	<?php endforeach;?>
       </div> */ ?>