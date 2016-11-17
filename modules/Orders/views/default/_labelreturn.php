<?php 
	
	use yii\helpers\Html;
	
	$this->title = 'Return Label';
	
	$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.label_img
	{
		margin-top: 150px;
		max-width: 800px;
		max-height: 400px;
	    -ms-transform: rotate(90deg); /* IE 9 */
	    -webkit-transform: rotate(90deg); /* Chrome, Safari, Opera */
	    transform: rotate(90deg);
	}
</style>
<?php 
/*<?= $this->render("_modals/_sendlabelmail");?>
	<div class="" >
		<div class="x_panel" style="padding: 10px 10px;">
			<div class="x_title">
				<div class="col-md-9">
					<h2><i class="fa fa-bars"></i> <?= $this->title ?></h2>
				</div>
				<div class="col-md-3" style="margin: 0; padding: 0">
					<ul class="nav navbar-right">
						<li><a class="collapse-link" style="line-height: 0px;"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<?= Html::a('<span class="glyphicon glyphicon-envelope"></span> Send Email', 'javascript:;', ['class' => 'btn btn-success', 'style' => 'margin-left: 75px;border-radius:4px;', 'onClick'=>'OpenLabelMail('.$model->id.');']) ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="x_content" style="min-height: 800px">
				<div class="row" style="text-align: center;">
					<img class="label_img" src="data:image/gif;base64,<?php echo $response['pkgs'][0]['label_img'];?>" />
				</div>
			</div>
		</div>
	</div>
	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/labelreturn.js"></script>*/ 
?>
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                         <?php foreach($files as $index => $file): ?>
                            <li data-target="#myCarousel" data-slide-to="<?php echo $index; ?>" class="<?php echo (!$index)?'active':''; ?>"></li>
                        <?php endforeach; ?>
                    </ol> 
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php foreach($files as $index => $file): ?> 
                            <div class="item <?php echo (!$index)?'active':''; ?>">   
                                <img class="label_img" src="<?php echo Yii::$app->request->baseUrl  . '/public/medias/labels/' . str_replace('.pdf', '', $file->filename); ?>.png" />
                            </div>
                        <?php endforeach; ?> 
                    </div>
                    <!-- Left and right controls -->
                    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>           