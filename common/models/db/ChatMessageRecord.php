<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class ChatMessageRecord
 * @package common\models\db
 * @author asisch
 *
 * ChatMessageRecord model
 *
 * @property integer $id
 * @property integer $list_id
 * @property string $text
 * @property integer $message_type
 * @property integer $hold_time
 * @property integer $is_active
 * @property integer $num
 * @property integer $created_at
 * @property integer $updated_at
 */
class ChatMessageRecord extends ActiveRecord
{

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const TYPE = [
        'text' => 0,
        'resBtn' => 1,
    ];

    public static function tableName()
    {
        return '{{tt_chat_messages}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['text'], 'required', 'on' => ['add', 'update']],
            [['text'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['list_id', 'message_type', 'hold_time', 'is_active', 'num'], 'integer', 'on' => ['add', 'update']],
            [['list_id', 'message_type'], 'default', 'value' => 0, 'on' => ['add']],
            [['hold_time'], 'default', 'value' => 0, 'on' => ['add']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['is_active'], 'integer', 'on' => ['change-active']],
            [['num'], 'integer', 'on' => ['change-num']],
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
            'list_id' => 'Список сообщений',
            'text' => 'Текст',
            'message_type' => 'Тип',
            'hold_time' => 'Время холда до следующего сообщения, с',
            'is_active' => 'Статус',
            'num' => 'Номер по порядку',
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
        $isActive = isset($params['is_active']) ? Type_Cast::toUInt($params['is_active']) : -1;
        $listId = isset($params['list_id']) ? Type_Cast::toUInt($params['list_id']) : -1;

        $query = self::find();
        if ($isActive > -1) {
            $query->andWhere(['is_active' => $isActive]);
        }
        if ($listId > -1) {
            $query->andWhere(['list_id' => $listId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['num' => SORT_ASC]
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

    /**
     * @description Активность
     * @return array
     */
    public static function getStatus() {
        return [
            self::IS_NOT_ACTIVE => 'Не активный',
            self::IS_ACTIVE => 'Активный',
        ];
    }

    /**
     * @description Тип
     * @return array
     */
    public static function getType() {
        return [
            self::TYPE['text'] => 'Текст',
            self::TYPE['resBtn'] => 'Кнопка результата',
        ];
    }

    /**
     * @description следующая позиция
     * @param int $listId
     * @return integer
     */
    public static function getNextPos($listId) {
        $num = self::find()->where(['list_id' => $listId])->max('num');
        ++$num;
        return $num;
    }

}