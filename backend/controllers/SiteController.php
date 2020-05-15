<?php
namespace backend\controllers;


use backend\models\form\adm\AdmLoginForm;
use backend\components\own\baseController\BackController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

/**
 * Class SiteController
 * @property-description Главный контроллер
 * @package backend\controllers
 * @author asisch
 */
class SiteController extends BackController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTest() {
        return $this->render('test');
    }

    public function actionLogin()
    {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdmLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        $name = 'Error: ' . $exception->getCode();
        return $this->render('error', ['exception' => $exception, 'name' => $name]);
    }

}
