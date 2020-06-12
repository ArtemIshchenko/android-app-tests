<?php
namespace backend\components\own\statistic;

use backend\models\form\StatisticAndroidFilter;
use common\models\db\LogRecord;
use librariesHelpers\helpers\Type\Type_Cast;

/**
 * Class StatisticAndroidSystem
 * @package backend\components\own\statistic
 * @author asisch
 * @description Обработка и генерация статистики
 */
class StatisticAndroidSystem
{

    /**
     * @description Генерация логов за период
     * @param StatisticAndroidFilter $statisticFilter
     * @return array
     * @throws \Exception
     */
    public static function logs(StatisticAndroidFilter $statisticFilter, $type = 0)
    {
        switch ($type) {
            case 0:
            default:
            return self::_logs1($statisticFilter);
                break;
            case 1:
                return self::_logs2($statisticFilter);
                break;
        }

    }


    protected static function _logs1(StatisticAndroidFilter $statisticFilter)
    {

        $dateTimestamp = $statisticFilter->getDateTimestamp();

        $from = $dateTimestamp['dateFrom'];
        $to = $dateTimestamp['dateTo'];

        $startTime = mktime(0, 0, 0, date('m', $from), date('d', $from), date('Y', $from));
        $endTime = mktime(23, 59, 59, date('m', $to), date('d', $to), date('Y', $to));

        $countLogs = LogRecord::find()
            ->select(['c' => 'COUNT(DISTINCT tt_logs.id)', 'name' => 'tt_deeplinks.name'])
            ->innerJoin('tt_deeplinks', "tt_logs.deeplink = tt_deeplinks.name")
            ->where(['BETWEEN', 'tt_logs.created_at', $startTime, $endTime])
            ->andWhere(['token' => ''])
            ->groupBy('tt_deeplinks.name')
            ->orderBy(['c' => SORT_DESC])
            ->asArray()
            ->all();


        return $countLogs;
    }

    protected static function _logs2(StatisticAndroidFilter $statisticFilter)
    {

        $dateTimestamp = $statisticFilter->getDateTimestamp();

        $from = $dateTimestamp['dateFrom'];
        $to = $dateTimestamp['dateTo'];

        $startTime = mktime(0, 0, 0, date('m', $from), date('d', $from), date('Y', $from));
        $endTime = mktime(23, 59, 59, date('m', $to), date('d', $to), date('Y', $to));

        $countLogs = LogRecord::find()
            ->select(['c' => 'COUNT(DISTINCT tt_logs.id)', 'name' => 'tt_deeplinks.name'])
            ->innerJoin('tt_deeplinks', "tt_logs.deeplink = tt_deeplinks.name")
            ->where(['BETWEEN', 'tt_logs.created_at', $startTime, $endTime])
            ->andWhere(['<>', 'token', ''])
            ->groupBy('tt_deeplinks.name')
            ->orderBy(['c' => SORT_DESC])
            ->asArray()
            ->all();


        return $countLogs;
    }
}
