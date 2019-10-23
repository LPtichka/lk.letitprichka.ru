<?php

use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id'             => 'basic',
    'basePath'       => dirname(__DIR__),
    'bootstrap'      => ['log'],
    'defaultRoute'   => 'main/index',
    'sourceLanguage' => 'en-US',
    'language'       => 'ru-RU',
    'name'           => 'LetitPtichka.ru',
    'aliases'        => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components'     => [
        'request'      => [
            'cookieValidationKey' => 'J0aidciAkL7ODYKG4gxbjYNAK8z6xT0O',
            'parsers'             => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'         => [
            'identityClass'   => 'app\models\User',
            'loginUrl'        => ['main/login'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer'       => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class'      => 'yii\log\FileTarget',
                    'logFile'    => '@runtime/logs/payment.log',
                    'levels'     => ['info'],
                    'categories' => ['payment-*'],
                    'logVars'    => [],
                    'maxFileSize' => 102400,
                    'maxLogFiles' => 10,
                ],
                [
                    'class'      => 'yii\log\FileTarget',
                    'logFile'    => '@runtime/logs/product.log',
                    'levels'     => ['info'],
                    'categories' => ['product-*'],
                    'logVars'    => [],
                    'maxFileSize' => 102400,
                    'maxLogFiles' => 10,
                ],
            ],
        ],
        'authManager'  => [
            'class' => 'yii\rbac\DbManager',
            'cache' => [
                'class' => 'yii\caching\FileCache'
            ],
        ],
        'db'           => $db,
        'formatter'    => [
            'class'                  => 'yii\i18n\Formatter',
            'nullDisplay'            => '',
            'locale'                 => 'ru-RU',
            'timeZone'               => 'Europe/Moscow',
            'currencyCode'           => 'RUB',
            'numberFormatterSymbols' => [
                NumberFormatter::CURRENCY_SYMBOL => '₽',
            ],
        ],
        'i18n'         => [
            'translations' => [
                '*' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'fileMap'  => [
                        'app'   => 'app.php',
                    ],
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'urlManager'   => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'baseUrl'             => '/',
            'normalizer'          => [
                'class'  => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
            ],
            'rules'               => [
                '/'                                           => 'main/index',
                '/login'                                      => 'main/login',
                '/forgot-password'                            => 'main/forgot-password',
                '/reset-password'                             => 'main/reset-password',
                '<action:\w+[^s]$>'                           => 'main/<action>',
                '<controller>s/'                              => '<controller>/index',
                '<controller>/<id:\d+>/<action>/<status:\w+>' => '<controller>/<action>',
                '<controller>/<id:\d+>/<action>'              => '<controller>/<action>',
                '<controller>/<id:\d+>'                       => '<controller>/view',
                '<controller>/<action>'                       => '<controller>/<action>',
            ],
        ],
    ],
    'as access'      => [
        'class'        => 'app\rbac\AccessControl',
        'allowActions' => [
            'main/login',
            'main/signup',
            'main/forgot-password',
            'main/reset-password',
            'gii/*',
            'debug/*',
        ]
    ],
    'modules'        => [
        'api' => [
            'class' => 'app\api\Module',
        ],
    ],
    'params'         => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
