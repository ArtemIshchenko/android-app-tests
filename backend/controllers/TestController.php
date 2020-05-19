<?php
namespace backend\controllers;

use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\SettingRecord;
use backend\components\own\baseController\BackController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\base\Model;

/**
 * Class TestController
 * @property-description Модуль тестов
 * @package backend\controllers
 * @author asisch
 */
class TestController extends BackController
{

    /**
     * @property-description Тесты
     */
    public function actionTests() {
        $model = new TestRecord();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('tests', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание теста
     * @return string
     */
    public function actionTestAdd() {
        $model = new TestRecord();
        $model->setScenario('add');

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/tests']));
            } else {
                $model->convReverse();
            }
        }

        return $this->render('test-add', ['model' => $model]);
    }

    /**
     * @property-description Редактирование теста
     * @param int $id
     * @return string
     * @throws HttpException
     */
    public function actionTestUpdate($id) {
        $model = TestRecord::findOne($id);
        $model->convReverse();
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Тест не найден", 404);
        }
        $model->setScenario('update');
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/tests']));
            } else {
                $model->convReverse();
            }
        }

        return $this->render('test-update', ['model' => $model]);
    }

    /**
     * @property-description Активация/Деактивация теста
     * @param integer $id
     */
    public function actionTestChangeActive($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = TestRecord::findOne($id);
        if(!is_null($model) && !empty($model)) {
            $model->setScenario('change-active');
            switch($model->is_active) {
                case TestRecord::IS_NOT_ACTIVE:
                    $model->is_active = TestRecord::IS_ACTIVE;
                    break;
                case TestRecord::IS_ACTIVE:
                    $model->is_active = TestRecord::IS_NOT_ACTIVE;
                    break;
            }
            $model->save();
        }
        $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @property-description Настройки
     * @return string
     * @throws HttpException
     */
    public function actionSetting() {
        $settings = SettingRecord::getSettings();
        if (Model::loadMultiple($settings, \Yii::$app->request->post()) && Model::validateMultiple($settings)) {
            $result = false;
            $i = 0;
            foreach ($settings as $setting) {
                if ($i == 0) {
                    $result = $setting->save(false);
                } else {
                    $result &= $setting->save(false);
                }
                ++$i;
            }
            if ($result) {
                $this->redirect(Url::toRoute(['test/index']));
            }
        }
        return $this->render('setting', [
            'settings' => $settings,
        ]);
    }

    /**
     * @property-description Диплинки
     */
    public function actionIndex() {
        $model = new DeeplinkRecord();
        $dataProvider = $model->search(\Yii::$app->request->get());
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание диплинка
     * @return string
     */
    public function actionDeeplinkAdd() {
        $model = new DeeplinkRecord();
        $model->setScenario('add');
        $model->is_active = DeeplinkRecord::IS_ACTIVE;
        $model->mode = DeeplinkRecord::MODE['warming'];

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['test/index']));
        }

        return $this->render('deeplink-add', ['model' => $model]);
    }

    /**
     * @property-description Редактирование диплинка
     * @param int $id
     * @return string
     * @throws HttpException
     */
    public function actionDeeplinkUpdate($id) {
        $model = DeeplinkRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Диплинк не найден", 404);
        }
        $model->setScenario('update');
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['test/index']));
        }

        return $this->render('deeplink-update', ['model' => $model]);
    }

    /**
     * @property-description Активация/Деактивация диплинка
     * @param integer $id
     */
    public function actionDeeplinkChangeActive($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = DeeplinkRecord::findOne($id);
        if(!is_null($model) && !empty($model)) {
            $model->setScenario('change-active');
            switch($model->is_active) {
                case DeeplinkRecord::IS_NOT_ACTIVE:
                    $model->is_active = DeeplinkRecord::IS_ACTIVE;
                    break;
                case DeeplinkRecord::IS_ACTIVE:
                    $model->is_active = DeeplinkRecord::IS_NOT_ACTIVE;
                    break;
            }
            $model->save();
        }
        $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionTest() {

    }
}