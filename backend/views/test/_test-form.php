<?php
use yii\helpers\Html;

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
                <kbd>imageAnswer</kbd> - вариант ответа для имиджевого режима, а также если не выбран ни один "сигнальный" ответ
            </div>
            <div>
                <kbd>timerSetting</kbd> - уставка таймера для отображения кнопки результатов, сек
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
                <kbd>number, text</kbd> - номер (целое число, по порядку) и текст ответа в массиве answers соответственно
            </div>
            <div>
                <kbd>isSignal</kbd> - флаг, является ли данный ответ сигнальным, возможные значения: 0 или 1
            </div>
            <div>
                <kbd>rating</kbd> - рейтинг ответа (количесвто баллов за ответ)
            </div>
            <div>
                <br>
                <b>Пример массива :</b> <br>
                <textarea rows="24" cols="130" disabled>
                    [
                            "id" => 1,
                            "title" => "тест",
                            "description" => "описание",
                            "imageAnswer" => "Вариант ответа для имиджевого режима",
                            "timerSetting" => 3700,
                            "questions" => [
                                    [
                                            "number" => 1,
                                            "text" => "вопрос",
                                            "answers" => [
                                                    [
                                                             "number" => 1,
                                                            "text" => "ответ1",
                                                            "isSignal" => 1,
                                                            "rating" => 1
                                                    ],
                                                    [
                                                            "number" => 2,
                                                            "text" => "ответ2",
                                                            "isSignal" => 1,
                                                            "rating" => 2
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
                        <?= $form->field($model, 'structForCheck')->textarea(['rows' => 30])
                            ->hint('') ?>
                        <div class="pull-right">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Проверка массива', ['class' => 'btn btn-info', 'id' => 'check-test-tests', 'name' => 'check', 'value' => 1]) ?><br>
                        </div>
                    </div>

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
