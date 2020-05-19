<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
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


    public static function tableName()
    {
        return '{{tt_deeplinks}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['test_id', 'app_test_id', 'name'], 'required', 'on' => ['add', 'update']],
            [['name', 'description'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['is_active', 'mode'], 'integer', 'on' => ['add', 'update']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['mode'], 'default', 'value' => self::MODE['warming'], 'on' => ['add']],
            [['url'], 'safe', 'on' => ['add', 'update']],
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

}