<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class SetUserPushRecord
 * @package common\models\db
 * @author asisch
 *
 * SetUserPushRecord model
 *
 * @property integer $id
 * @property integer $deeplink_id
 * @property integer $gtest_id
 * @property integer $wtest_id
 * @property integer $registration_from
 * @property integer $registration_to
 * @property string $title
 * @property string $text
 * @property integer $push_at
 * @property integer $count_users
 * @property integer $is_handler
 * @property integer $created_at
 * @property integer $updated_at
 */
class SetUserPushRecord extends ActiveRecord
{

    const NOT_IS_HANDLER = 0;
    const IS_HANDLER = 1;

    public $registrationDataRange = '';

    public static function tableName()
    {
        return '{{tt_set_users_pushes}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['title', 'text', 'push_at'], 'required', 'on' => ['add']],
            [['title'], 'string', 'max' => 256, 'on' => ['add']],
            [['text'], 'string', 'max' => 512, 'on' => ['add']],
            [['registrationDataRange'], 'string', 'on' => ['add']],
            [['deeplink_id', 'gtest_id', 'wtest_id', 'registration_from', 'registration_to', 'count_users', 'is_handler'], 'integer', 'on' => ['add']],
            [['deeplink_id', 'gtest_id', 'wtest_id', 'registration_from', 'registration_to', 'count_users', 'is_handler'], 'default', 'value' => 0, 'on' => ['add']],
            [['push_at'], 'datetime', 'min' => time(), 'minString' => date('d-m-Y H:i'), 'format' => 'd-M-yyyy H:m', 'timestampAttribute' => 'push_at', 'on' => ['add']],
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
            'deeplink_id' => 'Диплинк пользователя',
            'gtest_id' => 'Серый тест',
            'wtest_id' => 'Белый тест',
            'registration_from' => 'Дата регистрации пользователя, от',
            'registration_to' => 'Дата регистрации пользователя, до',
            'registrationDataRange' => 'Дата регистрации пользователя',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'push_at' => 'Дата и время пуша',
            'count_users' => 'Количество пользователей',
            'is_handler' => 'Обработан',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @description Поиск по выбраным полям
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $isHandler = isset($params['is_handler']) ? Type_Cast::toUInt($params['is_handler']) : -1;

        $query = self::find();
        if ($isHandler > -1) {
            $query->andWhere(['is_handler' => $isHandler]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['id' => SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => self::PAGE_LIMIT,
            ],
        ]);
        if (!($this->load($params))) {
            return $dataProvider;
        }
        return $dataProvider;
    }
}