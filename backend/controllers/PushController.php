<?php
namespace backend\controllers;

use common\models\db\SetUserPushRecord;
use common\models\db\UserTestRecord;
use common\models\db\DeeplinkRecord;
use common\models\db\UserPushStatisticRecord;
use common\models\db\TestRecord;
use backend\components\own\baseController\BackController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\base\Model;
use librariesHelpers\helpers\Utf8\Utf8;

/**
 * Class PushController
 * @property-description Модуль управление пушами
 * @package backend\controllers
 * @author asisch
 */
class PushController extends BackController
{
    public $apiToken = 'AAAAsYyEZfA:APA91bGDetC7UNQzqynxzfZRVBYhJYTdrvP8zZPMFsKeO5_Qgw-Frt_VTymDT1iWOY0Qi5uUIifhtqFSKXFvrTU2Y9QCdH2dpVRG76onyANhz0dAEsqy-t2GzYu_3CQ7zLEK1SBx2xdt';
    public $apiUrl = 'https://fcm.googleapis.com/fcm/send';
    /**
     * @property-description Задания пушей
     * @param integer $type
     */
    public function actionIndex($type = 0) {
        $params = [];
        switch($type) {
            case 0:
                $params = ['is_handler' => SetUserPushRecord::NOT_IS_HANDLER];
                break;
            case 1:
                $params = ['is_handler' => SetUserPushRecord::IS_HANDLER];
                break;
        }
        $model = new SetUserPushRecord();
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание задания на пуш
     * @param integer $type
     * @return string
     */
    public function actionAdd($type = 0) {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $registrationDataRange = \Yii::$app->request->get('registrationDataRange','');
            $deeplink_id = \Yii::$app->request->get('deeplink_id',0);
            $gtest_id = \Yii::$app->request->get('gtest_id',0);
            $wtest_id = \Yii::$app->request->get('wtest_id',0);

            $userCount = UserTestRecord::calcUsers($registrationDataRange, $deeplink_id, $gtest_id, $wtest_id);
            $json = ['result' => 'success', 'count' => $userCount];
            return $json;
        }

        $model = new SetUserPushRecord();
        $model->setScenario('add');
        $model->deeplink_id = 0;
        $model->gtest_id = 0;
        $model->wtest_id = 0;
        $model->registration_from = 0;
        $model->registration_to = 0;
        $model->title = '';
        $model->text = '';
        $model->push_at = 0;

        if ($model->load(\Yii::$app->request->post())) {
            if (!empty($model->registrationDataRange)) {
                $dateRange = Utf8::explode(' - ', $model->registrationDataRange);
                $model->registration_from = strtotime($dateRange[0]);
                $model->registration_to = strtotime($dateRange[1]);
            }
            if (!is_numeric($model->push_at)) {
                $model->push_at = strtotime($model->push_at);
            }
            $model->count_users = UserTestRecord::calcUsers($model->registrationDataRange, $model->deeplink_id, $model->gtest_id, $model->wtest_id);
            if ($model->save()) {
                $this->redirect(Url::toRoute(['index', 'type' => $type]));
            }
        }
        if ($model->registration_from > 0) {
            $model->registrationDataRange = date('d-m-Y H:i', $model->registration_from) . ' - ' . date('d-m-Y H:i', $model->registration_to);
        }
        $model->push_at = date('d-m-Y H:i', time());

        return $this->render('add', ['model' => $model, 'type' => $type]);
    }


    public function actionTest() {
       print_r(TestRecord::getPushText(4));
    }


}