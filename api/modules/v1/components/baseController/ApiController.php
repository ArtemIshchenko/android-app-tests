<?php
namespace api\modules\v1\components\baseController;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;


/**
 * Class ApiController
 * @package api\modules\v1\components\baseController
 * @author alhambr
 */
class ApiController extends Controller
{

    const API_VERSION = 1.0;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                //авторизированы
                [
                    'allow' => true,
	                //'actions' => ['*'],
                    'roles' => ['@'],
                ],
                //гости
                [
                    'allow' => true,
	                //'actions' => ['*'],
                    'roles' => ['?'],
                ],
            ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	public function beforeAction($action)
	{
		\Yii::$app->request->enableCsrfValidation = false;
		\Yii::$app->user->enableSession = false;
		if(!parent::beforeAction($action)){
			return false;
		}
		\Yii::$app->request->enableCsrfValidation = false;
		\Yii::$app->user->enableSession = false;
		return true;
	}

//    public function afterAction($action, $result)
//    {
////	    if($action->id != 'platform') {
//		    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
////	    }
//	    return parent::afterAction($action, $result);
//    }

}