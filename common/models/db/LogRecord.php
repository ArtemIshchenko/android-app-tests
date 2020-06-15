<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class LogRecord
 * @package common\models\db
 * @author asisch
 *
 * LogRecord model
 *
 * @property integer $id
 * @property string $device_id
 * @property string $deeplink
 * @property string $lang
 * @property string $token
 * @property integer $test_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class LogRecord extends ActiveRecord
{

    const PAGE_LIMIT1 = 100;

    public static function tableName()
    {
        return '{{tt_logs}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['device_id'], 'required', 'on' => ['add', 'update']],
            [['device_id', 'deeplink', 'lang', 'token'], 'string', 'on' => ['add', 'update']],
            [['test_id'], 'integer', 'on' => ['add', 'update']],
            [['test_id'], 'default', 'value' => 0, 'on' => ['add', 'update']],
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
            'device_id' => 'Ид устройства',
            'deeplink' => 'Диплинк',
            'lang' => 'Язык',
            'token' => 'Токен firebase',
            'test_id' => 'Ид теста',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @description Регистрация
     * @param $string $deeplink
     * @param $string $lang
     * @param $string $token
     * @param $int $testId
     * @return bool
     */
    public static function register($deviceId, $deeplink = '', $lang = '', $token = '', $testId = 0)
    {
        $log = new self;
        $log->setScenario('add');
        $log->device_id = $deviceId;
        $log->deeplink = $deeplink;
        $log->lang = $lang;
        $log->token = $token;
        $log->test_id = $testId;

        return $log->save();
    }

    /**
     * @description Поиск по выбраным полям
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $dateFrom = isset($params['dateFrom']) ? Type_Cast::toUInt($params['dateFrom']) : -1;
        $dateTo = isset($params['dateTo']) ? Type_Cast::toUInt($params['dateTo']) : -1;
        $type = isset($params['type']) ? Type_Cast::toUInt($params['type']) : -1;

        $query = self::find();
        if (($dateFrom > -1) && ($dateTo > -1)) {
            $query->where(['BETWEEN', 'created_at', $dateFrom, $dateTo]);
        }
        if ($type == 1) {
            $query->andWhere(['<>', 'token', '']);
        } else {
            $query->andWhere(['token' => '']);
        }
        $query->groupBy('device_id');



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => self::PAGE_LIMIT1,
            ],
        ]);
        if (!($this->load($params))) {
            return $dataProvider;
        }
        return $dataProvider;
    }

}