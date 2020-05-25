<?php
namespace api\modules\v1\controllers;

use api\modules\v1\components\baseController\ApiController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\helpers\Json;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;
use common\models\db\UserTestRecord;

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
                $json = ['result' => 'successful', 'structure' => [], 'url' => '', 'appState' => UserTestRecord::APP_STATE['white']];
                $deeplinkModel = DeeplinkRecord::findOne(['name' => $deeplink, 'is_active' => DeeplinkRecord::IS_ACTIVE]);
                $testId = 0;
                if (!is_null($deeplinkModel) && !empty($deeplinkModel)) {
                    $test = TestRecord::findOne(['id' => $deeplinkModel->test_id, 'is_active' => TestRecord::IS_ACTIVE]);
                    if (!is_null($test) && !empty($test)) {
                        $structure = $test->getStructure();
                        if (!empty($deeplinkModel->url) && !empty($structure)) {
                            $json = ['result' => 'successful', 'structure' => $structure, 'url' => $deeplinkModel->url, 'appState' => UserTestRecord::APP_STATE['grey']];
                            $testId = $test->id;
                        }
                    }
                }
                \Yii::info(['module' => 'test', 'data' => $structure], self::LOG_CATEGORY);
                UserTestRecord::setStatistic($deviceId, $lang, $deeplink, $testId, UserTestRecord::APP_STATE['white'],
                    UserTestRecord::TEST_STATE['notStart'], UserTestRecord::SHOW_ADS['notShow'], UserTestRecord::SHOW_REATING['notShow']);
            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'test', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

}