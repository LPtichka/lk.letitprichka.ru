<?php

use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$log    = require __DIR__ . '/logs.php';
$db     = array_merge(
    require(__DIR__ . '/db.php'),
    require(__DIR__ . '/db-local.php')
);

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
            'class'     => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'smtp.yandex.ru',
                'username'   => 'support@letitptichka.ru',
                'password'   => 'srs666tt',
                'port'       => '465',
                'encryption' => 'SSL',
            ],
        ],
        'log'          => $log,
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
                        'app' => 'app.php',
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
                '<module:api>/<controller>s/'                                                           => '<module>/<controller>/index',
                '<module:api>/<controller>/<action>'                                                    => '<module>/<controller>/<action>',
                '<module:api>/<controller>/<action:view|update|set-status|set-payment-method>/<id:\d+>' => '<module>/<controller>/<action>',
                '/'                                                                                     => 'main/index',
                '/login'                                                                                => 'main/login',
                '/forgot-password'                                                                      => 'main/forgot-password',
                '/reset-password'                                                                       => 'main/reset-password',
                '<action:\w+[^s]$>'                                                                     => 'main/<action>',
                '<controller>s/'                                                                        => '<controller>/index',
                '<controller>/<id:\d+>/<action>/<status:\w+>'                                           => '<controller>/<action>',
                '<controller>/<id:\d+>/<action>'                                                        => '<controller>/<action>',
                '<controller>/<id:\d+>'                                                                 => '<controller>/view',
                '<controller>/<action>'                                                                 => '<controller>/<action>',
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
        'allowedIPs' => ['127.0.0.1', '*'],
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
