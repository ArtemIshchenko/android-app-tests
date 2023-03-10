<?php
namespace api\modules\v1\controllers;

use api\modules\v1\components\baseController\ApiController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\helpers\Json;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\UserTestRecord;
use common\models\db\SettingRecord;
use common\models\db\UserPushRecord;
use common\models\db\LogRecord;

class AndroidTestController extends ApiController
{

    const LOG_CATEGORY = "android-test";

    const WHITE_LIST = [
        '5cc9ed3ca9b80086'
    ];

    /**Данные теста
     * @throws \yii\base\ExitException
     */
    public function actionTest()
    {
        //$data = \Yii::$app->request->post();
        $data = json_decode(file_get_contents("php://input"), true);
        \Yii::info(['module' => 'test', 'data' => $data], self::LOG_CATEGORY);
        $json = ['result' => 'error'];
        try {
            $deviceId = isset($data['deviceId']) ? Type_Cast::toStr($data['deviceId']) : '';
            $deeplink = isset($data['deeplink']) ? Type_Cast::toStr($data['deeplink']) : '';
            $deeplink = DeeplinkRecord::removeSuffix($deeplink);
            $lang = isset($data['lang']) ? Type_Cast::toStr($data['lang']) : 'en';
            $firebaseToken = isset($data['firebaseToken']) ? Type_Cast::toStr($data['firebaseToken']) : '';
            LogRecord::register($deviceId, $deeplink, $lang);
            if (!empty($deviceId) && !empty($deeplink)) {
                $appState = UserTestRecord::APP_STATE['white'];
                $structureInit = [
                    'id' => 0,
                    'title' => '',
                    'description' => '',
                    'docData' => '',
                    'imageAnswer' => '',
                    'timerSetting' => 0,
                    'fbEvent' => '',
                    'push1' => '',
                    'push2' => '',
                    'vbBotUri' => '',
                    'vbBotDeeplink' => '',
                    'questions' => [
                        [
                            'number' => 0,
                            'text' => '',
                            'answers' => [
                                [
                                    'number' => 0,
                                    'text' => '',
                                    'isSignal' => 0,
                                    'rating' => 0,
                                ]
                            ]
                        ]
                    ]
                ];
                $showAdvertising = SettingRecord::getValByName('showAdvertising', SettingRecord::SECTION['main']);
                $showCommentGpWidget = SettingRecord::getValByName('showCommentGpWidget', SettingRecord::SECTION['main']);
                $json = ['result' => 'successful', 'structure' => $structureInit, 'image' => '', 'imageName' => '', 'fbEvent' => 0, 'whiteTestId' => 1, 'url' => '', 'mode' => -1, 'appState' => $appState, 'showAdvertising' => $showAdvertising, 'showCommentGpWidget' => $showCommentGpWidget];
                $deeplinkModel = DeeplinkRecord::findOne(['name' => $deeplink, 'is_active' => DeeplinkRecord::IS_ACTIVE]);
                $testId = 0;
                $whiteTestId = 1;
                $image = '';
                $imageName = '';
                $fbEvent = 0;
                $structure = [];
//                if (!UserTestRecord::isHasUser($deviceId, self::WHITE_LIST)) {
                    if (!is_null($deeplinkModel) && !empty($deeplinkModel)) {
                        if ($deeplinkModel->mode != DeeplinkRecord::MODE['warming']) {
                            $test = TestRecord::findOne(['id' => $deeplinkModel->test_id, 'is_active' => TestRecord::IS_ACTIVE]);
                            if (!is_null($test) && !empty($test)) {
                                $testId = $test->id;
                                if (!empty($test->image)) {
                                    $image = TestRecord::getImageUrl($test->image);
                                    $imageName = $test->image;
                                }
                                $fbEvent = $test->fb_event;
                                $structure = $test->getStructure();
                                $appState = UserTestRecord::APP_STATE['grey'];
                            }
                        } else {
                            $structure = $structureInit;
                            $appState = UserTestRecord::APP_STATE['white'];
                            $whiteTestId = $deeplinkModel->app_test_id;
                        }

                        if (!empty($structure)) {
                            $json = ['result' => 'successful', 'structure' => $structure, 'image' => $image, 'imageName' => $imageName, 'fbEvent' => $fbEvent, 'whiteTestId' => $whiteTestId, 'url' => $deeplinkModel->url, 'mode' => $deeplinkModel->mode, 'appState' => $appState, 'showAdvertising' => $showAdvertising, 'showCommentGpWidget' => $showCommentGpWidget];
                        }
                    }
//                }

                \Yii::info(['module' => 'test', 'data' => $json], self::LOG_CATEGORY);
                UserTestRecord::setStatistic($deviceId, $lang, $deeplink, $firebaseToken, $whiteTestId, $testId, $appState,
                    UserTestRecord::TEST_STATE['notStart'], $showAdvertising, $showCommentGpWidget);
            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'test', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

    /**Данные пользователя
     * @throws \yii\base\ExitException
     */
    public function actionUser()
    {
        //$data = \Yii::$app->request->post();
        $data = json_decode(file_get_contents("php://input"), true);
        \Yii::info(['module' => 'user', 'data' => $data], self::LOG_CATEGORY);
        $json = ['result' => 'error'];
        try {
            $deviceId = isset($data['deviceId']) ? Type_Cast::toStr($data['deviceId']) : '';
            $token = isset($data['token']) ? Type_Cast::toStr($data['token']) : '';
            $testId = isset($data['testId']) ? Type_Cast::toUInt($data['testId']) : 0;
            $pushAt = isset($data['pushAt']) ? Type_Cast::toUInt($data['pushAt']) : 0;

            if (!empty($deviceId) && !empty($token)) {
                LogRecord::register($deviceId, '', '', $token, $testId);
                $res = UserPushRecord::setPush($deviceId, $token, $testId, $pushAt);
                $showAdvertising = SettingRecord::getValByName('showAdvertising', SettingRecord::SECTION['main']);
                $showCommentGpWidget = SettingRecord::getValByName('showCommentGpWidget', SettingRecord::SECTION['main']);
                UserTestRecord::setStatistic($deviceId, '', '', $token, 0, $testId, UserTestRecord::APP_STATE['grey'],
                    UserTestRecord::TEST_STATE['finishNotBtn'], $showAdvertising, $showCommentGpWidget);
                if ($res) {
                    $json = ['result' => 'successful'];
                    \Yii::info(['module' => 'user', 'data' => ['deviceId' => $deviceId, 'token' => $token, 'testId' => $testId, 'pushAt' => $pushAt]], self::LOG_CATEGORY);
                }
            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'user', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

}