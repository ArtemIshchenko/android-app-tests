<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use common\components\own\generate\Generate;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;

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
            [['test_id', 'app_test_id', 'deeplink_hash', 'name'], 'required', 'on' => ['add', 'update']],
            [['deeplink_hash'], 'required', 'on' => ['add']],
            [['name', 'description'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['url', 'deeplink_hash'], 'string', 'on' => ['add', 'update']],
            [['name', 'description', 'url'], 'trim', 'on' => ['add', 'update']],
            [['url'], 'url', 'validSchemes' => ['https', 'http'], 'on' => ['add', 'update']],
            [['is_active', 'mode'], 'integer', 'on' => ['add', 'update']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['mode'], 'default', 'value' => self::MODE['warming'], 'on' => ['add']],
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
            'test_id' => 'Тест',
            'app_test_id' => 'Тест приложения (для прогревочного режима)',
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
            self::MODE['image'] => 'Имеджевый',
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

}