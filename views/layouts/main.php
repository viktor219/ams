<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\User;
use app\models\Medias;
use app\models\ItemRequested;

AppAsset::register($this);

$requests = ItemRequested::find()->all();
$model = User::findOne(Yii::$app->user->id);
$profile_image = Yii::getAlias('@web').'/public/images/users/default.jpg';
if($model->picture_id){
    $mediaModel = Medias::findOne($model->picture_id);
    if(file_exists(Yii::getAlias('@webroot').'/'.$mediaModel->path.$mediaModel->filename)){
        $profile_image = Yii::getAlias('@web').$mediaModel->path.$mediaModel->filename;
    }
}
?>
<?php //$this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="<?= Yii::$app->charset ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
		<title><?= Html::encode($this->title) ?></title>
		<?php $this->head() ?>
		<!-- Bootstrap core CSS -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/bootstrap.min.css" rel="stylesheet">
		<!-- Fonts -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/fonts/css/font-awesome.min.css" rel="stylesheet">
		<!-- Animation -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/animate.min.css" rel="stylesheet">
		<!-- Custom styling plus plugins -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/custom.css" rel="stylesheet">	
		<!-- Map -->
			<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl;?>/public/css/maps/jquery-jvectormap-2.0.1.css" />
		<!-- Checkbox -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/icheck/flat/green.css" rel="stylesheet" />
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/floatexamples.css" rel="stylesheet" type="text/css" />
			<script>
				var jsCrsf = "<?=Yii::$app->request->getCsrfToken()?>";
				var jsBaseUrl = "<?php echo Yii::$app->request->baseUrl;?>";
				var shipByPurchaseDate = "<?php echo date("m/d/Y", strtotime("+3 weekday"))?>";
				var shipByServiceDate = "<?php echo date("m/d/Y", strtotime("+10 weekday"))?>";
				var shipByIntegrationDate = "<?php echo date("m/d/Y", strtotime("+2 weekday"))?>";
				var shipByWarehouseDate = "<?php echo date("m/d/Y", strtotime("+2 weekday"))?>";
			</script>
		<!-- Load JQuery plugin -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.min.js"></script>
		<!-- select2 -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/select/select2.min.css" rel="stylesheet">		
		<!-- switchery -->
			<link rel="stylesheet" href="<?php echo Yii::$app->request->baseUrl;?>/public/css/switchery/switchery.min.css" />
		<!-- Uploader styles -->
		<!-- File uploader -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/uploader.css" rel="stylesheet" />
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/demo.css" rel="stylesheet" />
		<!-- Progress Bar -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/nprogress.js"></script>	
			<script>
				NProgress.start();
			</script>
			<!--<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/chosen.css" rel="stylesheet">-->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/bootstrap-switch.css" rel="stylesheet">
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/bootstrap-switch.js"></script>
			<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
			<?php if(
				Yii::$app->urlManager->parseRequest(Yii::$app->request)[0] !== "users/default/index"
				&& Yii::$app->urlManager->parseRequest(Yii::$app->request)[0] !== "inventory/default/index"
				&& Yii::$app->urlManager->parseRequest(Yii::$app->request)[0] !== "orders/default/index") :?>
				<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
				<!--<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>-->
			<?php endif;?>
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.mockjax.js"></script>
        	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/bootstrap-typeahead.js"></script>
        	<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.fileupload.js"></script>
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/jquery.filer.css" type="text/css" rel="stylesheet" />
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/themes/jquery.filer-dragdropbox-theme.css" type="text/css" rel="stylesheet" />
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.filer.min.js?v=1.0.5"></script>
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/drag_custom.js?v=1.0.5"></script>
		<!-- Popover -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/tooltip.js"></script>
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/popover.js"></script>
		<!-- Sound Player Library -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/ion.sound.min.js"></script>
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.playSound.js"></script>
		<!-- JQuery Form libs -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.form.js"></script>		
		<!--[if lt IE 9]>
			<script src="<?php echo Yii::$app->request->baseUrl;?>/assets/public/js/ie8-responsive-file-warning.js"></script>
			<![endif]-->

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
			<![endif]-->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/custom_styles.css" type="text/css" rel="stylesheet" />
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/responsive.css" type="text/css" rel="stylesheet" />
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/pdfobject.js"></script>
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/functions.js"></script>
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/dropzone.css" type="text/css" rel="stylesheet" />
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/dropzone.js"></script>
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/jquery.elevatezoom.min.js"></script>
			<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/sumoselect/jquery.sumoselect.min.js"></script>
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/js/sumoselect/sumoselect.css" type="text/css" rel="stylesheet" />
			<!-- bxSlider Javascript file -->
			<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/bxslider/jquery.bxslider.min.js"></script>
			<!-- bxSlider CSS file -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/js/bxslider/jquery.bxslider.css" rel="stylesheet" />
			<script>
			<!-- zooming -->
			var $viewportMeta = $('meta[name="viewport"]');
			$('input, select, textarea').bind('focus blur', function(event) {
				$viewportMeta.attr('content', 'width=device-width,initial-scale=1,maximum-scale=' + (event.type == 'blur' ? 10 : 1));
			});
				/*function initSounds() {
					window.sounds = new Object();
					var soundSuccess = new Audio('http://assetenterprises.com/testing/live/public/medias/notifications/success.aac');
					var soundError = new Audio('http://assetenterprises.com/testing/live/public/medias/notifications/error.acc');
					soundSuccess.load();
					soundError.load();
					window.sounds['success.mp3'] = soundSuccess;
					window.sounds['error.mp3'] = soundError;
				}*/
				$(document).ready(function () {
					//
						ion.sound({
							sounds: [
								{
									name: "success"
								},
								{
									name: "error",
									volume: 0.2
								}
							],
							volume: 0.5,
							path: jsBaseUrl+"/public/medias/notifications/",
							preload: true
						});						
					//
				    $('form#add-serial-form').submit(function(event) {

				    	$('.col-md-12').removeClass('has-error'); 
				    	$('.help-block').remove(); // remove the error text
					    
				    	var vserialnumber = $('input[name=serialnumber]').val();
				    	var vlanenumber = $('input[name=lanenumber]').val();
				    	var vorder = $('#serialOrderId').val();
				    	var vcurrentmodel = $('#serialCurrentModel').val();
				    	var vcurrentitem = $('#serialCurrentItem').val();
				    	var vcustomer = $('#customerId').val();

				    	//alert(vcurrentitem);
				    	var currentpickbutton = $('#pickbutton'+ vcurrentmodel);
				    	var currentpickcountbutton = $('#picked-count-button-'+ vcurrentmodel);
				    	var currentinstockcountbutton = $('#instock-count-button-'+ vcurrentmodel);
						
				    	if (vserialnumber.length == 0) {
							$('#qserialnumber').focus();
							//window.sounds['error.mp3'].play();
							ion.sound.play("error");
							$('#serial-group').addClass('has-error'); // add the error class to show red input
							$('#serial-group').append('<div class="help-block">Serial Number field is required!</div>'); // add the actual error message under our input
						}else{
							$('#qserialnumber').focus();
						//verify serial numbers...
				 	       $.ajax({
					            url: jsBaseUrl+"/orders/default/validateserial",
					            data: {
									"serial": vserialnumber,
									"currentmodel": vcurrentmodel,
									"customer": vcustomer
					            },
					            dataType: "json",
					            encode          : true
					        }).done(function (data) {
								//alert(data.toSource());
								if(data.error) {
									$('#qserialnumber').focus();
									//play error sound 
									//$.playSound('http://assetenterprises.com/testing/live/public/medias/notifications/error');	
									//window.sounds['error.mp3'].play();
									ion.sound.play("error");
									$('#serial-group').addClass('has-error'); // add the error class to show red input
									$('#serial-group').append('<div class="help-block">' + data.html + '</div>'); // add the actual error message under our input									
								}else if(data.success){
									$('#qserialnumber').focus();
									//save serial number...
								    $.ajax({
										type        : 'POST',
										url: jsBaseUrl+"/orders/default/saveserial",
										data: {
											"serial": vserialnumber,
											"lane": vlanenumber,
											"order": vorder,
											"currentmodel": vcurrentmodel,
											"currentitem": vcurrentitem,
											"_csrf":jsCrsf
										},
										dataType: "json",
										encode          : true
									}).done(function (data) {
										//alert(data.toSource());
										if(data.success)
										{
											$('#qserialnumber').focus();
											//$.playSound('http://assetenterprises.com/testing/live/public/medias/notifications/success');
											//window.sounds['success.mp3'].play();
											ion.sound.play("success");
											$('#add-serial-form')[0].reset();
											currentpickcountbutton.html(parseInt(currentpickcountbutton.text())+1);
											currentinstockcountbutton.html(parseInt(currentinstockcountbutton.text())-1);
											if(parseInt(currentinstockcountbutton.text())-1==0)
											{
												currentinstockcountbutton.removeClass('btn-warning');
												currentinstockcountbutton.addClass('btn-danger');
											}
											//change button status 
											//alert(data.message);
											if(data.done){
												currentpickbutton.removeAttr("onClick");
												currentpickbutton.attr('class', "glyphicon glyphicon-ok btn btn-warning");
												$('#addSerials').modal('hide'); 
												//alert(currentpickcountbutton.text());	
												new PNotify({
													title: 'Notifications',
													text: data.message,
													type: 'info',
													styling: "bootstrap3",
													opacity: 0.8,
													delay: 5000
												});												
											}else{ 
												loadSerializedNextModel(vcurrentmodel, vorder);
											}								
										}
									});		
								}
							});
				    	}

						// stop the form from submitting the normal way and refreshing the page
						event.preventDefault();
						//
						return  false;
				    });			    
				});
				//
				$(document).ready(function () {
					$('form#request-vitem-form').submit(function(event) {
						$('.col-sm-6').removeClass('has-error');
						$('.col-md-14').removeClass('has-error');
						$('.help-block').remove(); // remove the error text
					//
						var vdescription = $('#v_description').val();
						var vmanpart = $('#v_manpart').val();
						var vmanufacturer = $('#v_manufacturer').val();
						var vcategory = $('#v_category').val();
						var vdepartement = $('#v_departement').val();
					//
						if (vdescription.length == 0) {
							$('#rv_description-group').addClass('has-error'); // add the error class to show red input
							$('#rv_description-group').append('<div class="help-block">Description field is required!</div>'); // add the actual error message under our input
						} else if (vmanpart.length == 0) {
							$('#rv_manpart-group').addClass('has-error'); // add the error class to show red input
							$('#rv_manpart-group').append('<div class="help-block">Manufacturer Part Number field is required!</div>'); // add the actual error message under our input
						} else if (!vmanufacturer) {
							$('#rv_manufacturer-group').addClass('has-error'); // add the error class to show red input
							$('#rv_manufacturer-group').append('<div class="help-block">Manufacturer must been selected!</div>'); // add the actual error message under our input
						} else if (!vcategory) {
							$('#rv_category-group').addClass('has-error'); // add the error class to show red input
							$('#rv_category-group').append('<div class="help-block">Category must been selected!</div>'); // add the actual error message under our input
						} else if (!vdepartement) {
							$('#rv_departement-group').addClass('has-error'); // add the error class to show red input
							$('#rv_departement-group').append('<div class="help-block">Departement must been selected!</div>'); // add the actual error message under our input
						}else {
							$('#request-vitem-form')[0].submit();
						}
						// stop the form from submitting the normal way and refreshing the page
						event.preventDefault();
						//
						return  false;
					});
					//
				});
			</script>
	</head>
	<body class="nav-md">
		<?php $this->beginBody() ?>
		<div class="container body">
			<div class="main_container">
				<div id="loading" style="background : transparent;position:fixed;top:0;display:none;left:50%;z-index:100000;"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/ellipsis.gif" style="max-width:80px;max-width:40px;"/></div>
				<div class="col-md-3 left_col" style="position:fixed;">
					<div class="left_col scroll-view">
						<div class="navbar nav_title" style="border: 0;">
							<p class="flotte">
								<a href="<?= Url::to(['/site/index']) ?>" class="site_title"><img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/icons/ams-icon.png" alt="..." id="app-icon" class="profile_img"></a>
							</p>
							<div class="app_name" style="margin-top: 10px"><b>A&nbsp;&nbsp;S&nbsp;&nbsp;S&nbsp;&nbsp;E&nbsp;&nbsp;T</b></div>				
							<div class="app_name app_mini_name" style="font-size:11px;float: left;margin-left: 10px;font-size: 9px;position: relative;bottom: 5px;">M A N A G E M E N T &nbsp;&nbsp;S Y S T E M</div>							
						</div>
						<div class="clearfix"></div>
						<!-- menu prile quick info -->
						<div class="profile">
                            <div class="profile_info">
								<span>Welcome,</span>
								<h2 style="text-align: right;"><?php echo Yii::$app->user->identity->firstname . ' ' . Yii::$app->user->identity->lastname;?></h2>
							</div>
							<div class="profile_pic">
								<img src="<?php echo $profile_image;?>" alt="..." class="img-circle profile_img">
							</div>
						</div>
						<!-- /menu prile quick info -->
						<!-- sidebar menu -->
						<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
							<div class="menu_section">
								<ul class="nav side-menu" style="margin-top:80px;">
									<li><a href="<?= Url::to(['/overview/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-tasks"></span> Overview <span class="sr-only">(current)</span></a></li>
									<?php if( Yii::$app->user->identity->usertype===User::TYPE_ADMIN || 
											Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN ||
											Yii::$app->user->identity->usertype===User::TYPE_SALES || 
											Yii::$app->user->identity->usertype===User::TYPE_SHIPPING ||
											Yii::$app->user->identity->usertype===User::TYPE_BILLING
											):?>
										<li><a href="<?= Url::to(['/orders/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-equalizer"></span> Sales Orders</a></li>
										<li><a href="<?= Url::to(['/purchasing/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-tags"></span> Purchasing</a></li>
										<li><a href="<?= Url::to(['/receiving/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-save"></span> Receiving</a></li>
										<li><a href="<?= Url::to(['/inprogress/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-cog"></span> In Progress</a></li>
										<li><a href="<?= Url::to(['/shipping/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-open"></span> Shipping</a></li>
										<li><a href="<?= Url::to(['/billing/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-send"></span> Billing</a></li>
										<li><a href="<?= Url::to(['/inventory/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-list-alt"></span> Inventory</a></li>
										<li><a href="<?= Url::to(['/users/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-user"></span> User Accounts</a></li>
										<li><a href="<?= Url::to(['/customers/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-globe"></span> Customers</a></li>
										<li><a href="<?= Url::to(['/analytics/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-dashboard"></span> Analytics</a></li>
									<?php endif;?>
									<?php if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER):?>
										<li><a href="<?= Url::to(['/orders/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-equalizer"></span> Orders</a></li>
										<li><a href="<?= Url::to(['/inprogress/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-cog"></span> In Progress</a></li>
										<li><a href="<?= Url::to(['/inventory/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-list-alt"></span> Inventory</a></li>
										<?php if(Yii::$app->user->identity->usertype===User::REPRESENTATIVE):?>
											<li><a href="<?= Url::to(['/users/update', 'id'=>Yii::$app->user->id]) ?>" class="navlinks"><span class="glyphicon glyphicon-user"></span> Settings</a></li>
										<?php else :?>
											<li><a href="<?= Url::to(['/location/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-globe"></span> Locations</a></li>
											<li><a href="<?= Url::to(['/users/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-user"></span> User Accounts</a></li>
										<?php endif;?>
										<li><a href="<?= Url::to(['/analytics/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-dashboard"></span> Analytics</a></li>
									<?php endif;?>
									<?php /*if(Yii::$app->user->identity->usertype===User::TYPE_TECHNICIAN):?>
										<li><a href="<?= Url::to(['/purchasing/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-tags"></span> Purchasing</a></li>
										<li><a href="<?= Url::to(['/inprogress/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-cog"></span> In Progress</a></li>
										<li><a href="<?= Url::to(['/inventory/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-list-alt"></span> Inventory</a></li>
										<li><a href="<?= Url::to(['/analytics/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-dashboard"></span> Analytics</a></li>
									<?php endif;?>
									<?php if(Yii::$app->user->identity->usertype===User::TYPE_RECEIVING || Yii::$app->user->identity->usertype===User::TYPE_SHIPPING):?>
										<li><a href="<?= Url::to(['/purchasing/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-tags"></span> Purchasing</a></li>
										<li><a href="<?= Url::to(['/inprogress/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-cog"></span> In Progress</a></li>
										<li><a href="<?= Url::to(['/receiving/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-save"></span> Receiving</a></li>
										<li><a href="<?= Url::to(['/shipping/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-open"></span> Shipping</a></li>
										<li><a href="<?= Url::to(['/inventory/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-list-alt"></span> Inventory</a></li>
										<li><a href="<?= Url::to(['/analytics/index']) ?>" class="navlinks"><span class="glyphicon glyphicon-dashboard"></span> Analytics</a></li>
									<?php endif;*/?>
								</ul>
							</div>
						</div>
						<!-- /sidebar menu -->
						<!-- /menu footer buttons -->
						<div class="sidebar-footer hidden-small">
							<a data-toggle="tooltip" data-placement="top" title="Settings">
								<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="FullScreen">
								<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="Lock">
								<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
							</a>
							<a data-toggle="tooltip" data-placement="top" title="Logout" href="<?= Url::to(['/site/logout']) ?>">
								<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
							</a>
						</div>
						<!-- /menu footer buttons -->
					</div>
				</div>
				<!-- top navigation -->
				<div class="top_nav">
					<div class="nav_menu">
						<nav class="" role="navigation">
							<div class="nav toggle">
								<a id="menu_toggle"><i class="fa fa-bars"></i></a>
							</div>
							<ul class="nav navbar-nav navbar-right">
								<li class="">
									<a href="javascript:;"  class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<img src="<?php echo $profile_image;?>" alt=""> <?php echo Yii::$app->user->identity->username;?>
										<span class=" fa fa-angle-down"></span>
									</a>
									<ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
										<li><a href="<?php echo Yii::$app->request->baseUrl;?>/users/profile">  Profile</a>
										</li>
										<li><a href="<?= Url::to(['/training']) ?>">  Help & Training</a>
										</li>
										<li><a href="<?= Url::to(['/site/logout']) ?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
										</li>
									</ul>
								</li>
							<?php if(Yii::$app->user->identity->usertype===User::TYPE_ADMIN || Yii::$app->user->identity->usertype===User::TYPE_CUSTOMER_ADMIN):?>
								<?php if($requests !== null && count($requests) !== 0) : ?>
									<?php 
										$today = date('Y-m-d H:i:s');
										$datetime = new DateTime($today);
									?>
									<li role="presentation" class="dropdown">
										<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
											<i class="fa fa-envelope-o"></i>
											<span class="badge bg-green"><?php echo count($requests);?></span>
										</a>
										<ul id="menu1" class="dropdown-menu list-unstyled msg_list animated fadeInDown" role="menu">
											<?php foreach($requests as $request) :?>
												<?php 
													$timeago = $datetime->diff(new DateTime($request->date_added));
												?>
												<li>
													<a href="javascript:;" onclick="validateRequest('<?php echo $request->id;?>', '<?php echo $request->description;?>', '<?php echo $request->manpartnum;?>');">
														<span>
															<span>New Item Requested</span>
															<span class="time"><?php if($timeago->d==0) echo $timeago->i . ' mins'; else echo $timeago->d . ' days';?> ago</span>
														</span>
														<span class="message">
															You have a new item requested. 
														</span>
													</a>
												</li>
											<?php endforeach;?>
											<li>
												<div class="text-center">
													<a href="#">
														<strong>See All Alerts</strong>
														<i class="fa fa-angle-right"></i>
													</a>
												</div>
											</li>
										</ul>
									</li>
								<?php endif;?>
							<?php endif;?>
							</ul>
						</nav>
					</div>
				</div>
				<!-- /top navigation -->
				<!-- page content -->
				<div class="right_col" role="main">
					<div class="breadcrumbs_g">
						<?= Breadcrumbs::widget([
							'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
						]) ?>
					</div>
					<div class="row" id="row-master">
						<?php /*= Yii::$app->session->getFlash('error'); ?>
						<?= Yii::$app->session->getFlash('success'); */?>
					</div>
					<?= $content ?>
					<?php //LOAD PICTURE SHOWING WIDGET --->?>
					<?= $this->render("_modals/_showimage");?>
					<?= $this->render("_modals/_modelslide");?>
					<?php //LOAD MODEL ADDING FORM --->?>
					<?= $this->render("_modals/_requestitemvalidate");?>
					<!-- footer content -->
					<footer>
						<div class="">
							<p class="pull-right mobile-app-name">Asset Management System |
								<span class="lead"> <img src="<?php echo Yii::$app->request->baseUrl;?>/public/images/icons/ams-icon.png" alt="..." id="app-icon" class="img-circle profile_img"> Copyright &copy; <?php echo date('Y');?></span>
							</p>
						</div>
						<div class="clearfix"></div>
					</footer>
					<!-- /footer content -->
				</div>
				<!-- /page content -->
			</div>
		</div>
		<div id="custom_notifications" class="custom-notifications dsp_none">
			<ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
			</ul>
			<div class="clearfix"></div>
			<div id="notif-group" class="tabbed_notifications"></div>
		</div>
	<!-- File uploader -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uploader/dmpreview.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uploader/dmuploader.js"></script>
	<!-- Shared scripts -->
	<script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/shared_scripts.js"></script>
    <!-- gauge js -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/gauge/gauge.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/gauge/gauge_demo.js"></script>
    <!-- chart js -->
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/chartjs/chart.min.js"></script>
    <!-- bootstrap progress js -->
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/progressbar/bootstrap-progressbar.min.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/nicescroll/jquery.nicescroll.min.js"></script>
    <!-- icheck -->
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/icheck/icheck.min.js"></script>
    <!-- PNotify -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/notify/pnotify.core.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/notify/pnotify.buttons.js"></script>
	<script type="text/javascript">
	<?php if(Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('warning') || Yii::$app->session->hasFlash('danger')) :?>
		$(function(){
			new PNotify({
				title: 'Notifications',
				text: '<?php if(Yii::$app->session->hasFlash('success')) :?><?= Yii::$app->session->getFlash('success');?><?php endif;?><?php if(Yii::$app->session->hasFlash('warning')) :?><?= Yii::$app->session->getFlash('warning');?><?php endif;?><?php if(Yii::$app->session->hasFlash('danger')) :?><?= Yii::$app->session->getFlash('danger');?><?php endif;?>',
				type: '<?php if(Yii::$app->session->hasFlash('success')) :?>success<?php endif;?><?php if(Yii::$app->session->hasFlash('warning')) :?>notice<?php endif;?><?php if(Yii::$app->session->hasFlash('danger')) :?>error<?php endif;?>',
				styling: "bootstrap3",
				opacity: 0.8,
				delay: 5000
			});
			//PNotify.prototype.options.styling = "bootstrap3";
		});
	<?php endif;?>
	</script>
    <!-- daterangepicker -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/datepicker/daterangepicker.js"></script>
        <!-- switchery -->
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/switchery/switchery.min.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/custom.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/item_request.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/reorder.js"></script>
    <!-- flot js -->
    <!--[if lte IE 8]><script type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.pie.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.orderBars.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.time.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/date.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.spline.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.stack.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/curvedLines.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/flot/jquery.flot.resize.js"></script>
    <!-- form wizard -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/wizard/jquery.smartWizard.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Smart Wizard 	
            $('#wizard').smartWizard({
				includeFinishButton : false,
				enableAllSteps: true,
				hideButtonsOnDisabled: true,
				noForwardJumping:true,
			});

            function onFinishCallback() {
                $('#wizard').smartWizard('showMessage', 'Finish Clicked');
                //alert('Finish Clicked');
            }
        });

        $(document).ready(function () {
            // Smart Wizard	
            $('#wizard_verticle').smartWizard({
                transitionEffect: 'slide'
            });

        });
    </script>
        <!-- Autocomplete -->
        <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/autocomplete/countries.js"></script>
        <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/autocomplete/jquery.autocomplete.js"></script>
		<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/select/select2.full.js"></script>
		<script type="text/javascript">
			$('.location_zip').focusout(function(e) {
				var id = $(this)[0].id;
				id = id.replace('_zip', '');
				//alert(id);
				var client = new XMLHttpRequest();
				//var data, response;
				client.open("GET", "http://api.zippopotam.us/us/"+$(this).val(), true);
				client.onreadystatechange = function() {
					if(client.readyState == 4) {
						//alert($.paseXML(client.responseText));
						var response = eval ("(" + client.responseText + ")");
						//response.places[0].state
						//alert(response.places[0]['place name']);
						$('#' + id + '_state').val(response.places[0].state);//state
						$('#' + id + '_city').val(response.places[0]['place name']);//city
						$('#' + id + '_country').val(response.country);//country
					};
				};

				client.send();
			});	
			$(document).on('click touchstart', '.comment_item_button', function() {
				var e = $(this);
				var id = e.attr('id');
				//alert(id.split('_')[1]);
				var row = id.split('_')[1];
				//
				$('#itemNote_'+row).show();
			});			
			// 
			$(document).on('change', '.selectedItems', function() {
				var e = $(this);
				var row = e[0].id.split('-')[1];
				//alert(rowid);
				var id = e.val();
				//alert(e.val());
				//alert('#autocompletevalitem_'+row);
				var text = $( "#" + e[0].id + " option:selected" ).text();
				//alert($( "#" + e[0].id + " option:selected" ).text());
				//$('#item_'+rowid).val($( "#" + e[0].id + " option:selected" ).text());
				$('#entry'+row+' .input_h').val(id);
				$('#entry'+row+' .input_fn').val(text);
			});	   
			// 
			$("#shippingcompany").change(function() {
				//alert($(this).val());
				var $parentform = $(this).closest("form");
				loadShippingMethods($(this), $parentform.find('.shipping_method_select2_single'), "");
			});
			//
			$("#c1_shippingcompany").change(function() {
				//alert($(this).val());
				loadShippingMethods($(this), $('#add-customer-form #defaultshippingmethod'), "");
			});
			//
			$("#o-add-location-form  #lshippingcompany").change(function() {
				//alert($(this).val());
				loadShippingMethods($(this), $('#o-add-location-form #lshippingmethod'), "");
			});
			//
			$("#c2_shippingcompany").change(function() {
				//alert($(this).val());
				loadShippingMethods($(this), $('#add-customer-form #secondaryshippingmethod'), "");
			});
			//
			$(document).ready(function () {
			     $('[data-toggle="btn-input"]').each(function () {
			         var $this = $(this);
			         var $input = $($this.data('target'));
			         var name = $this.data('name');
			         var active = false; // Maybe check button state instead
			         $this.on('click', function () {
			             active = !active;

			             if (active) $input.attr('name', name).val($this.val());
			             else $input.removeAttr('name').removeAttr('value');

			             $this.button('toggle');
			         });
			     });
			 });
			//
			$('body').on('click' , '[rel="popover"]' , function(e){
				e.stopPropagation();

				var i = $(this);
				var thisPopover = $('.popoverClose').filter('[data-info-id="' +i.data('info-id')+ '"]').closest('.popover');        
				if( thisPopover.is(':visible') ){
					$('.popover').remove();
				}
				else{
					$(this).popover('show');
				}
			});
        </script>
        <!-- select2 -->
        <script>
            $(document).ready(function () {
                $(".state_location_select2_single").select2({
                    placeholder: "Choose a State",
					width: '100%',
                    allowClear: true
                });					
                $(".receiving_location_select2_single").select2({
                    placeholder: "Choose a Receiving Location",
					width: '100%',
                    allowClear: true
                });	
                $(".select2_option").select2({
                    placeholder: "Select An Option",
					width: '100%',
                    allowClear: true
                });				
                $(".select2_vendor").select2({
                    placeholder: "Select Vendor",
					width: '100%',
                    allowClear: true
                });
                $(".select2_partnumber_type").select2({
                    placeholder: "Select Type",
					width: '100%',
                    allowClear: true
                });				
                $(".select2_config_option").select2({
                    placeholder: "Select An Existing Configuration",
					width: '100%',
                    allowClear: true
                });				
                $(".manufacturer_select2_single").select2({
                    placeholder: "Select Manufacturer",
					width: '100%',
                    allowClear: true
                });      
                $(".category_select2_single").select2({
                    placeholder: "Select Category",
					width: '100%',
                    allowClear: true
                });   
                $(".department_select2_single").select2({
                    placeholder: "Select Department",
					width: '100%',
                    allowClear: true
                });                                        
                $(".select2_user").select2({
                    placeholder: "Select User",
					width: '100%',
                    allowClear: true
                });				
               $(".select2_single").select2({
                    placeholder: "Select an element",
                    allowClear: true
                });
               $(".default_select2_single").select2({
                    placeholder: "Choose a location",
					width: '100%',
                    allowClear: true
                });
               $(".default_parent_location_select2").select2({
                    placeholder: "Choose a parent location (Optional)",
					width: '100%',
                    allowClear: true
                });
               $(".store_select2_single").select2({
                    placeholder: "Returned From",
					width: '100%',
                    allowClear: true
                });				
               $(".shipping_select2_single").select2({
                    placeholder: "Shipping Location",
					width: '100%',
                    allowClear: true
                });	
               $(".default_shipping_select2_single").select2({
                    placeholder: "Default Shipping Location",
					width: '100%',
                    allowClear: true
                });	
               $(".receiving_select2_single").select2({
                    placeholder: "Receiving Location",
					width: '100%',
                    allowClear: true
                });				
               $(".select2_purchase").select2({
                    placeholder: "Select Existing Purchase Order",
					width: '100%',
                    allowClear: true
                });				
               $(".shipping_company_select2_single").select2({
                    placeholder: "Shipping Company",
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    allowClear: true
                });	
               $(".shipping_method_select2_single").select2({
                    placeholder: "Shipping Method",
					width: '100%',
                    allowClear: true
                });				
               $(".item_select2_single").select2({
                    placeholder: "Select An Item",
					width: '100%',
                    allowClear: true
                });					
				//customer page
				$("#c1_shippingcompany").select2({
					placeholder: "Customer Shipping Company",
					width: '100%',
					allowClear: true
				});	
				$("#c2_shippingcompany").select2({
					placeholder: "Other Shipping Company",
					width: '100%',
					allowClear: true
				});	
				$("#defaultshippingmethod").select2({
					placeholder: "Customer Shipping Method",
					width: '100%',
					allowClear: true
				});
				$("#secondaryshippingmethod").select2({
					placeholder: "Other Shipping Method",
					width: '100%',
					allowClear: true
				});
				$(".parent_child_location").select2({
					placeholder: "View By Location",
					width: '25%',
					allowClear: true
				});	
				$(".default_select_folder").select2({
					placeholder: "Select Folder",
					width: '100%',
					allowClear: true
				});				
				$("#paymentterms").select2({
					placeholder: "Choose Payment terms",
					width: '100%',
					allowClear: true
				});					
				//
				$('#selectCustomers').SumoSelect({placeholder: 'Select all customers for this user'});
				$('#selectLocations').SumoSelect({placeholder: 'Select all locations for this parent name'});
            });
        </script>
    <!-- End Script Loading -->
		<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/custom-datepicker.js"></script>
		<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/uc/canvas-define.js"></script>
    <!-- worldmap -->
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/maps/jquery-jvectormap-2.0.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/maps/gdp-data.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/maps/jquery-jvectormap-world-mill-en.js"></script>
    <script type="text/javascript" src="<?php echo Yii::$app->request->baseUrl;?>/public/js/maps/jquery-jvectormap-us-aea-en.js"></script>
    <script>
	$(function () {
            $('#world-map-gdp').vectorMap({
                map: 'world_mill_en',
                backgroundColor: 'transparent',
                zoomOnScroll: false,
                series: {
                    regions: [{
                        values: gdpData,
                        scale: ['#E6F2F0', '#149B7E'],
                        normalizeFunction: 'polynomial'
                    }]
                },
                onRegionTipShow: function (e, el, code) {
                    el.html(el.html() + ' (GDP - ' + gdpData[code] + ')');
                }
            });
        });
    </script>
    <!-- skycons -->
    <script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/skycons/skycons.js"></script>
    <script>
        var icons = new Skycons({
                "color": "#73879C"
            }),
            list = [
                "clear-day", "clear-night", "partly-cloudy-day",
                "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
                "fog"
            ],
            i;

        for (i = list.length; i--;)
            icons.set(list[i], list[i]);

        icons.play();
    </script>

    <!-- dashbord linegraph -->
    <script>
        var doughnutData = [
            {
                value: 30,
                color: "#455C73"
            },
            {
                value: 30,
                color: "#9B59B6"
            },
            {
                value: 60,
                color: "#BDC3C7"
            },
            {
                value: 100,
                color: "#26B99A"
            },
            {
                value: 120,
                color: "#3498DB"
            }
    ];
        var myDoughnut = new Chart(document.getElementById("canvas1").getContext("2d")).Doughnut(doughnutData);
    </script>
    <!-- /dashbord linegraph -->
    <!-- datepicker -->
    <script type="text/javascript">
        $(document).ready(function () {

            var cb = function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
            }

            var optionSet1 = {
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2015',
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'left',
                buttonClasses: ['btn btn-default'],
                applyClass: 'btn-small btn-primary',
                cancelClass: 'btn-small',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                locale: {
                    applyLabel: 'Submit',
                    cancelLabel: 'Clear',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    firstDay: 1
                }
            };
            $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
            $('#reportrange').daterangepicker(optionSet1, cb);
            $('#reportrange').on('show.daterangepicker', function () {
                console.log("show event fired");
            });
            $('#reportrange').on('hide.daterangepicker', function () {
                console.log("hide event fired");
            });
            $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
            });
            $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
                console.log("cancel event fired");
            });
            $('#options1').click(function () {
                $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
            });
            $('#options2').click(function () {
                $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
            });
            $('#destroy').click(function () {
                $('#reportrange').data('daterangepicker').remove();
            });
        });
    </script>
    <script>
        NProgress.done();
    </script>
    <!-- /datepicker -->
    <!-- /footer content -->
		<?php $this->endBody() ?>
	</body>
</html>
<?php //$this->endPage() ?>