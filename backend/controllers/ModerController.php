<?php

namespace backend\controllers;

use backend\components\own\baseController\BackController;
use backend\models\db\adm\Adm;

use librariesHelpers\helpers\Type\Type_Cast;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * Class ModerController
 * @property-description  Управление администраторами
 * @package backend\controllers
 * @author alhambr
 */
class ModerController extends BackController
{

    /**
     * @property-description Просмотр всех администраторов системы
     */
    public function actionIndex()
    {
        $model = new Adm();
        $dataProvider = $model->search(Yii::$app->request->get());
        return $this->render("index", ['model' => $model, 'dataProvider' => $dataProvider]);
    }

    /**
     * @property-description Добавление нового администратора системы
     */
    public function actionAdd()
    {
        $model = new Adm();
        $model->scenario = 'add';
        if (isset($_POST) && !empty($_POST)) {
            $data = $_POST['Adm'];
            $data['rules'] = Adm::serializeRules($data['rules']);
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Yii::$app->security->generatePasswordHash($data["password"]);
            }
            $_POST['Adm'] = $data;
        }
        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::toRoute(['moder/index']));
        } else {
            $classList = Yii::$app->rbacManager->getControllerActions();
            return $this->render('add', [
                'model' => $model,
                'classList' => $classList
            ]);
        }
    }

    /**
     * @property-description Редактирование администратора системы
     */
    public function actionUpdate($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = Adm::findOne($id);
        $oldAttributes = $model->attributes;
        $model->scenario = 'update';
        if (isset($_POST) && !empty($_POST)) {
            $data = $_POST['Adm'];
            if (!empty($data['rules'])) {
                $data['rules'] = Adm::serializeRules($data['rules']);
            }
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Yii::$app->security->generatePasswordHash($data["password"]);
            }
            if (isset($data['password']) && empty($data['password'])) {
                $data['password'] = $model->password;
            }
            $_POST['Adm'] = $data;
        }
        if ($model->load($_POST) && $model->save()) {
//            LogRecord::logger(LogRecord::ACTION_UPDATE, Adm::className(), $model->id, $oldAttributes, $model->attributes);
            return $this->redirect(Url::toRoute(['moder/index']));
        } else {
            $classList = Yii::$app->rbacManager->getControllerActions();
            $userRules = Adm::unserializeRules($model->rules);
            foreach ($classList as $className) {
                if (isset($className['method'])) {
                    foreach ($className["method"] as $key => $method) {
                        $name = mb_strtoupper($className["className"][0] . '_' . $method);
                        if (!empty($userRules[$name])) {
                            $rules[$name] = $userRules[$name];
                        } else {
                            $rules[$name] = 0;
                        }
                    }
                }
            }
            $model->rules = $rules;
            return $this->render('update', [
                'model' => $model,
                'classList' => $classList
            ]);
        }
    }

    /**
     * @property-description Просмотр данных администратора
     * @param integer $id
     * @return string
     * @throws HttpException
     */
    public function actionView($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = Adm::findOne($id);
        if (is_null($model)) {
            throw new HttpException(404, 'Данная страница не существует');
        }
        $classList = Yii::$app->rbacManager->getControllerActions();
        $userRules = Adm::unserializeRules($model->rules);
        $rules = [];

        foreach ($classList as $className) {
            if (isset($className['method'])) {
                foreach ($className["method"] as $key => $method) {
                    $name = mb_strtoupper($className["className"][0] . '_' . $method);
                    if (!empty($userRules[$name])) {
                        $rules[$name] = $userRules[$name];
                    } else {
                        $rules[$name] = 0;
                    }
                }
            }
        }
//        $modelLog = LogRecord::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC]);
//        $dataProvider = new ActiveDataProvider([
//            'query' => $modelLog,
//            'pagination' => [
//                'pageSize' => 20,
//            ],
//        ]);
        return $this->render('view', ['model' => $model, 'classList' => $classList, 'rules' => $rules/*, 'modelLog' => $modelLog, 'dataProvider' => $dataProvider*/]);
    }

    /**
     * @property-description Удаление администратора
     * @param integer $id
     * @throws HttpException
     */
    public function actionDelete($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = Adm::findOne($id);
        $oldAttributes = $model->attributes;
        if (is_null($model)) {
            throw new HttpException(404, 'Данный администратор не существует');
        }
        $model->is_deleted = 1;
        $model->save();
//        LogRecord::logger(LogRecord::ACTION_DELETE, Adm::className(), $model->id, $oldAttributes, $model->attributes);
        if (Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
            $json = ['result' => 'successful'];
            print Json::encode($json);
            Yii::$app->end();
        }
        $this->redirect(Yii::$app->request->referrer);
    }


}