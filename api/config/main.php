<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'thousands-tests-api',
    'name' => 'thousands-tests',
    'basePath' => dirname(__DIR__),
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'controllerNamespace' => 'api\controllers',
    'defaultRoute' => 'site/index',
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
//        'urlManager' => [
//            'class' => 'yii\web\UrlManager',
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//            'rules' =>
//                [
//                    's/<hash>' => 'short-url/go',
//                    '<controller:\w+>/<id:\d+>' => '<controller>/index',
//                    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
//                    '<controller:\w+>/<action:\w+>/<name:\w+>' => '<controller>/<action>',
//                    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
//                ]
//        ],
        'user' => [
            'identityClass' => 'backend\models\db\adm\Adm',
            //'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendUser',
            ]
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => sys_get_temp_dir(),
        ],
        'request' => [
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'cookieValidationKey' => 'jFAmctJh6JcAUbAAvd2uniffSzBEpm0x',
            'csrfParam' => '_backendCSRF',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
