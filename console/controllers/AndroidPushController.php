<?php
namespace console\controllers;

use common\models\db\UserPushRecord;
use common\models\db\TestRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use librariesHelpers\helpers\Utf8\Utf8;
use yii\console\Controller;

class AndroidPushController extends Controller
{

    const MAX_USER_SEND = 50;

    public $apiToken = 'AAAAsYyEZfA:APA91bGDetC7UNQzqynxzfZRVBYhJYTdrvP8zZPMFsKeO5_Qgw-Frt_VTymDT1iWOY0Qi5uUIifhtqFSKXFvrTU2Y9QCdH2dpVRG76onyANhz0dAEsqy-t2GzYu_3CQ7zLEK1SBx2xdt';

    public $apiUrl = 'https://fcm.googleapis.com/fcm/send';


    public function actionPush() {
        $text = 'Просмотрите их сейчас';
        $title = 'Результаты теста готовы';

print 'push';
        $timezone = 'Europe/Kiev';
        date_default_timezone_set($timezone);

        $userPushes = UserPushRecord::find()
            ->where(['is_handler' => UserPushRecord::NOT_IS_HANDLER])
            ->andWhere(['<', 'push_at', time()])
            ->orderBy(['id' => SORT_ASC])
            ->limit(self::MAX_USER_SEND)
            ->all();

        if (!is_null($userPushes) && !empty($userPushes)) {
              foreach ($userPushes as $userPush) {
                  $pushText = TestRecord::getPushText($userPush->test_id);
                  if (!empty($pushText['push1'])) {
                      $title = $pushText['push1'];
                  }
                  if (!empty($pushText['push2'])) {
                      $text = $pushText['push2'];
                  }
                  $sendParams = self::prepareSendMessage($userPush->token, $title, $text);
                  $this->sendMessage($sendParams);
                  $userPush->setScenario('set-handler');
                  $userPush->is_handler = UserPushRecord::IS_HANDLER;
                  $userPush->save();
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

//    private static function asyncSendMessage($params) {
//
//        $config = [
//            'timeout'  => 5.0,
//            'read_timeout' => 30,
//        ];
//
//        $client = new Client($config);
//        $requests = function ($total) use ($params) {
//            for ($i = 0; $i < $total; $i++) {
//                yield new Request(
//                    'POST',
//                    $this->apiUrl,
//                    [
//                        'Authorization' => 'key=' . $this->apiToken,
//                        'Content-type' => 'application/json'
//                    ],
//                    json_encode($params[$i])
//                );
//            }
//        };
//
//        $result = [];
//        $pool = new Pool($client, $requests(count($params)), [
//            'concurrency' => 5,
//            'fulfilled' => function (ResponseInterface $response, $index) use (&$result) {
//                $result[$index] = json_decode($response->getBody()->getContents());
//            },
//            'rejected' => function ($reason, $index) use (&$result) {
//                if ($reason instanceof ClientException) {
//                    $result[$index] = json_decode($reason->getResponse()->getBody()->getContents());
//                }
//            },
//        ]);
//
//        $promise = $pool->promise();
//        $promise->wait();
//        return $result;
//    }

}