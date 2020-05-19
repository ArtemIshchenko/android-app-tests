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
 * @property integer $is_active
 * @property integer $created_at
 * @property integer $updated_at
 */
class TestRecord extends ActiveRecord
{

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    const APP_TESTS = [1 => 'В порядке ли ваши нервы?', 2 => 'ЧТО О ВАС ДУМАЮТ ДРУГИЕ ЛЮДИ'];

    public static function tableName()
    {
        return '{{tt_tests}}';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name', 'structure'], 'required', 'on' => ['add', 'update']],
            [['name'], 'string', 'max' => 256, 'on' => ['add', 'update']],
            [['structure'], 'string', 'on' => ['add', 'update']],
            [['structure'], 'convStruct', 'on' => ['add', 'update']],
            [['is_active'], 'integer', 'on' => ['add', 'update']],
            [['is_active'], 'default', 'value' => self::IS_ACTIVE, 'on' => ['add']],
            [['is_active'], 'integer', 'on' => ['change-active']],
        ];
    }

    public function convStruct($attribute, $params) {
        if ($this->scenario != 'change-active') {
            if (!empty($this->$attribute)) {
                $attr = $this->$attribute;
                $structure = [];
                eval("\$structure = $attr;");
                if (!empty($structure)) {
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
                                }
                            }
                        }
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
            'structure' => 'Структура',
            'is_active' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
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
            $content .= "\t" . '"title" => "' . $title . "\",\n";
            $description = isset($structure['description']) ? $structure['description'] : '';
            $content .= "\t" . '"description" => "' . $description . "\",\n";

            $content .= "\t" . '"questions" => ' . "[\n";
            if (isset($structure['questions']) && !empty($structure['questions'])) {
                foreach ($structure['questions'] as $question) {
                    $content .= "\t\t" . "[\n";
                    $questionNumber = isset($question['number']) ? $question['number'] : '';
                    $content .= "\t\t\t" . '"number" => ' . $questionNumber . ",\n";
                    $questionText = isset($question['text']) ? $question['text'] : '';
                    $content .= "\t\t\t" . '"text" => "' . $questionText . "\",\n";

                    $content .= "\t\t\t" . '"answers" => ' . "[\n";
                    if (isset($question['answers']) && !empty($question['answers'])) {
                        foreach ($question['answers'] as $answer) {
                            $content .= "\t\t\t\t" . "[\n";
                            $answerNumber = isset($answer['number']) ? $answer['number'] : '';
                            $content .= "\t\t\t\t\t" . '"number" => ' . $answerNumber . ",\n";
                            $answerText = isset($answer['text']) ? $answer['text'] : '';
                            $content .= "\t\t\t\t\t" . '"text" => "' . $answerText . "\",\n";
                            $answerIsSignal = isset($answer['isSignal']) ? $answer['isSignal'] : false;
                            $content .= "\t\t\t\t\t" . '"isSignal" => ' . $answerIsSignal . "\n";
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

}