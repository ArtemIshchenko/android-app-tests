<?php
namespace backend\controllers;

use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\SettingRecord;
use common\models\db\LogRecord;
use backend\components\own\baseController\BackController;
use backend\models\form\StatisticAndroidFilter;
use backend\components\own\statistic\StatisticAndroidSystem;
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
//    public $apiToken = 'AAAAsYyEZfA:APA91bGDetC7UNQzqynxzfZRVBYhJYTdrvP8zZPMFsKeO5_Qgw-Frt_VTymDT1iWOY0Qi5uUIifhtqFSKXFvrTU2Y9QCdH2dpVRG76onyANhz0dAEsqy-t2GzYu_3CQ7zLEK1SBx2xdt';

//    public $apiUrl = 'https://fcm.googleapis.com/fcm/send';
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
        $isCheck = \Yii::$app->request->post('check', false);
         if (!$isCheck) {
            $model = new TestRecord();
            $model->setScenario('add');
            switch ($type) {
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
        } else {
             $model = new TestRecord();
             $model->setScenario('check');
             $model->load(\Yii::$app->request->post());
             $model->validate();
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
        $isCheck = \Yii::$app->request->post('check', false);
        if (!$isCheck) {
            $model = TestRecord::findOne($id);
            $model->convReverse();
            if (is_null($model) || empty($model)) {
                throw new HttpException(404, "Тест не найден", 404);
            }
            $model->setScenario('update');
            if ($model->load(\Yii::$app->request->post())) {
                if ($model->save()) {
                    $this->redirect(Url::toRoute(['test/tests', 'type' => $type]));
                }
            }
        } else {
            $model = new TestRecord();
            $model->setScenario('check');
            $model->load(\Yii::$app->request->post());
            $model->validate();
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
        $model->setScenario('white-add');
        $model->app_test_id = 0;
        $model->test_id = 0;
        $model->url = '';
        $model->description = '';
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
            if (in_array($model->mode, [DeeplinkRecord::MODE['fighting'], DeeplinkRecord::MODE['image']])) {
                $model->setScenario('grey-add');
            }
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
        $model->setScenario('white-update');
        if ($model->load(\Yii::$app->request->post())) {
            if (in_array($model->mode, [DeeplinkRecord::MODE['fighting'], DeeplinkRecord::MODE['image']])) {
                $model->setScenario('grey-update');
            }
            echo $model->mode;
            echo $model->scenario;
            if ($model->save()) {
                $this->redirect(Url::toRoute(['test/index', 'type' => $type]));
            }
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

    /**
     * @property-description Логи
     * @param integer $t
     */
    public function actionLog($t = 0) {
        $params = [];
        switch($t) {
            case 0:
                $params = ['type' => 0];
                break;
            case 1:
                $params = ['type' => 1];
                break;
        }
        $statisticFilter = new StatisticAndroidFilter();
        $statisticFilter->load(\Yii::$app->request->get());
        //$pageSize = $statisticFilter->pageSize;

        $data = StatisticAndroidSystem::logs($statisticFilter, $t);
        $dateTimestamp = $statisticFilter->getDateTimestamp();
        $params['dateFrom'] = $dateTimestamp['dateFrom'];
        $params['dateTo'] = $dateTimestamp['dateTo'];

        $model = new LogRecord();
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('log', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'statisticFilter' => $statisticFilter,
            'data' => $data,
        ]);
    }

    public function actionTest() {
        //LogRecord::register('sdsdf', 'dfgfd', 'ru');
//        $logs = LogRecord::find()->all();
//        if (!empty($logs)) {
//            foreach ($logs as $log) {
//                $log->setScenario('update');
//                $log->deeplink = DeeplinkRecord::removeSuffix($log->deeplink);
//                $log->save();
//            }
//        }
    }

}