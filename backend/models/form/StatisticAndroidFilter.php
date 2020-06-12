<?php

namespace backend\models\form;

use librariesHelpers\helpers\Utf8\Utf8;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 * @property array $dateTimestamp
 */
class StatisticAndroidFilter extends Model
{

    public $dateRange;
	public $pageSize = 50;

	public function init()
	{
		$this->dateRange = date("d-m-Y", strtotime('-1 day')).' - '.date("d-m-Y H:i", self::getLastDayTimeStamp()); //за 7 дней
		parent::init();
	}

	public function attributeLabels()
	{
		return [
			'dateRange' => 'Дата',
			'pageSize' => 'Кол-во записей',
		];
	}

	public function rules(){
		return [
			[['dateRange','pageSize'], 'safe']
		];
	}

    /**
     * @param bool $dateRange
     * @return array
     */
	public function getDateTimestamp()
    {
        if (!empty($this->dateRange)) {
            $dateRange = Utf8::explode(' - ', $this->dateRange);
            $dateFrom = strtotime($dateRange[0]);
            $dateTo = strtotime($dateRange[1]);
            if (date("H", $dateTo) == '00') {
                $dateTo += 86400;
            }
            $dateTo = self::getLastDayTimeStamp($dateTo);
            $result['dateFrom'] = $dateFrom;
            $result['dateTo'] = $dateTo;
        } else {
            $currentDayStart = self::getCurrentDayTimeStamp();
            $result = [
                'dateFrom' => self::getCurrentDayTimeStamp(),
                'dateTo' => self::getLastDayTimeStamp($currentDayStart + 86400)
            ];
        }
		return $result;
	}

	public static function getCurrentDayTimeStamp($time=null)
	{
		$time = !empty($time) ? $time : time();
		return mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));
	}

	public static function getLastDayTimeStamp($time=null)
	{
		$processedTime = !empty($time) ? $time : time();
		if(!is_null($time)) {
			if (date("H", $time) != '23' && date("i", $time) != '59') {
				return $time;
			}
		}
		return mktime(23, 59, 0, date('m', $processedTime), date('d', $processedTime), date('Y', $processedTime));
	}

	public static function getPageSize()
	{
		return [0 => 'Все записи', 10 => 10, 20 => 20, 50 => 50, 100 => 100, 150 => 150, 200 => 200, 250 => 250, 500 => 500, 1000 => 1000];
	}

}