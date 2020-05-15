<?php

namespace backend\components\own\baseController;

use backend\models\db\adm\Adm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class BackController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => Yii::$app->rbacManager->getAllowActions(),
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
    	if (!parent::beforeAction($action)) {
            return false;
        }
        //Апдейтим запись последней активности пользователя
        Adm::upLastActive();
        return true;
    }

}