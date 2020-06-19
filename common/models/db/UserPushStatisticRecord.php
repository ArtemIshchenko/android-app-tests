<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class UserPushStatisticRecord
 * @package common\models\db
 * @author asisch
 *
 * UserPushStatisticRecord model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $push_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserPushStatisticRecord extends ActiveRecord
{

    const NOT_IS_HANDLER = 0;
    const IS_HANDLER = 1;

    public static function tableName()
    {
        return '{{tt_users_pushes_statistic}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['user_id', 'push_id'], 'required', 'on' => ['add', 'update']],
            [['user_id', 'push_id'], 'integer', 'on' => ['add', 'update']],
            [['user_id', 'push_id'], 'default', 'value' => 0, 'on' => ['add']],
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
            'user_id' => 'Ид пользователя',
            'push_id' => 'Ид пуша',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @description статистика
     * @param integer $userId
     * @param integer $pushId
     * @return bool
     */
    public static function setStatistic($userId, $pushId)
    {
        $result = true;
        $userTest = self::findOne(['user_id' => $userId, 'push_id' => $pushId]);
        if (is_null($userTest) || empty($userTest)) {
            $userTest = new self;
            $userTest->setScenario('add');
            $userTest->user_id = $userId;
            $userTest->push_id = $pushId;
            $result = $userTest->save();
        }
        return $result;
    }

}