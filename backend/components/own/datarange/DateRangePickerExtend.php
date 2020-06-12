<?php

namespace backend\components\own\datarange;

use kartik\daterange\DateRangePicker;
use Yii;
use yii\web\JsExpression;
use yii\base\InvalidConfigException;

class DateRangePickerExtend extends DateRangePicker
{

	protected function initRange()
	{
		if (isset($dummyValidation)) {
			/** @noinspection PhpUnusedLocalVariableInspection */
			$msg = Yii::t('kvdrp', 'Select Date Range');
		}
		$m = 'moment()';
		if ($this->presetDropdown) {
			$this->initRangeExpr = $this->hideInput = true;
			$this->pluginOptions['opens'] = 'left';
			$this->pluginOptions['ranges'] = [
				Yii::t('kvdrp', 'Today') => ["{$m}.startOf('day')", "{$m}.endOf('day')"],
				Yii::t('kvdrp', 'Yesterday') => [
					"{$m}.startOf('day').subtract(1,'days')",
					"{$m}.endOf('day').subtract(1,'days')",
				],
				Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["{$m}.startOf('day').subtract(6, 'days')", "{$m}.endOf('day')"],
				Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["{$m}.startOf('day').subtract(29, 'days')", "{$m}.endOf('day')"],
				Yii::t('kvdrp', 'This Month') => ["{$m}.startOf('month')", "{$m}.endOf('month')"],
				Yii::t('kvdrp', 'Last Month') => [
					"{$m}.subtract(1, 'month').startOf('month')",
					"{$m}.subtract(1, 'month').endOf('month')",
				],
			];
			if (empty($this->value)) {
				$this->pluginOptions['startDate'] = new JsExpression("{$m}.startOf('day')");
				$this->pluginOptions['endDate'] = new JsExpression($m);
			}
		}
		$opts = $this->pluginOptions;
		if (!$this->initRangeExpr || empty($opts['ranges']) || !is_array($opts['ranges'])) {
			return;
		}
		$range = [];
		foreach ($opts['ranges'] as $key => $value) {
			if (!is_array($value) || empty($value[0]) || empty($value[1])) {
				throw new InvalidConfigException(
					"Invalid settings for pluginOptions['ranges']. Each range value must be a two element array."
				);
			}
			$range[$key] = [static::parseJsExpr($value[0]), static::parseJsExpr($value[1])];
		}
		$this->pluginOptions['ranges'] = $range;
	}

}