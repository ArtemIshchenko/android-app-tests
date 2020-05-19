<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;

/**
 * Class SettingRecord
 * @package common\models\db
 * @author asisch
 *
 * SettingRecord model
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $value
 * @property integer $number
 * @property integer $section
 * @property integer $created_at
 * @property integer $updated_at
 */
class SettingRecord extends ActiveRecord
{

    const TYPE = [
        'input' => 1,
        'checkbox' => 2,
    ];

    const SECTION = [
        'main' => 1,
    ];

    const SETTINGS = [
        1 => ['name' => 'showAdvertising', 'section' => self::SECTION['main'], 'description' => 'Отображать рекламу', 'type' => self::TYPE['checkbox'],  'defaultValue' => 1],
        2 => ['name' => 'showCommentGpWidget', 'section' => self::SECTION['main'], 'description' => 'Отображать виджет для создания отзывов в гп', 'type' => self::TYPE['checkbox'],  'defaultValue' => 1],
    ];

    public static function tableName()
    {
        return '{{tt_settings}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name', 'section'], 'required', 'on' => ['add', 'update']],
            [['name', 'description'], 'string', 'on' => ['add', 'update']],
            [['number', 'value', 'section'], 'integer', 'on' => ['add', 'update']],
            [['number'], 'integer', 'on' => ['set-number']],
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
            'name' => 'Наименование параметра',
            'description' => 'Описание параметра',
            'value' => 'Значение',
            'number' => 'Номер по порядку',
            'section' => 'Секция',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * настройки
     * @return array
     */
    public static function getSettings()
    {
        $result = [];
        $settings = self::find()->orderBy(['section' => SORT_ASC, 'number' => SORT_ASC])->indexBy('id')->all();
        if (empty($settings)) {
            foreach (self::SECTION as $sectionName => $sectionCode) {
                foreach (self::SETTINGS as $number => $item) {
                    $model = new self;
                    $model->setScenario('add');
                    $model->name = $item['name'];
                    $model->description = $item['description'];
                    $model->value = $item['defaultValue'];
                    $model->number = $number;
                    $model->section = $sectionCode;
                    if ($model->save()) {
                        $model->setScenario('update');
                        $result[$model->id] = $model;
                    }
                }
            }
        } else {
            foreach (self::SECTION as $sectionName => $sectionCode) {
                foreach (self::SETTINGS as $number => $item) {
                    $isSet = false;
                    foreach ($settings as $setting) {
                        if (($setting->section == $sectionCode) && ($setting->name == $item['name'])) {
                            if ($setting->number != $number) {
                                $setting->setScenario('set-number');
                                $setting->number = $number;
                                $setting->save();
                            }
                            $isSet = true;
                            $setting->setScenario('update');
                            $result[$setting->id] = $setting;
                            break;
                        }
                    }
                    if (!$isSet) {
                        $model = new self;
                        $model->setScenario('add');
                        $model->name = $item['name'];
                        $model->description = $item['description'];
                        $model->value = $item['defaultValue'];
                        $model->number = $number;
                        $model->section = $sectionCode;
                        if ($model->save()) {
                            $model->setScenario('update');
                            $result[$model->id] = $model;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * настройки
     * @param integer $section
     * @param string $byId
     * @return array
     */
    public static function getSettingList($section, $byId = true)
    {
        $res = [];
        $settings = self::find()->orderBy(['section' => SORT_ASC, 'number' => SORT_ASC])->all();
        if (empty($settings)) {
            foreach (self::SECTION as $sectionName => $sectionCode) {
                $settingsOneSection = [];
                foreach (self::SETTINGS as $number => $item) {
                    $model = new self;
                    $model->setScenario('add');
                    $model->name = $item['name'];
                    $model->description = $item['description'];
                    $model->value = $item['defaultValue'];
                    $model->number = $number;
                    $model->section = $sectionCode;
                    if ($model->save()) {
                        $settingsOneSection[] = $model;
                    }
                }
                if (!empty($settingsOneSection)) {
                    if ($byId) {
                        $result[$sectionCode] = ArrayHelper::map($settingsOneSection, 'id', 'value');
                    } else {
                        $result[$sectionCode] = ArrayHelper::map($settingsOneSection, 'name', 'value');
                    }
                }
            }
        } else {
            foreach (self::SECTION as $sectionName => $sectionCode) {
                foreach (self::SETTINGS as $number => $item) {
                    $isSet = false;
                    foreach ($settings as $setting) {
                        if (($setting->section == $sectionCode) && ($setting->name == $item['name'])) {
                            if ($setting->number != $number) {
                                $setting->setScenario('set-number');
                                $setting->number = $number;
                                $setting->save();
                            }
                            $isSet = true;
                            if ($byId) {
                                $result[$sectionCode][$setting->id] = $setting->value;
                            } else {
                                $result[$sectionCode][$setting->name] = $setting->value;
                            }
                            break;
                        }
                    }
                    if (!$isSet) {
                        $model = new self;
                        $model->setScenario('add');
                        $model->name = $item['name'];
                        $model->description = $item['description'];
                        $model->value = $item['defaultValue'];
                        $model->number = $number;
                        $model->section = $sectionCode;
                        if ($model->save()) {
                            if ($byId) {
                                $result[$sectionCode][$model->id] = $model->value;
                            } else {
                                $result[$sectionCode][$model->name] = $model->value;
                            }
                        }
                    }
                }
            }
        }

        if (isset($result[$section])) {
            $res = $result[$section];
        }
        return $res;
    }

    /**
     * настройки
     * @param string $name
     * @return integer | false
     */
    public static function getTypeByName($name)
    {
        $type = false;
        foreach (self::SETTINGS as $setting) {
            if ($setting['name'] == $name) {
                $type = $setting['type'];
                break;
            }
        }
        return $type;
    }

    /**
     * описание по ид
     * @param integer $id
     * @return string
     */
    public static function getDescriptionById($id)
    {
        $description = '';
        $settingModel = self::findOne($id);
        if (!is_null($settingModel) && !empty($settingModel)) {
            foreach (self::SETTINGS as $setting) {
                if ($settingModel->name == $setting['name']) {
                    $description = $setting['description'];
                    break;
                }
            }
        }
        return $description;
    }

    /**
     * ид по name
     * @param string $name
     * @param integer $section
     * @return integer
     */
    public static function getIdByName($name, $section)
    {
        $id = 0;
        $settingModel = self::findOne(['name' => $name, 'section' => $section]);
        if (!is_null($settingModel) && !empty($settingModel)) {
            $id = $settingModel->id;
        }
        return $id;
    }

    /**
     * значение по name
     * @param string $name
     * @param integer $section
     * @return string | false
     */
    public static function getValByName($name, $section)
    {
        $val = false;
        $settingModel = self::findOne(['name' => $name, 'section' => $section]);
        if (!is_null($settingModel) && !empty($settingModel)) {
            $val = $settingModel->value;
        }
        return $val;
    }

    /**
     * тип по ид
     * @param integer $id
     * @return integer
     */
    public static function getTypeById($id)
    {
        $type = 0;
        $settingModel = self::findOne($id);
        if (!is_null($settingModel) && !empty($settingModel)) {
            foreach (self::SETTINGS as $setting) {
                if ($settingModel->name == $setting['name']) {
                    $type = $setting['type'];
                    break;
                }
            }
        }
        return $type;
    }

    public function afterFind() {
        parent::afterFind();
        if (is_numeric($this->value)) {
            $this->value = (int) $this->value;
        }
    }

}