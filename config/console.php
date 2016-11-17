<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'name' => 'Asset Management System',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        'RocketShipIt' => '@vendor/RocketShipIt/RocketShipIt',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'mailer' => [
	        'class' => 'yii\swiftmailer\Mailer',
	        //'viewPath' => '@app/views/mail/templates',
	        //'useFileTransport' => false,
	        'transport' => [
	            'class' => 'Swift_SmtpTransport',
	            'host' => 'web.assetenterprises.com',
	            'username' => 'matt.ebersole@assetenterprises.com',
	            'password' => 'maxima251',
	            'port' => '465',
	            'encryption' => 'ssl',
	        ],
        ],
         'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'baseUrl' => 'http://assetenterprises.com/testing/live/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ]
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}
error_reporting(E_ERROR);
return $config;
