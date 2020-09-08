<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class ChatMessageListRecord
 * @package common\models\db
 * @author asisch
 *
 * ChatMessageListRecord model
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class ChatMessageListRecord extends ActiveRecord
{

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;


    public static function tableName()
    {
        return '{{tt_chat_message_lists}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['is_active'], 'integer', 'on' => ['add', 'update']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['is_active'], 'integer', 'on' => ['change-active']],
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
            'name' => 'Наименование',
            'is_active' => 'Статус',
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

        $query = self::find();
        if ($isActive > -1) {
            $query->andWhere(['is_active' => $isActive]);
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
     * @description Наименование
     * @param integer $id
     * @return array
     */
    public static function getNameById($id) {
        $name = '';
        $model = self::findOne($id);
        if (!is_null($model) && !empty($model)) {
            $name = $model->name;
        }
        return $name;
    }

    /**
     * @description Списки
     * @return array
     */
    public static function getList() {
        $list = ArrayHelper::map(self::find()->where(['is_active' => self::IS_ACTIVE])->orderBy(['id' => SORT_ASC])->all(), 'id', 'name');
        return $list;
    }

}