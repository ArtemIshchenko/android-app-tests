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
     * @param integer $type
     */
    public function actionTests($type = 0) {
        $params = [];
        switch($type) {
            case 1:
                $params = ['is_active' => TestRecord::IS_ACTIVE];
                break;
            case 2:
                $params = ['is_active' => TestRecord::IS_NOT_ACTIVE];
                break;
        }
        $model = new TestRecord();
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('tests', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание теста
     * @param integer $type
     * @return string
     */
    public function actionTestAdd($type = 0) {
        $model = new TestRecord();
        $model->setScenario('add');
        switch($type) {
            case 1:
            default:
                $model->is_active = TestRecord::IS_ACTIVE;
                break;
            case 2:
                $model->is_active = TestRecord::IS_NOT_ACTIVE;
                break;
        }

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/tests', 'type' => $type]));
            }
        }

        return $this->render('test-add', ['model' => $model]);
    }

    /**
     * @property-description Редактирование теста
     * @param int $id
     * @param integer $type
     * @return string
     * @throws HttpException
     */
    public function actionTestUpdate($id, $type = 0) {
        $model = TestRecord::findOne($id);
        $model->convReverse();
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Тест не найден", 404);
        }
        $model->setScenario('update');
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/tests', 'type' => $type]));
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
     * @param integer $type
     */
    public function actionIndex($type = 0) {
        $params = [];
        switch($type) {
            case 1:
                $params = ['is_active' => DeeplinkRecord::IS_ACTIVE];
                break;
            case 2:
                $params = ['is_active' => DeeplinkRecord::IS_NOT_ACTIVE];
                break;
            case 3:
                $params = ['mode' => DeeplinkRecord::MODE['warming']];
                break;
            case 4:
                $params = ['mode' => DeeplinkRecord::MODE['image']];
                break;
            case 5:
                $params = ['mode' => DeeplinkRecord::MODE['fighting']];
                break;
        }
        $model = new DeeplinkRecord();
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание диплинка
     * @param integer $type
     * @return string
     */
    public function actionDeeplinkAdd($type = 0) {
        $model = new DeeplinkRecord();
        $model->setScenario('add');
        switch($type) {
            case 1:
            default:
                $model->is_active = DeeplinkRecord::IS_ACTIVE;
                $model->mode = DeeplinkRecord::MODE['warming'];
                break;
            case 2:
                $model->is_active = DeeplinkRecord::IS_NOT_ACTIVE;
                $model->mode = DeeplinkRecord::MODE['warming'];
                break;
            case 4:
                $model->is_active = DeeplinkRecord::IS_ACTIVE;
                $model->mode = DeeplinkRecord::MODE['image'];
                break;
            case 5:
                $model->is_active = DeeplinkRecord::IS_ACTIVE;
                $model->mode = DeeplinkRecord::MODE['fighting'];
                break;
        }

        if ($model->load(\Yii::$app->request->post())) {
            $model->deeplink_hash = DeeplinkRecord::getHash();
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/index', 'type' => $type]));
            }
        }

        return $this->render('deeplink-add', ['model' => $model]);
    }

    /**
     * @property-description Редактирование диплинка
     * @param int $id
     * @param integer $type
     * @return string
     * @throws HttpException
     */
    public function actionDeeplinkUpdate($id, $type = 0) {
        $model = DeeplinkRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Диплинк не найден", 404);
        }
        $model->setScenario('update');
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['test/index', 'type' => $type]));
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