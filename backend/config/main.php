<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'thousands-tests-backend',
    'name' => 'thousands-tests',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'site/index',
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    'bootstrap' => ['debug', 'log'],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => [$_SERVER['REMOTE_ADDR']],
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module'
        ],
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
        ],
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
            'enableAutoLogin' => true,
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
		'rbacManager' => [
			'class' => 'backend\components\own\rbac\AdmAccess',
		],
//        'rbacManager' => [
//            'class' => 'rbacManager\component\AdmAccess',
//            'defaultAllowControllers' => ['site'], //Контроллеры доступные всем(авторизация, ошибки, etc)
//            'rulesField' => 'rules', //Название поля в базе где хранится сер. массив с правами
//            'rootField' => 'is_root', //Название булевого поля в базе соответствия идентификатора руту
//            'controllerDir' => ROOT_PATH . '/controllers/',
//            'controllerNameSpace' => 'backend\controllers'
//        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'UA',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['user'],
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                    'logFile' => '@app/runtime/users/registration.log',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
