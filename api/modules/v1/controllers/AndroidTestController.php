<?php
namespace api\modules\v1\controllers;

use api\modules\v1\components\baseController\ApiController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\helpers\Json;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\UserTestRecord;
use common\models\db\SettingRecord;

class AndroidTestController extends ApiController
{

    const LOG_CATEGORY = "android-test";

    /**Сохранение емейла и пинкода пользователя
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
            $lang = isset($data['lang']) ? Type_Cast::toStr($data['lang']) : 'en';

            if (!empty($deviceId) && !empty($deeplink)) {
                $appState = UserTestRecord::APP_STATE['white'];
                $structureInit = [
                    'id' => 0,
                    'title' => '',
                    'description' => '',
                    'imageAnswer' => '',
                    'timerSetting' => 0,
                    'questions' => [
                        'number' => 0,
                        'text' => '',
                        'answers' => [
                            'number' => 0,
                            'text' => '',
                            'isSignal' => 0,
                            'rating' => 0,
                        ]
                    ]
                ];
                $showAdvertising = SettingRecord::getValByName('showAdvertising', SettingRecord::SECTION['main']);
                $showCommentGpWidget = SettingRecord::getValByName('showCommentGpWidget', SettingRecord::SECTION['main']);
                $json = ['result' => 'successful', 'structure' => $structureInit, 'whiteTestId' => '', 'url' => '', 'mode' => '', 'appState' => $appState, 'showAdvertising' => $showAdvertising, 'showCommentGpWidget' => $showCommentGpWidget];
                $deeplinkModel = DeeplinkRecord::findOne(['name' => $deeplink, 'is_active' => DeeplinkRecord::IS_ACTIVE]);
                $testId = 0;
                $structure = [];
                if (!is_null($deeplinkModel) && !empty($deeplinkModel)) {
                    $test = TestRecord::findOne(['id' => $deeplinkModel->test_id, 'is_active' => TestRecord::IS_ACTIVE]);
                    if (!is_null($test) && !empty($test)) {
                        $structure = $test->getStructure();
                        $appState = UserTestRecord::APP_STATE['grey'];
                        if ($deeplinkModel->mode == DeeplinkRecord::MODE['warming']) {
                            $appState = UserTestRecord::APP_STATE['white'];
                        }
                        if (!empty($deeplinkModel->url) && !empty($structure)) {
                            $json = ['result' => 'successful', 'structure' => $structure, 'whiteTestId' => $deeplinkModel->app_test_id, 'url' => $deeplinkModel->url, 'mode' => $deeplinkModel->mode, 'appState' => $appState, 'showAdvertising' => $showAdvertising, 'showCommentGpWidget' => $showCommentGpWidget];
                            $testId = $test->id;
                        }
                    }
                }
                \Yii::info(['module' => 'test', 'data' => $structure], self::LOG_CATEGORY);
                UserTestRecord::setStatistic($deviceId, $lang, $deeplink, $testId, $appState,
                    UserTestRecord::TEST_STATE['notStart'], UserTestRecord::SHOW_ADS['notShow'], UserTestRecord::SHOW_REATING['notShow']);
            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'test', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

}