<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\own\datarange\DateRangePickerExtend;
use kartik\datetime\DateTimePicker;
use common\models\db\DeeplinkRecord;
use common\models\db\TestRecord;

?>
<div class="content">
	<div class="row">
		<?php $form = \yii\widgets\ActiveForm::begin([
			'id' => 'push-form',
			'method' => 'post',
			'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
			'fieldConfig' => [
				'template' => "{label}\n
                    <div class=\"col-md-9\">{input}</div>\n
                    <div class=\"col-sm-offset-3 col-md-9\">{error}\n{hint}</div>",
				'labelOptions' => ['class' => 'col-md-3 control-label'],
			],
		]); ?>
		<div class="panel panel-primary">
			<div class="panel-heading"><?= $this->title ?></div>
			<div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= $form->field($model, 'title')->textInput()
                            ->hint('') ?>

                        <?= $form->field($model, 'text')->textarea(['rows' => 5])
                            ->hint('') ?>

                        <?= $form->field($model, 'registrationDataRange')->widget(DateRangePickerExtend::className(), [
                            'id' => 'registrationDataRange',
                            'name'=>'registrationDataRange',
                            'presetDropdown'=>true,
                            'hideInput'=>true,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'timePicker'=>false,
                                'locale' => [
                                    'format' => 'DD-MM-YYYY HH:mm'
                                ],
                            ]
                        ]) ?>

                        <?= $form->field($model, 'deeplink_id')->dropDownList(DeeplinkRecord::deeplinkList(), ['id' => 'deeplink_id', 'class' => 'form-control', 'prompt' => 'Выберите значение'])
                            ->hint('') ?>

                        <?= $form->field($model, 'gtest_id')->dropDownList(TestRecord::getGreyTestList(), ['id' => 'gtest_id', 'class' => 'form-control', 'prompt' => 'Выберите значение'])
                            ->hint('') ?>

                        <?= $form->field($model, 'wtest_id')->dropDownList(TestRecord::getAppTestList(), ['id' => 'wtest_id', 'class' => 'form-control', 'prompt' => 'Выберите значение'])
                            ->hint('') ?>

                        <?= $form->field($model, 'push_at')->widget(DateTimePicker::classname(),
                            [
                                'options' => ['placeholder' => 'Выберите дату пуша ...'],
                                'layout' => '{picker}{input}',
                                'readonly' => true,
//                                'removeButton' => [
//                                    //'icon'=>'trash',
//                                ],
                                'pluginOptions' => [
                                    'orientation' => 'top right',
                                    'autoclose'=>true,
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                    'timePicker'=>true,
                                    'timePickerIncrement'=>5,
                                    'timePicker24Hour' => true,
                                    'format' => 'dd-mm-yyyy HH:ii'
                                ]
                            ]
                        ) ?>
                    </div>
                </div>
			</div>
			<div class="panel-footer">
                <div class="pull-right">
					<?= Html::submitButton('<i class="fa fa-save"></i> Разослать', ['class' => 'btn btn-info', 'id' => 'save-set-push']) ?><br>
				</div>
                <div class="pull-right" id="calc-value" style="margin: 0 100px 0 20px; font-size: 18px;">
                    &nbsp;
                </div>
                <div class="pull-right">
                    <?= Html::Button('<i class="fa fa-pencil"></i> Расчитать', ['class' => 'btn btn-info', 'id' => 'calc-set-push', 'data-url' => Url::toRoute(['add'])]) ?><br>
                </div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>
