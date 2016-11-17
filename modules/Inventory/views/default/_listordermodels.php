<?php 
	use app\models\Medias;
	use app\models\Models;
?>
<?php foreach ($cart as $c) :?>
	<?php 
		$_model = Models::findOne($c->modelid);
		$_media = Medias::findOne($_model->image_id);
	?>
	<div class="row row-margin">
		<div class="form-inline">
			<img alt="" src="<?php echo Yii::getAlias('@web').'/public/images/models/'. $_media->filename;?>" onClick="ModelsViewer(<?=$c->modelid?>);" style="height: 33px"/>
			<?php if(!empty($_model->aei)) :?>
	             <a tabindex="0" class="btn btn-default popup-marker" data-content = "" id="partitem-popover_<?=$c->modelid?>" data-poload="<?php echo Yii::$app->request->baseUrl . '/ajaxrequest/getinventorypartnumbers?modelid=' . $c->modelid;?>" role="button" data-html="true" data-placement="right" data-toggle="popover" data-animation="true" data-trigger="focus" data-original-title="Owners & Parts"> <?=$c->modelid?> </a>
            <?php else :?> 
                 No Part Number
            <?php endif;?>
		</div>
	</div>
<?php endforeach;?>