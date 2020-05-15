<?php
/**
 * Created by PhpStorm.
 * User: alhambr
 * Date: 23.01.18
 * Time: 11:51
 */

namespace backend\components\own\select2;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

class Select2 extends \kartik\select2\Select2
{
	/**
	 * Initializes and renders the widget
	 */
	public function renderWidget()
	{
		$this->initI18N(__DIR__);
		$this->pluginOptions['theme'] = $this->theme;
		$multiple = ArrayHelper::getValue($this->pluginOptions, 'multiple', false);
		unset($this->pluginOptions['multiple']);
		$multiple = ArrayHelper::getValue($this->options, 'multiple', $multiple);
		$this->options['multiple'] = $multiple;
		if (!empty($this->addon) || empty($this->pluginOptions['width'])) {
			$this->pluginOptions['width'] = '100%';
		}
		if ($this->hideSearch) {
			$this->pluginOptions['minimumResultsForSearch'] = new JsExpression('Infinity');
		}
		$this->initPlaceholder();
		if (!isset($this->data)) {
			if (!isset($this->value) && !isset($this->initValueText)) {
				$this->data = [];
			} else {
				if ($multiple) {
					$key = isset($this->value) && is_array($this->value) ? $this->value : [];
				} else {
					$key = isset($this->value) ? $this->value : '';
				}
				$val = isset($this->initValueText) ? $this->initValueText : $key;
				$this->data = $multiple ? array_combine((array)$key, (array)$val) : [$key => $val];
			}
		}
		Html::addCssClass($this->options, 'form-control');
		$this->initLanguage('language', true);
		$this->renderToggleAll();
		$this->registerAssets();
		$this->renderInput();
	}

	/**
	 * Registers the client assets for [[Select2]] widget.
	 */
	public function registerAssets()
	{
		$id = $this->options['id'];
		$this->registerAssetBundle();
		$isMultiple = isset($this->options['multiple']) && $this->options['multiple'];
		$options = Json::encode([
			'themeCss' => ".select2-container--{$this->theme}",
			'sizeCss' => empty($this->addon) && $this->size !== self::MEDIUM ? 'input-' . $this->size : '',
			'doReset' => static::parseBool($this->changeOnReset),
			'doToggle' => static::parseBool($isMultiple && $this->showToggleAll),
			'doOrder' => static::parseBool($isMultiple && $this->maintainOrder),
		]);
		$this->_s2OptionsVar = 's2options_' . hash('crc32', $options);
		$this->options['data-s2-options'] = $this->_s2OptionsVar;
		$view = $this->getView();
		$view->registerJs("var {$this->_s2OptionsVar} = {$options};", View::POS_END);
		if ($this->maintainOrder) {
			$val = Json::encode(is_array($this->value) ? $this->value : [$this->value]);
			$view->registerJs("initS2Order('{$id}',{$val});", View::POS_END);
		}
		$this->registerPlugin($this->pluginName, "jQuery('#{$id}')", "initS2Loading('{$id}','{$this->_s2OptionsVar}')");
	}
}