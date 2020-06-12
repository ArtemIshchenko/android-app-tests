<?php

use backend\components\own\datarange\DateRangePickerExtend;
use backend\models\form\StatisticAndroidFilter;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>
<?php $form = ActiveForm::begin([
	'method' => 'get',
	'enableAjaxValidation'   => false,
    'id' => 'form-filter',
	'action' => $url,
]); ?>
<input type="hidden" name="type" value="form">
<div class="row">
    <div class="col-md-12">
        <div class="col-md-5">
			<?= $form->field($statisticFilter, 'dateRange')->widget(DateRangePickerExtend::className(), [
				'name'=>'date_range',
				'presetDropdown'=>true,
				'hideInput'=>true,
				'pluginOptions' => [
					'timePicker'=>false,
					'locale' => [
						'format' => $useTime?'DD-MM-YYYY HH:mm':'DD-MM-YYYY'
					],
				]
			]); ?>
        </div>
    </div>
    <div class="col-md-12">
        <div class="pull-right" style="padding: 25px 0 0 0;">
			<?= Html::submitButton('Фильтровать', ['class' => 'btn btn-info filter-button']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="clearfix"></div>
