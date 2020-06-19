<?php
namespace common\models\db;

use common\components\own\db\mysql\ActiveRecord;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * Class TestRecord
 * @package common\models\db
 * @author asisch
 *
 * TestRecord model
 *
 * @property integer $id
 * @property string $name
 * @property string $structure
 * @property string $image
 * @property integer $fb_event
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class TestRecord extends ActiveRecord
{

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const FB_EVENT = [
        'achieveLevel' => 1,
        'adClick' => 2,
        'adImpression' => 3,
        'addPaymentInfo' => 4,
        'completeRegistration' => 7,
        'contact' => 9,
    ];

    const APP_TESTS = [
        1 => 'В ПОРЯДКЕ ЛИ ВАШИ НЕРВЫ?',
        2 => 'ЧТО О ВАС ДУМАЮТ ДРУГИЕ ЛЮДИ',
        3 => 'БОЛЬШАЯ ЛЮБОВЬ ИЛИ ЛЕГКОЕ УВЛЕЧЕНИЕ?',
        4 => 'ТЕСТ "ВАЖНОСТЬ СЕКСА ДЛЯ ВАС"',
        5 => 'УЗНАЙТЕ, КАКИЕ У ВАС СКРЫТЫЕ ТАЛАНТЫ',
        6 => 'КАК СИЛЬНО ВЫ СЕБЯ ЛЮБИТЕ',
        7 => 'Тест на зависимость от телевидения',
        8 => 'ТЕСТ НА ПЕДАНТИЧНОСТЬ',
        9 => 'ТЕСТ "НАСКОЛЬКО ВЫ ТЕРПИМЫ К УБЕЖДЕНИЯМ ДРУГИХ"',
        10 => 'ТЕСТ "СТРАСТЬ К ПУТЕШЕСТВИЯМ"',
        11 => 'ТЕСТ "СПОСОБНЫ ЛИ ВЫ БЫТЬ ПОБЕДИТЕЛЕМ"',
        12 => 'ТЕСТ "СКЛОННОСТЬ К САМОРАЗРУШЕНИЮ"',
        13 => 'ТЕСТ НА РЕВНИВОСТЬ',
        14 => 'Тест на рассеянность',
        15 => 'Тест "Подходит ли вам место работы?"',
        16 => 'Тест "Подвержены ли Вы стрессу?"',
        17 => 'Тест "На сколько вы застенчивы?"',
        18 => 'ТЕСТ «Умеете ли вы себя контролировать?»',
        19 => 'Тест «Психологическая устойчивость» для мужчин и парней',
        20 => 'Самостоятельны ли вы?',
        21 => 'Хороший ли Вы водитель?',
    ];

    public $structForCheck = '';

    public static function tableName()
    {
        return '{{tt_tests}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name', 'structure'], 'required', 'enableClientValidation' => false, 'on' => ['add', 'update']],
            [['name'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['structure', 'image'], 'string', 'on' => ['add', 'update']],
            [['image'], 'checkImgResolution', 'on' => ['add', 'update']],
            [['structure'], 'convStruct', 'on' => ['add', 'update']],
            [['structForCheck'], 'checkStruct', 'on' => ['check']],
            [['is_active', 'fb_event'], 'integer', 'on' => ['add', 'update']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['fb_event'], 'default', 'value' => 0, 'on' => ['add']],
            [['is_active'], 'integer', 'on' => ['change-active']],
        ];
    }

    public function checkImgResolution($attribute, $params) {
        if ($this->scenario != 'change-active') {
            if (!empty($this->$attribute)) {
                $img = \Yii::$app->params['testPathDir'] . '/' . $this->$attribute;
                $size = getimagesize($img);
                if ($size) {
                    if (($size[0] > 300) || ($size[1] > 300)) {
                        $this->addError($attribute, 'Размер изображения должен быть не более 300х300 px');
                    }
                }
            }
        }
    }

    public function checkStruct($attribute, $params) {
        if ($this->scenario != 'change-active') {
            if (!empty($this->$attribute)) {
                $attr = $this->$attribute;
                try {
                    eval("\$structure = $attr;");
                } catch (\Throwable $e) {
                    $this->addError($attribute, 'Неверный синтаксис массива - проверьте наличие всех скобок и запятые после закрывающих скобок');
                }

            }
        }
    }

    public function convStruct($attribute, $params) {
        if ($this->scenario != 'change-active') {
            if (!empty($this->$attribute)) {
                $attr = $this->$attribute;
                $structure = [];
                try {
                    eval("\$structure = $attr;");
                } catch (\Throwable $e) {
                    $this->addError($attribute, 'Неверная структура массива - проверьте наличие всех скобок и запятые после закрывающих скобок');
                }

                if (!$this->hasErrors() && !empty($structure)) {
                    if (!isset($structure['id']) || empty($structure['id'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле id теста');
                    }
                    if (!isset($structure['title']) || empty($structure['title'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле title теста');
                    }
                    if (!isset($structure['description']) || empty($structure['description'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле description теста');
                    }
                    if (!isset($structure['questions']) || empty($structure['questions'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле questions теста');
                    }
                    if (!isset($structure['imageAnswer']) || empty($structure['imageAnswer'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле imageAnswer теста');
                    }
                    if (!isset($structure['timerSetting']) || empty($structure['timerSetting'])) {
                        $this->addError($attribute, 'Неверная структура массива - заполните поле timerSetting теста');
                    }
//                    if (!isset($structure['results']) || empty($structure['results'])) {
//                        $this->addError($attribute, 'Неверная структура массива - заполните поле results теста');
//                    }
                    if (!$this->hasErrors()) {
                        foreach ($structure['questions'] as $i => $question) {
                            if (!isset($question['number']) || empty($question['number'])) {
                                $this->addError($attribute, 'Неверная структура массива - заполните поле number вопроса ' . ($i + 1) . ' теста');
                            }
                            if (!isset($question['text']) || empty($question['text'])) {
                                $this->addError($attribute, 'Неверная структура массива - заполните поле text вопроса ' . ($i + 1) . ' теста');
                            }
                            if (!isset($question['answers']) || empty($question['answers'])) {
                                $this->addError($attribute, 'Неверная структура массива - заполните поле answers теста ' . ($i + 1) . ' теста');
                            }
                            if (!$this->hasErrors()) {
                                foreach ($question['answers'] as $j => $answer) {
                                    if (!isset($answer['number']) || empty($answer['number'])) {
                                        $this->addError($attribute, 'Неверная структура массива - заполните поле number ответа ' . ($j + 1) . ' вопроса ' . ($i + 1) . ' теста');
                                    }
                                    if (!isset($answer['text']) || empty($answer['text'])) {
                                        $this->addError($attribute, 'Неверная структура массива - заполните поле text ответа ' . ($j + 1) . ' вопроса ' . ($i + 1) . ' теста');
                                    }
                                    if (!isset($answer['isSignal'])) {
                                        $this->addError($attribute, 'Неверная структура массива - заполните поле isSignal ответа ' . ($j + 1) . ' вопроса ' . ($i + 1) . ' теста');
                                    }
                                    if (!isset($answer['rating'])) {
                                        $this->addError($attribute, 'Неверная структура массива - заполните поле rating ответа ' . ($j + 1) . ' вопроса ' . ($i + 1) . ' теста');
                                    }
                                }
                            }
                        }
                    }

//                    if (!$this->hasErrors()) {
//                        foreach ($structure['results'] as $k => $result) {
//                            if (!isset($result['min']) || (!is_numeric($result['min']))) {
//                                $this->addError($attribute, 'Неверная структура массива - заполните поле min результата теста ' . ($k + 1));
//                            }
//                            if (!isset($result['max']) || (!is_numeric($result['max']))) {
//                                $this->addError($attribute, 'Неверная структура массива - заполните поле max результата теста ' . ($k + 1));
//                            }
//                            if (!isset($result['text']) || empty($result['text'])) {
//                                $this->addError($attribute, 'Неверная структура массива - заполните поле text результата теста ' . ($k + 1));
//                            }
//                        }
//                    }

                    if (!$this->hasErrors()) {
                        if (preg_match('/\]\s*\[/m', $attr)) {
                            $this->addError($attribute, 'Неверная структура массива - пропущены запятые после закрывающих скобок');
                        }
                    }
                    if (!$this->hasErrors()) {
                        $this->$attribute = json_encode($structure);
                    }
                }

            }
        }
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
            'structForCheck' => 'Структура для проверки',
            'structure' => 'Структура',
            'image' => 'Изображение',
            'fb_event' => 'Стандартное событие фейсбук',
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
     * @description имя по ид
     * @param integer $id
     * @return string | false
     */
    public static function getNameById($id) {
        $model = self::findOne($id);
        if (!is_null($model) && !empty($model)) {
            return $model->name;
        }
        return false;
    }

    /**
     * @description пробразование из json-строки в строку для отображения
     * @return void
     */
    public function convReverse() {
        $structure = json_decode($this->structure, true);
        $content = '';
        if (!empty($structure)) {
            $content .= '[' . "\n";
            $structureId = isset($structure['id']) ? $structure['id'] : '';
            $content .= "\t" . '"id" => ' . $structureId . ",\n";
            $title = isset($structure['title']) ? $structure['title'] : '';
            $title = str_replace('"', '\"', $title);
            $content .= "\t" . '"title" => "' . $title . "\",\n";
            $description = isset($structure['description']) ? $structure['description'] : '';
            $description = str_replace('"', '\"', $description);
            $content .= "\t" . '"description" => "' . $description . "\",\n";
            $imageAnswer = isset($structure['imageAnswer']) ? $structure['imageAnswer'] : '';
            $imageAnswer = str_replace('"', '\"', $imageAnswer);
            $content .= "\t" . '"imageAnswer" => "' . $imageAnswer . "\",\n";
            $timerSetting = isset($structure['timerSetting']) ? $structure['timerSetting'] : 0;
            $content .= "\t" . '"timerSetting" => ' . $timerSetting . ",\n";

            $content .= "\t" . '"questions" => ' . "[\n";
            if (isset($structure['questions']) && !empty($structure['questions'])) {
                foreach ($structure['questions'] as $question) {
                    $content .= "\t\t" . "[\n";
                    $questionNumber = isset($question['number']) ? $question['number'] : '';
                    $content .= "\t\t\t" . '"number" => ' . $questionNumber . ",\n";
                    $questionText = isset($question['text']) ? $question['text'] : '';
                    $questionText = str_replace('"', '\"', $questionText);
                    $content .= "\t\t\t" . '"text" => "' . $questionText . "\",\n";

                    $content .= "\t\t\t" . '"answers" => ' . "[\n";
                    if (isset($question['answers']) && !empty($question['answers'])) {
                        foreach ($question['answers'] as $answer) {
                            $content .= "\t\t\t\t" . "[\n";
                            $answerNumber = isset($answer['number']) ? $answer['number'] : '';
                            $content .= "\t\t\t\t\t" . '"number" => ' . $answerNumber . ",\n";
                            $answerText = isset($answer['text']) ? $answer['text'] : '';
                            $answerText = str_replace('"', '\"', $answerText);
                            $content .= "\t\t\t\t\t" . '"text" => "' . $answerText . "\",\n";
                            $answerIsSignal = isset($answer['isSignal']) ? $answer['isSignal'] : false;
                            $content .= "\t\t\t\t\t" . '"isSignal" => ' . $answerIsSignal . ",\n";
                            $answerRating = isset($answer['rating']) ? $answer['rating'] : '';
                            $content .= "\t\t\t\t\t" . '"rating" => ' . $answerRating . "\n";
                            $content .= "\t\t\t\t],\n";
                        }
                    }
                    $content .= "\t\t\t]\n";
                    $content .= "\t\t],\n";
                }
            }
            $content .= "\t]\n";

            $content .= "]";
        }
        $this->structure = $content;
    }

    /**
     * @description сттруктура теста в виде массива
     * @return array
     */
    public function getStructure() {
        return json_decode($this->structure, true);
    }

    /**
     * @description Тесты
     * @return array
     */
    public static function getTestList() {
        $list = ArrayHelper::map(self::find()->where(['is_active' => self::IS_ACTIVE])->orderBy(['id' => SORT_ASC])->all(), 'id', 'name');
        return $list;
    }

    /**
     * @description Тесты из приложения
     * @return array
     */
    public static function getAppTestList() {
        return self::APP_TESTS;
    }

    /**
     * @description Серые тесты
     * @return array
     */
    public static function getGreyTestList() {
        $tests = [];
        $testModels = self::find()->where(['is_active' => self::IS_ACTIVE])->orderBy(['id' => SORT_ASC])->all();
        if (!is_null($testModels) && !empty($testModels)) {
            foreach ($testModels as $testModel) {
                $structure = $testModel->getStructure();
                if (!empty($structure)) {
                    $tests[$structure['id']] = $structure['title'];
                }
            }
        }
        return $tests;
    }

    /**
     * @description Стандартные события фейсбук
     * @return array
     */
    public static function getFbEventList() {
        return [
            self::FB_EVENT['achieveLevel'] => 'Achieve Level',
            self::FB_EVENT['adClick'] => 'Ad Click',
            self::FB_EVENT['adImpression'] => 'Ad Impression',
            self::FB_EVENT['addPaymentInfo'] => 'Add Payment Info',
            self::FB_EVENT['completeRegistration'] => 'Complete Registration',
            self::FB_EVENT['contact'] => 'Contact',
        ];
    }

    /**
     * @description Url картинки
     * @param string $name
     * @return string
     */
    public static function getImageUrl($name) {
        $destination = \Yii::$app->params['testWebDir'];
        if (substr($destination, -1) != '/') {
            $destination .= '/';
        }
        return $destination . $name;
    }
}