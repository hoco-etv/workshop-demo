<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
  'id' => 'basic',
  'name' => 'Klushok',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'l0LZvRZyqKqTwWqSNLJ55pS3t-ra2Fjb',
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
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => (YII_ENV === 'local'),
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'dutmail.tudelft.nl', 
                // 'username' => 'username',
                // 'password' => 'password',
                // 'port' => '587', // Port 25 is a very common port too
                // 'encryption' => 'tls', // It is often used, check your provider or mail server specs
            ]
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
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // '<alias:\w+>' => 'site/<alias>',
				// ['class' => 'maerduq\usm\components\RedirectRule']
            ],
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'EUR',
       ],
    ],
    'params' => $params,
    'modules' => [
        'gridview' =>  [
             'class' => '\kartik\grid\Module'
             // enter optional module parameters below - only if you need to  
             // use your own export download action or custom translation 
             // message source
             // 'downloadAction' => 'gridview/export/download',
             // 'i18n' => []
         ],
         'markdown' => [
            // the module class
            'class' => 'kartik\markdown\Module',
            
            // the controller action route used for markdown editor preview
            'previewAction' => '/markdown/parse/preview',
            
            // the controller action route used for downloading the markdown exported file
            'downloadAction' => '/markdown/parse/download',
            
            // the list of custom conversion patterns for post processing
            'customConversion' => [
                '<table>' => '<table class="table table-bordered table-striped">'
            ],
            
            // whether to use PHP SmartyPantsTypographer to process Markdown output
            'smartyPants' => true,
            
            // array the the internalization configuration for this module
            'i18n' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/kartik-V/yii2-markdown/src//messages',
                'forceTranslation' => true
            ],        
        ],

		'usm' => [
			'class' => 'maerduq\usm\UsmModule',
			'access_type' => 'yii',
			'layout_container' => '@app/views/layouts/main',
			'plugins' => [
                '/admin',
                '/maillist/admin'
			]
        ],
        'maillist' => [
            'class' => 'app\modules\maillist\MaillistModule',
        ],
    ]
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
    ];
    $config['components']['assetManager']['forceCopy'] = true;
}

return $config;
