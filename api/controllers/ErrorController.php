<?php
namespace api\controllers;

use yii\helpers\Json;
use yii\web\Controller;

/**
 * Class ErrorController
 * @package api\controllers
 * @author alhambr
 */
class ErrorController extends Controller
{

    public function actionIndex()
    {
        $status = \Yii::$app->errorHandler->exception->statusCode;
        $message = [
            'code'=>$status,
            'message'=>\Yii::$app->errorHandler->exception->getMessage()
        ];
        print Json::encode($message);
        \Yii::$app->end();
    }

}