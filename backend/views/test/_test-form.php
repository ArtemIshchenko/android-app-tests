<?php
use kartik\select2\Select2;
use common\models\db\AdvertisingSystemRecord;
use yii\helpers\Html;
use yii\web\JsExpression;

?>
<div class="content">
	<div class="row">
        <div class="pall10"></div>
        <div class="well">
            <div>
                Обозначения обязательных полей в массиве :
            </div>
            <div>
                <kbd>id, title, description</kbd> - идентификатор (целое число, по порядку), заголовок и описание теста соответственно
            </div>
            <div>
                <kbd>questions</kbd> - массив вопросов теста
            </div>
            <div>
                <kbd>number, text</kbd> - номер (целое число, по порядку) и текст вопроса в массиве questions соответственно
            </div>
            <div>
                <kbd>answers</kbd> - массив ответов
            </div>
            <div>
                <kbd>number, text</kbd> - номер (целое число, по порядку) и текст вопроса в массиве answers соответственно
            </div>
            <div>
                <kbd>isSignal</kbd> - флаг, является данный ответ сигнальным, возможные значения: 0 и 1
            </div>
            <div>
                Пример массива : <br>
                <textarea rows="24" cols="45" disabled>
                    [
                        "id" => 1,
                        "title" => "тест",
                        "description" => "описание",
                        "questions" => [
                            [
                                "number" => 1,
                                "text" => "вопрос",
                                "answers" => [
                                [
                                    "number" => 1,
                                    "text" => "ответ1",
                                    "isSignal" => 1
                                ],
                                [
                                    "number" => 2,
                                    "text" => "ответ2",
                                    "isSignal" => 1
                                ],
                                ]
                            ],
                        ]
                    ]
                </textarea>
            </div>
        </div>
        <div class="pall10"></div>
		<?php $form = \yii\widgets\ActiveForm::begin([
			'id' => 'test-tests-form',
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
                        <?= $form->field($model, 'name')->textInput()
                            ->hint('') ?>

                        <?= $form->field($model, 'structure')->textarea(['rows' => 30])
                            ->hint('') ?>

                    </div>
                </div>
			</div>
			<div class="panel-footer">
                <div class="pull-right">
					<?= Html::submitButton('<i class="fa fa-save"></i> Сохранить', ['class' => 'btn btn-info', 'id' => 'save-test-tests']) ?><br>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>
