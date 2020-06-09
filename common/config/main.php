<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true,
            'servers'=>[
                ['host' => 'localhost', 'port' => 11211, 'weight' => 60],
            ],
            'keyPrefix' => 'tt3'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['android-test'],
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                    'logFile' => '@app/runtime/android-test/log.log',
                    'logVars' => [],
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                ],
            ],
        ],
    ],
];
