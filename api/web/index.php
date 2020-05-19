<?php
header('Content-type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.UTF8');
ini_set('display_errors', 1);
error_reporting(E_ALL | E_DEPRECATED);
date_default_timezone_set('Europe/Kiev');
mb_internal_encoding('UTF-8');

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__).'/..');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();
