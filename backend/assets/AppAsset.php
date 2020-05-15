<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/style.css',
        'css/font-awesome/css/font-awesome.css',
    ];

    public $js = [
        'libs/clipboard/dist/clipboard.min.js',
        'libs/jquery-sortable/jquery-sortable.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',

        'kartik\select2\Select2Asset',
        'kartik\select2\ThemeKrajeeAsset',
        'kartik\base\WidgetAsset',
    ];
}