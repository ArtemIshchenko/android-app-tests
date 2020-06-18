<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use common\components\own\generate\Generate;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class DeeplinkRecord
 * @package common\models\db
 * @author asisch
 *
 * DeeplinkRecord model
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $app_test_id
 * @property string $deeplink_hash
 * @property string $name
 * @property string $description
 * @property integer $is_active
 * @property integer $mode
 * @property string $url
 * @property integer $created_at
 * @property integer $updated_at
 */
class DeeplinkRecord extends ActiveRecord
{

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const MODE = [
        'warming' => 0,
        'image' => 1,
        'fighting' => 2
    ];

    const MAX_HASH_SIZE = 12;

    public static function tableName()
    {
        return '{{tt_deeplinks}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['deeplink_hash', 'name'], 'required', 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['app_test_id'], 'required', 'enableClientValidation' => false, 'on' => ['white-add', 'white-update']],
            [['test_id'], 'required', 'enableClientValidation' => false, 'on' => ['grey-add', 'grey-update']],
            [['app_test_id', 'test_id'], 'integer', 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['name', 'description'], 'string', 'max' => 256, 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['url', 'deeplink_hash'], 'string', 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['name', 'description', 'url'], 'trim', 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['url'], 'url', 'validSchemes' => ['https', 'http'], 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['is_active', 'mode'], 'integer', 'on' => ['white-add', 'white-update', 'grey-add', 'grey-update']],
            [['test_id', 'app_test_id'], 'default', 'value' => 0, 'on' => ['white-add', 'grey-add']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['white-add', 'grey-add']],
            [['mode'], 'default', 'value' => self::MODE['warming'], 'on' => ['white-add', 'grey-add']],
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
            'test_id' => 'Серый тест',
            'app_test_id' => 'Белый тест',
            'deeplink_hash' => 'Хеш диплинка',
            'name' => 'Наименование',
            'description' => 'Описание',
            'is_active' => 'Статус',
            'mode' => 'Режим',
            'url' => 'Боевая ссылка',
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
        $mode = isset($params['mode']) ? Type_Cast::toUInt($params['mode']) : -1;

        $query = self::find();
        if ($isActive > -1) {
            $query->andWhere(['is_active' => $isActive]);
        }
        if ($mode > -1) {
            $query->andWhere(['mode' => $mode]);
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
     * @description Режимы
     * @return array
     */
    public static function getModes() {
        return [
            self::MODE['warming'] => 'Прогревочный',
            self::MODE['image'] => 'Имиджевый',
            self::MODE['fighting'] => 'Боевой',
        ];
    }

    public static function getHash()
    {
        $code  = '';
        for($i=0;$i<100000;$i++) {
            $code = Generate::generateMixCode(self::MAX_HASH_SIZE);
            $model = self::findOne(['deeplink_hash' => Type_Cast::toStr($code)]);
            if(is_null($model) || empty($model)) {
                break;
            }
        }
        return $code;
    }

    /**
     * @description Удаление приставки
     * @param string $deeplink
     * @return string
     */
    public static function removeSuffix($deeplink) {
        $suffix = 'com.thousandstests://';
        $deeplink = str_replace($suffix, "", $deeplink);
        return $deeplink;
    }

    public static function deeplinkList()
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

}