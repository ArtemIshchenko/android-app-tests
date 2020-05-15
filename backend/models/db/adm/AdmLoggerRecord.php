<?php

namespace backend\models\db\adm;


use common\components\own\behavior\UserBehavior;
use common\components\own\db\mysql\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;


/**
 * Class AdmLoggerRecord
 * @package backend\models\db\adm
 * @author alhambr
 *
 * AdmLoggerRecord model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $object_id
 * @property string $action
 * @property string $model
 * @property string $before_action
 * @property string $after_action
 * @property string $ip
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class AdmLoggerRecord extends ActiveRecord
{

	const ACTION_ADD = 'add';
	const ACTION_UPDATE = 'update';
	const ACTION_DELETE = 'delete';

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{adm_logger}}';
	}

	public function attributeLabels() {
		return array(
			'id'=>'#',
			'user_id'=>'Администратор',
			'object_id'=>'Обьект',
			'ip'=>'IP',
			'created_at'=>'Время',
			'model'=>'Модель',
			'action'=>'Действие',
			'before_action'=>'Данные',
		);
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id','object_id', 'action', 'model'], 'required', 'on' => ['add']],
			[['user_id', 'object_id'], 'integer', 'on' => ['add']],
			['action', 'in', 'range' => [self::ACTION_ADD, self::ACTION_UPDATE, self::ACTION_DELETE], 'on' => ['add']],
			['ip', 'safe', 'on' => ['add']],
			['ip', 'safe', 'on' => ['add']],
			['model', 'string', 'encoding' => 'UTF-8', 'on' => ['add']],
			[['before_action', 'after_action'], 'string', 'encoding' => 'UTF-8', 'on' => ['add']],
		];
	}


	/**
	 * @description Поиск по выбраным полям
	 * @param $params
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = self::find()->orderBy(['created_at'=>SORT_DESC]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
		]);
		if (!($this->load($params))) {
			return $dataProvider;
		}
		$query->andFilterWhere(['user_id' => $this->user_id]);
		//$query->andFilterWhere(['like', 'ip', $this->ip]);
		$query->andFilterWhere(['ip' => $this->ip]);;
		$query->andFilterWhere(['model' => $this->model]);
		$query->andFilterWhere(['action' => $this->action]);
		return $dataProvider;
	}


	public static function compare($last, $new) {
		$last = unserialize($last);
		$new = unserialize($new);
		$result = @array_diff_assoc($last, $new);
		$change = [];
		if(is_array($result) && !empty($result)) {
			foreach($result as $atr => $value) {
				if(!empty($atr)){
					$n = isset($new[$atr])?$new[$atr]:"";
					$l = isset($last[$atr])?$last[$atr]:"";
					$change[$atr]=[$l,$n];
				}
			}
		}
		return $change;
	}

	/**
	 * Записываем лог
	 * @param $action
	 * @param $class
	 * @param $objectId
	 * @param array $beforeAction
	 * @param array $afterAction
	 * @return bool
	 */
	public static function logger($action, $class, $objectId, $beforeAction = [], $afterAction = [])
	{
		$model = new self();
		$model->scenario = 'add';
		$model->user_id =  \Yii::$app->user->id;
		$model->object_id = $objectId;
		$model->action = (in_array($action,[self::ACTION_ADD, self::ACTION_UPDATE, self::ACTION_DELETE]))?$action:self::ACTION_ADD;
		$model->model = $class;
		$model->before_action = serialize($beforeAction);
		$model->after_action = serialize($afterAction);
		$model->ip = \Yii::$app->request->userIP;
		return $model->save();
	}

}