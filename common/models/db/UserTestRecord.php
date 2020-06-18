<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class UserTestRecord
 * @package common\models\db
 * @author asisch
 *
 * UserTestRecord model
 *
 * @property integer $id
 * @property string $device_id
 * @property string $lang
 * @property string $deeplink
 * @property string $firebase_token
 * @property integer $app_test_id
 * @property integer $test_id
 * @property integer $app_state
 * @property integer $test_state
 * @property integer $show_ads_state
 * @property integer $show_rating_state
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserTestRecord extends ActiveRecord
{

    const APP_STATE = [
        'white' => 0,
        'grey' => 1,
    ];

    const TEST_STATE = [
        'notStart' => 0,
        'startNotFinish' => 1,
        'finishNotBtn' => 2,
        'finishBtn' => 3,
    ];

    const SHOW_ADS = [
        'notShow' => 0,
        'show' => 1
    ];

    const SHOW_REATING = [
        'notShow' => 0,
        'show' => 1
    ];

    public static function tableName()
    {
        return '{{tt_users_tests}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['device_id'], 'required', 'on' => ['add', 'update']],
            [['device_id'], 'unique', 'targetAttribute' => ['device_id'], 'on' => ['add', 'update']],
            [['device_id', 'lang', 'deeplink', 'firebase_token'], 'string', 'on' => ['add', 'update']],
            [['app_test_id', 'test_id', 'app_state', 'test_state', 'show_ads_state', 'show_rating_state'], 'integer', 'on' => ['add', 'update']],
            [['app_test_id', 'test_id'], 'default', 'value' => 0, 'on' => ['add']],
            [['app_state'], 'default', 'value' => self::APP_STATE['white'], 'on' => ['add']],
            [['test_state'], 'default', 'value' => self::TEST_STATE['notStart'], 'on' => ['add']],
            [['show_ads_state'], 'default', 'value' => self::SHOW_ADS['notShow'], 'on' => ['add']],
            [['show_rating_state'], 'default', 'value' => self::SHOW_REATING['notShow'], 'on' => ['add']],
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
            'lang' => 'Язык устройства',
            'deeplink' => 'Диплинк пользователя',
            'firebase_token' => 'Токен Firebase',
            'app_test_id' => 'Ид белого теста',
            'test_id' => 'Ид серого теста',
            'app_state' => 'Сотояние приложения',
            'test_state' => 'Сотояние прохождения теста',
            'show_ads_state' => 'Состояние показывать рекламу или нет',
            'show_rating_state' => 'Состояние показывать оценки с рейтингом или нет',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @description статистика
     * @param string $deviceId
     * @param string $lang
     * @param string $deeplink
     * @param string $testId
     * @param integer $appState
     * @param integer $testState
     * @param integer $showAdsState
     * @param integer $showRatingState
     * @return bool
     */
    public static function setStatistic($deviceId, $lang, $deeplink, $firebaseToken, $whiteTestId, $testId, $appState, $testState, $showAdsState, $showRatingState) {
        $userTest = self::findOne(['device_id' => $deviceId]);
        if (!is_null($userTest) && !empty($userTest)) {
            $userTest->setScenario('update');
            $userTest->lang = $lang;
            $userTest->deeplink = $deeplink;
            $userTest->firebase_token = $firebaseToken;
            $userTest->app_test_id = $whiteTestId;
            $userTest->test_id = $testId;
            $userTest->app_state = $appState;
            $userTest->test_state = $testState;
            $userTest->show_ads_state = $showAdsState;
            $userTest->show_rating_state = $showRatingState;
        } else {
            $userTest = new self;
            $userTest->setScenario('add');
            $userTest->device_id = $deviceId;
            $userTest->lang = $lang;
            $userTest->deeplink = $deeplink;
            $userTest->firebase_token = $firebaseToken;
            $userTest->app_test_id = $whiteTestId;
            $userTest->test_id = $testId;
            $userTest->app_state = $appState;
            $userTest->test_state = $testState;
            $userTest->show_ads_state = $showAdsState;
            $userTest->show_rating_state = $showRatingState;
        }

        return $userTest->save();
    }

}