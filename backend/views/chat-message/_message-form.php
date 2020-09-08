<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use common\models\db\ChatMessageListRecord;
use common\models\db\ChatMessageRecord;

?>
<div class="content">
	<div class="row">
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
                        <?= $form->field($model, 'text')->textarea()
                            ->hint('') ?>

                        <?= $form->field($model, 'list_id')->dropDownList(ChatMessageListRecord::getList(), ['class' => 'form-control', 'prompt' => 'Выберите значение']) ?>

                        <?= $form->field($model, 'message_type')->radioList(ChatMessageRecord::getType())
                            ->hint('') ?>

                        <?= $form->field($model, 'hold_time')->textInput()
                            ->hint('') ?>

                        <?= $form->field($model, 'is_active')->radioList(ChatMessageRecord::getStatus())
                            ->hint('') ?>

                    </div>
                </div>
			</div>
			<div class="panel-footer">
                <div class="pull-right">
					<?= Html::submitButton('<i class="fa fa-save"></i> Сохранить', ['class' => 'btn btn-info', 'id' => 'save-message']) ?><br>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>
