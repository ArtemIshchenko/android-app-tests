<?php
namespace console\controllers;

use common\models\db\SetUserPushRecord;
use common\models\db\UserPushStatisticRecord;
use common\models\db\DeeplinkRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use librariesHelpers\helpers\Utf8\Utf8;
use yii\console\Controller;

class AndroidExtraPushController extends Controller
{

    const MAX_PUSH_PROCESS = 3;
    const MAX_USER_SEND = 30;
    const MAX_USER_AT_ONE_REQUEST = 10;

    public $apiToken = 'AAAAsYyEZfA:APA91bGDetC7UNQzqynxzfZRVBYhJYTdrvP8zZPMFsKeO5_Qgw-Frt_VTymDT1iWOY0Qi5uUIifhtqFSKXFvrTU2Y9QCdH2dpVRG76onyANhz0dAEsqy-t2GzYu_3CQ7zLEK1SBx2xdt';

    public $apiUrl = 'https://fcm.googleapis.com/fcm/send';

    public $testDeviceId = 'f8eb3cca3c8669c1';
    public $testToken = 'eGBZ-leKTECKN2Y_hBIY6E:APA91bFwLupZT8sSoGriwNZTVNO_lpYUQECc0RS-sVnUXEEE2MUhscK8YsFSII2ReL5kE_qPkY6NvxBeqIqZQsMvfXBCnX2xZYa1BmfpuyY-VmIejdKufWWlEPX9-ri0tBR0I16qVLSW';

    public function actionPush() {

        $timezone = 'Europe/Kiev';
        date_default_timezone_set($timezone);

        $setUserPushes = SetUserPushRecord::find()
            ->where(['is_handler' => SetUserPushRecord::NOT_IS_HANDLER])
            ->andWhere(['<', 'push_at', time()])
            ->orderBy(['id' => SORT_ASC])
            ->limit(self::MAX_PUSH_PROCESS)
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
                $testDeviceId = $this->testDeviceId;
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
                $sql .= " LIMIT " . self::MAX_USER_SEND;

                $results = \Yii::$app->db->createCommand($sql)->queryAll();
                if(is_array($results) && !empty($results)) {
                    $tokens = [];
                    $resultsCount = count($results);
                    $i = 0;
                    foreach ($results as $result) {
                        if (!empty($result['firebase_token'])) {
                            $tokens[$result['id']] = $result['firebase_token'];
                            if ((($i + 1) >= self::MAX_USER_AT_ONE_REQUEST) || (($i + 1) >= $resultsCount)) {
                                if (!empty($tokens)) {
                                    $sendParams = self::prepareSendMessageMulty($tokens, $title, $text);
                                    $sendResult = $this->sendMessageMulty($sendParams);
                                    if (is_array($sendResult) && !empty($sendResult)) {
                                        foreach ($sendResult as $userId => $sr) {
                                            if (isset($sr['response']) && ($sr['response']['success'] == 1)) {
                                                UserPushStatisticRecord::setStatistic($userId, $setUserPush->id);
                                            }
                                        }
                                    }
                                    $tokens = [];
                                    $i = -1;
                                }
                            }
                            ++$i;
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



}