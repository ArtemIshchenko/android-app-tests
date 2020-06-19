<?php
namespace backend\controllers;

use common\models\db\SetUserPushRecord;
use common\models\db\UserTestRecord;
use common\models\db\DeeplinkRecord;
use common\models\db\UserPushStatisticRecord;
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
        $timezone = 'Europe/Kiev';
        date_default_timezone_set($timezone);

        $setUserPushes = SetUserPushRecord::find()
            ->where(['is_handler' => SetUserPushRecord::NOT_IS_HANDLER])
            ->andWhere(['<', 'push_at', time()])
            ->orderBy(['id' => SORT_ASC])
            ->limit(5)
            ->all();

        if (!is_null($setUserPushes) && !empty($setUserPushes)) {
            foreach ($setUserPushes as $setUserPush) {
                $title = $setUserPush->title;
                $text = $setUserPush->text;
                $pushId = $setUserPush->id;
                $deeplinkId = $setUserPush->deeplink_id;
                $gtestId = $setUserPush->gtest_id;
                $wtestId = $setUserPush->wtest_id;
                $registrationFrom = $setUserPush->registration_from;
                $registrationTo = $setUserPush->registration_to;

                $excludeUser = "SELECT `user_id` FROM `tt_users_pushes_statistic` WHERE `push_id` = '$pushId'";
                $sql = "SELECT * FROM `tt_users_tests` WHERE `id` NOT IN ($excludeUser)";
                $testDeviceId = "f8eb3cca3c8669c1";
                $sql .= " AND device_id = '$testDeviceId'";
                if ($deeplinkId > 0) {
                    $deeplinkModel = DeeplinkRecord::findOne($deeplinkId);
                    if (!is_null($deeplinkModel) && !empty($deeplinkModel)) {
                        $deeplink = $deeplinkModel->name;
                        $sql .= " AND deeplink = '$deeplink'";
                    }
                }
                if ($gtestId > 0) {
                    $sql .= " AND test_id = '$gtestId'";
                }
                if ($wtestId > 0) {
                    $sql .= " AND app_test_id = '$wtestId'";
                }
                if (($registrationFrom > 0) && ($registrationTo > 0)) {
                    $sql .= " AND created_at BETWEEN $registrationFrom AND $registrationTo";
                }
                $sql .= " LIMIT 30";

                $results = \Yii::$app->db->createCommand($sql)->queryAll();
                if(is_array($results) && !empty($results)) {
                    $tokens = [];
                    $resultsCount = count($results);
                    foreach ($results as $i => $result) {
                        $tokens[$result['id']] = $result['firebase_token'];
                        if ((($i + 1) >= 10) || (($i + 1) >= $resultsCount)) {
                            if (!empty($tokens)) {
                                $sendParams = self::prepareSendMessageMulty($tokens, $title, $text);
                                echo '<pre>';print_r($sendParams);
                                $sendResult = $this->sendMessageMulty($sendParams);
                                print_r($sendResult);
                                if (is_array($sendResult) && !empty($sendResult)) {
                                    foreach ($sendResult as $userId => $sr) {
                                        if (isset($sr['response']) && ($sr['response']['success'] == 1)) {
                                            UserPushStatisticRecord::setStatistic($userId, $setUserPush->id);
                                        }
                                    }
                                }
                            }
                            $tokens = [];
                        }
                    }
                } else {
                    $setUserPush->setScenario('set-handler');
                    $setUserPush->is_handler = SetUserPushRecord::IS_HANDLER;
                    $setUserPush->save();
                }
            }
        }
    }

    private static function prepareSendMessageMulty($tokens, $title, $body)
    {
        $fields = [];
        if (is_array($tokens) && !empty($tokens)) {
            foreach ($tokens as $userId => $token) {
                $fields[$userId] = [
                    'to' => $token,
                    'content_available' => true,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'click_action' => 'fcm.action.Ttests',
                    ],
                    'priority' => 'high'
                ];
            }
        }
        return $fields;
    }

    private function sendMessageMulty($params)
    {
        if (is_array($params) && !empty($params)) {
            $mh = curl_multi_init();
            $channels = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key=' . $this->apiToken;

            foreach ($params as $userId => $param) {
                $json = json_encode($param);
                $ch = curl_init($this->apiUrl);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt( $ch, CURLOPT_TIMEOUT, 30);
                curl_setopt( $ch, CURLOPT_MAXREDIRS, 7);

                curl_multi_add_handle($mh, $ch);

                $channels[$userId] = $ch;
            }

            $running = NULL;
            do {
                curl_multi_exec($mh,$running);
            } while($running > 0);

            $result = [];
            foreach ($channels as $userId => $ch) {
                $response = curl_multi_getcontent($ch);
                $res = explode("\n",$response);
                $response = json_decode($response, true);
                //$status = explode(":",$res[0])[1];
                //$errorCode = explode(":",$res[1])[1];


                $result[$userId] = [
                    'response' => $response,
                    //'status' => $status,
                    //'error_code' => $errorCode,
                ];

                curl_multi_remove_handle($mh, $ch);
            }
            curl_multi_close($mh);
        }
        return $result;
    }
    private function sendMessageMulty1($params)
    {
        if (is_array($params) && !empty($params)) {
            $mh = curl_multi_init();
            $channels = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key=' . $this->apiToken;

            foreach ($params as $param) {
                $json = json_encode($param);
                $ch = curl_init($this->apiUrl);
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_multi_add_handle($mh, $ch);

                $channels[] = $ch;
            }

            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) == -1) {
                    continue;
                }
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }

            $result = [];
            foreach ($channels as $ch) {
                $response = curl_multi_getcontent($ch);
                $res = explode("\n",$response);
                $response = json_decode($response, true);
                //$status = explode(":",$res[0])[1];
                //$errorCode = explode(":",$res[1])[1];


                $result[] = [
                    'response' => $response,
                    //'status' => $status,
                    //'error_code' => $errorCode,
                ];

                curl_multi_remove_handle($mh, $ch);
            }
            curl_multi_close($mh);
        }
        return $result;
    }

    private static function prepareSendMessage($token, $title, $body)
    {

        $fields = [
            'to' => $token,
            'content_available' => true,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => [
                'click_action' => 'fcm.action.Ttests',
            ],
            'priority' => 'high'
        ];
        return $fields;
    }

    private function sendMessage($params)
    {

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $this->apiToken;

        $json = json_encode($params);
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($ch);
        print_r($return);
        curl_close($ch);
    }


}