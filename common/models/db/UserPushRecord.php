<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class UserPushRecord
 * @package common\models\db
 * @author asisch
 *
 * UserPushRecord model
 *
 * @property integer $id
 * @property string $device_id
 * @property string $token
 * @property integer $test_id
 * @property integer $push_at
 * @property integer $is_handler
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserPushRecord extends ActiveRecord
{

    const NOT_IS_HANDLER = 0;
    const IS_HANDLER = 1;

    public static function tableName()
    {
        return '{{tt_users_pushes}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['device_id', 'token'], 'required', 'on' => ['add', 'update']],
            [['device_id', 'token'], 'string', 'on' => ['add', 'update']],
            [['test_id', 'push_at', 'is_handler'], 'integer', 'on' => ['add', 'update']],
            [['test_id', 'push_at', 'is_handler'], 'default', 'value' => 0, 'on' => ['add']],
            [['is_handler'], 'integer', 'on' => ['set-handler']],
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'device_id' => 'Ид устройства, используется для идентификации пользователя',
            'token' => 'Токен для Firebase',
            'test_id' => 'Ид теста',
            'push_at' => 'Время пуша',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }


    /**
     * @description установка пуша
     * @param string $deviceId
     * @param string $token
     * @param integer $testId
     * @param integer $pushAt
     * @return bool
     */
    public static function setPush($deviceId, $token, $testId, $pushAt) {
        $userPush = self::findOne(['device_id' => $deviceId, 'test_id' => $testId, 'is_handler' => 0]);
        if (is_null($userPush) || empty($userPush)) {
            $userPush = new self;
            $userPush->setScenario('add');
            $userPush->device_id = $deviceId;
            $userPush->token = $token;
            $userPush->test_id = $testId;
            $userPush->push_at = $pushAt;
            $userPush->is_handler = 0;
            return $userPush->save();
        }
        return true;
    }
}