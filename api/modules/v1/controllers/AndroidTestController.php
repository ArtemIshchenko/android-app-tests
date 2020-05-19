<?php
namespace api\modules\v1\controllers;

use api\modules\v1\components\baseController\ApiController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\helpers\Json;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;

class AndroidTestController extends ApiController
{

    const LOG_CATEGORY = "android-test";

    /**Сохранение емейла и пинкода пользователя
     * @throws \yii\base\ExitException
     */
    public function actionTest()
    {
        //$data = json_decode(file_get_contents("php://input"), true);
        $data = \Yii::$app->request->post();
        \Yii::info(['module' => 'test', 'data' => $data], self::LOG_CATEGORY);
        $json = ['result' => 'error'];
        try {
            $deeplinkHash = isset($data['deeplinkHash']) ? Type_Cast::toStr($data['deeplinkHash']) : '';

            $deeplink = DeeplinkRecord::findOne(['deeplink_hash' => $deeplinkHash, 'is_active' => DeeplinkRecord::IS_ACTIVE]);
            if (!is_null($deeplink) && !empty($deeplink)) {
                $test = TestRecord::findOne(['id' => $deeplink->test_id, 'is_active' => TestRecord::IS_ACTIVE]);
                if (!is_null($test) && !empty($test)) {
                    $structure = $test->getStructure();
                    if (!empty($deeplink->url) && !empty($structure)) {
                        $json = ['result' => 'successful', 'structure' => $structure, 'url' => $deeplink->url];
                    } else {
                        \Yii::error(['module' => 'test', 'post' => $data, 'message' => 'params is empty'], self::LOG_CATEGORY);
                    }
                } else {
                    \Yii::error(['module' => 'test', 'post' => $data, 'message' => 'undefined test'], self::LOG_CATEGORY);
                }
            } else {
                \Yii::error(['module' => 'test', 'post' => $data, 'message' => 'undefined deeplink'], self::LOG_CATEGORY);
            }
        } catch (\Exception $e) {
            \Yii::error(['module' => 'test', 'post' => $data, 'message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'file' => $e->getFile()], self::LOG_CATEGORY);
        }
        header('Content-Type: application/json');
        print json_encode($json);exit;
    }

}