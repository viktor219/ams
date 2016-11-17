<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Help & Training';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render("_modals/_video"); ?>
<div class="Training-default-index">
    <div class="row row-margin">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row vertical-align">
                    <div class="col-md-9 vcenter">
                        <h4>
                            <span class="glyphicon glyphicon-equalizer"></span>
                            Help & Training
                        </h4>
                    </div>
                    <div class="col-md-3 vcenter text-right">
                    </div>
                </div>		
            </div>
            <div class="panel-body">	                                       
                <div class="col-md-12">
                    <?php
                    $count = 1;
                    foreach($videos as $video):
                        /*add row after 4th video for layout.*/
                        if ($count%4 == 1)
                        {
                            echo "<div class='row video-wrapper'>";
                        }

                        ?>
                        <div class="col-md-3 col-sm-4 col-xs-6 video-details">
                        <h3 style="font-size: 15px; text-align: center; min-height: 30px;" class="video-title"><b><?= $video->title; ?></b></h3>
<!--                        <div class="col-md-12" style="padding: 0; text-align: center">
                                <?php /*$video->description; */?>
                        </div>-->
                        <div class="col-md-12" style="text-align: center">
                            <a href="<?= Yii::$app->request->baseUrl.'/uploads/training/'.$video->filename; ?>" class="btn-danger watch_video"><img src="<?= Yii::$app->request->baseUrl.'/uploads/training/thumbnails/'.$video->thumbnails;?>" class="img-responsive thumbnai-wrapper" width="250px" title="<?=$video->title?>" /> </a>

                        </div>
                    </div>
                        <?php
                        if ($count%4 == 0)
                        {
                            echo "</div>";
                        }
                        $count++;
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
       $('.watch_video').click(function(){
           var href = $(this).attr('href');
           var title = $(this).parents('.video-details').find('.video-title b').html();
           $('.modal-title').html('<span class="fa fa-youtube-play"></span> '+title);
           var html = '<video width="100%" controls autoplay><source src="'+href+'" type="video/mp4"></video>'
           $('#videoPlayer .modal-body').html(html);
           $('#videoPlayer').modal('show');
          return false; 
       });
       //when hidden
        $('#videoPlayer').on('hidden.bs.modal', function(e) { 
            $('#videoPlayer .modal-body').html('');
        });
    });
</script>
