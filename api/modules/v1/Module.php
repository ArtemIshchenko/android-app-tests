<?php
namespace api\modules\v1;

/**
 * Class Module
 * @package api\modules\v1
 * @author alhambr
 */
class Module extends \yii\base\Module
{

    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init()
    {
        parent::init();
        \Yii::configure($this, require(__DIR__ . '/config/main.php'));
    }

}