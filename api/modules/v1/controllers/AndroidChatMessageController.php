<?php
namespace api\modules\v1\controllers;

use api\modules\v1\components\baseController\ApiController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\helpers\Json;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\UserTestRecord;
use common\models\db\ChatMessageListRecord;
use common\models\db\ChatMessageRecord;

class AndroidChatMessageController extends ApiController
{

    const LOG_CATEGORY = "android-chat-message";

    /**Данные сообщения
     * @throws \yii\base\ExitException
     */
    public function actionMessage()
    {
        //$data = \Yii::$app->request->post();
        $data = json_decode(file_get_contents("php://input"), true);
        \Yii::info(['module' => 'chat-message', 'data' => $data], self::LOG_CATEGORY);
        $json = ['result' => 'error'];
        try {
            $deviceId = isset($data['deviceId']) ? Type_Cast::toStr($data['deviceId']) : '';
            $deeplink = isset($data['deeplink']) ? Type_Cast::toStr($data['deeplink']) : '';
            $deeplink = DeeplinkRecord::removeSuffix($deeplink);
            if (!empty($deviceId) && !empty($deeplink)) {
                $json = ['result' => 'successful', 'messages' => []];
                $deeplinkModel = DeeplinkRecord::findOne(['name' => $deeplink, 'is_active' => DeeplinkRecord::IS_ACTIVE]);
                $messages = [];
                if (!is_null($deeplinkModel) && !empty($deeplinkModel) && ($deeplinkModel->chatlist_id > 0)) {
                        $chatList = ChatMessageListRecord::findOne(['id' => $deeplinkModel->chatlist_id, 'is_active' => ChatMessageListRecord::IS_ACTIVE]);
                        if (!is_null($chatList) && !empty($chatList)) {
                            $chatMessages = ChatMessageRecord::find()->where(['list_id' => $deeplinkModel->chatlist_id])->orderBy(['num' => SORT_ASC])->all();
                            if (!is_null($chatMessages) && !empty($chatMessages)) {
                                foreach ($chatMessages as $msg) {
                                    $messages[] = [
                                        'number' => $msg->num,
                                        'text' => $msg->text,
                                        'msgType' => $msg->message_type,
                                        'holdTime' => $msg->hold_time,
                                    ];
                                }
                            }
                        }

                    if (!empty($messages)) {
                        $json = ['result' => 'successful', 'messages' => $messages];
                    }
                }
                \Yii::info(['module' => 'chat-message', 'data' => $json], self::LOG_CATEGORY);

            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'chat-message', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

}