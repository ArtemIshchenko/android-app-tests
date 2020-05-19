<?php
namespace common\components\own\db\mysql;

use common\components\own\behavior\UserBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

/**
 * Class ActiveRecord
 * @package common\components\own\db\mysql
 * @author alhambr
 * 
 */
class ActiveRecord extends \yii\db\ActiveRecord 
{

    const PAGE_LIMIT = 20;

    protected static $_modelsCache = [];

    public static function findOne($condition, $withOutCache = false)
    {
        if($withOutCache) {
	        static::findByCondition($condition)->one();
        }
    	$models = &static::$_modelsCache;
        if (is_scalar($condition)) {
            $className = static::class;
            $modelsByClass = isset($models[$className]) ? $models[$className] : [];
            $models[$className] = &$modelsByClass;
            if (isset($modelsByClass[$condition])) {
                return $modelsByClass[$condition];
            } else {
                $model = static::findByCondition($condition)->one();
                $modelsByClass[$condition] = $model;
                return $model;
            }
        } elseif(is_array($condition) && !empty($condition)) {
	        $className = static::class;
	        $modelsByClass = isset($models[$className]) ? $models[$className] : [];
	        $models[$className] = &$modelsByClass;
	        $tmpCondition = md5(serialize($condition));
	        if (isset($modelsByClass[$tmpCondition])) {
		        return $modelsByClass[$tmpCondition];
	        } else {
		        $model = static::findByCondition($condition)->one();
		        $modelsByClass[$tmpCondition] = $model;
		        return $model;
	        }
        } else {
            return static::findByCondition($condition)->one();
        }
    }


    /**
     * @description Поиск по выбраным полям
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
	        'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
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
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
			UserBehavior::className()
		];
	}

}