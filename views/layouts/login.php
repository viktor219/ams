<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?> 
<!DOCTYPE html>
<html lang="en">
	<head>
	        <meta charset="utf-8"> 
	        <meta http-equiv="X-UA-Compatible" content="IE=edge">
	        <meta name="viewport" content="width=device-width, initial-scale=1">
	        <meta name="description" content="">
	        <meta name="author" content="Matthew Ebersole, Asset Enterprises Inc.">
	        <link rel="icon" href="favicon.ico">
	        <title><?= Html::encode($this->title) ?></title>
		<!-- Bootstrap core CSS -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/bootstrap.min.css" rel="stylesheet">
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/login.css" rel="stylesheet">
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/g-checkbox.css" rel="stylesheet">
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/css/normalize.css" rel="stylesheet">
		<!-- Fonts -->
			<link href="<?php echo Yii::$app->request->baseUrl;?>/public/fonts/css/font-awesome.min.css" rel="stylesheet">
	</head>
	<body style="background:#0361A7;">
		<?php $this->beginBody() ?>
		    <?= $content ?>
		<?php $this->endBody() ?>
		<script src="<?php echo Yii::$app->request->baseUrl;?>/public/js/bootstrap.min.js"></script>
	</body>
</html>
<?php $this->endPage() ?>