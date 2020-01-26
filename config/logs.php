<?php

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets'    => [
        [
            'class'  => 'yii\log\FileTarget',
            'levels' => ['error', 'warning'],
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/payment.log',
            'levels'      => ['info'],
            'categories'  => ['payment-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/product.log',
            'levels'      => ['info'],
            'categories'  => ['product-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/user.log',
            'levels'      => ['info'],
            'categories'  => ['user-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/exception.log',
            'levels'      => ['info'],
            'categories'  => ['exception-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/dish.log',
            'levels'      => ['info'],
            'categories'  => ['dish-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/customer.log',
            'levels'      => ['info'],
            'categories'  => ['customer-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/address.log',
            'levels'      => ['info'],
            'categories'  => ['address-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/subscription.log',
            'levels'      => ['info'],
            'categories'  => ['subscriptions-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
        [
            'class'       => 'yii\log\FileTarget',
            'logFile'     => '@runtime/logs/franchise.log',
            'levels'      => ['info'],
            'categories'  => ['franchise-*'],
            'logVars'     => [],
            'maxFileSize' => 102400,
            'maxLogFiles' => 10,
        ],
    ],
];
