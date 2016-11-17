<?php

$params = require(__DIR__ . '/params.php');

use \yii\web\Request;
use kartik\mpdf\Pdf;

$baseUrl = str_replace('/asset_managment/web', '', (new Request)->getBaseUrl());
//

$config = [
    'id' => 'basic',
    'name' => 'Asset Management System',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'/*, 'Application'*/],
    //overwrite controller IDs
	'controllerMap' => [
		'overview' => 'app\controllers\SiteController',
	],
	'aliases' => [
		'RocketShipIt' => '@vendor/RocketShipIt/RocketShipIt',
                'Firebase' => '@vendor/Firebase',
	],
	'modules' => [ 
        'orders' => [
            'class' => 'app\modules\Orders\Module',
        ],
		'inventory' => [
			'class' => 'app\modules\Inventory\Module', 
		],
		'users' => [
			'class' => 'app\modules\Users\Module',
		],
		'customers' => [
			'class' => 'app\modules\Customers\Module',
		],
		'analytics' => [
			'class' => 'app\modules\Analytics\Module',
		],
        'shipping' => [
			'class' => 'app\modules\Shipping\Module',
		],
		'location' => [
			'class' => 'app\modules\Location\Module',
		],		
		'purchasing' => [
			'class' => 'app\modules\Purchasing\Module',
		],
		'receiving' => [
			'class' => 'app\modules\Receiving\Module',
		],
		'inprogress' => [
			'class' => 'app\modules\Inprogress\Module',
		],
		'billing' => [
			'class' => 'app\modules\Billing\Module',
		],
		'vendor' => [
			'class' => 'app\modules\Vendor\Module',
		],	
		'gimp' => [
			'class' => 'app\modules\GImport\Module',
		],	
		'training' => [
			'class' => 'app\modules\Training\Module',
		],	
		'api' => [
			'class' => 'app\modules\Api\Module',
		],		
	], 
    'components' => [
         'common' => [
            'class' => 'app\components\Common',
            ],
		/*'assetManager' => [
			'bundles' => [
				'yii\web\JqueryAsset' => [
					'sourcePath' => null,
					'basePath' => '@webroot',
					'baseUrl' => '@web',
					'js' => [
						'public/js/jquery.min.js',
						'//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js',
					]
				],
			],
		],
	   /* 'Application'=>[
	    		'class'=>'app\components\Application'
	    ],*/
		//geolocation component 
        /*'geoip' => [
		   'class' => 'dpodium\yii2\geoip\components\CGeoIP',
		   'mode' => 'STANDARD',  // Choose MEMORY_CACHE or STANDARD mode
        ],*/
		// setup Krajee Pdf component
		'pdf' => [
			'class' => Pdf::classname(),
	        // set to use core fonts only
	        'mode' => Pdf::MODE_CORE, 
	        // A4 paper format
	        'format' => Pdf::FORMAT_A4, 
	        // portrait orientation
	        'orientation' => Pdf::ORIENT_PORTRAIT, 
	        // stream to browser inline
	        'destination' => Pdf::DEST_BROWSER, 
	        // format content from your own css file if needed or use the
	        // enhanced bootstrap css built by Krajee for mPDF formatting
	        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
	
		],
		//mobile detection component...
		'mobileDetect' => [
			'class' => 'app\vendor\mobiledetect\MobileDetect'
		],
        'request' => [
        	'baseUrl' => $baseUrl,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '#@HJsftA457# ##',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
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
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'baseUrl' => $baseUrl,
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
			'rules' => [
				//main custom rules
				'login' => 'site/login',
				'logout' => 'site/logout',
				//modules custom rules
				'<module:orders>/<action:\w+>' => '<module>/default/<action>',
				'<module:orders>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:gimp>/<action:\w+>' => '<module>/default/<action>',
				'<module:gimp>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:analytics>/<action:\w+>' => '<module>/default/<action>',
				'<module:analytics>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:customers>/<action:\w+>' => '<module>/default/<action>',
				'<module:customers>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:inventory>/<action:\w+>' => '<module>/default/<action>',
				'<module:inventory>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:location>/<action:\w+>' => '<module>/default/<action>',
				'<module:location>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',				
				'<module:users>/<action:\w+>' => '<module>/default/<action>',
				'<module:users>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:purchasing>/<action:\w+>' => '<module>/default/<action>',
				'<module:purchasing>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:receiving>/<action:\w+>' => '<module>/default/<action>',
				'<module:receiving>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:inprogress>/<action:\w+>' => '<module>/default/<action>',
				'<module:inprogress>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:shipping>/<action:\w+>' => '<module>/default/<action>',
				'<module:shipping>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:billing>/<action:\w+>' => '<module>/default/<action>',
				'<module:billing>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',
				'<module:vendor>/<action:\w+>' => '<module>/default/<action>',
				'<module:vendor>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',			
				'<module:training>/<action:\w+>' => '<module>/default/<action>',
				'<module:training>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',	
				'<module:api>/<action:\w+>' => '<module>/default/<action>',
				'<module:api>/<action:\w+>/<id:\d+>' => '<module>/default/<action>',				
			]
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '41.85.189.*', '41.138.89.*'],
    ];
}

//var_dump($config['modules']);

return $config;
