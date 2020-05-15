<?php

use yii\helpers\Html;

$this->title = 'Авторизация';

?>
<div class="span100">
    <div class="row">
        <div class="panel panel-info"
             style="width:350px;height:248px;position: absolute;left: 50%;top: 50%;margin: -224px 0 0 -175px;">
            <div class="panel-heading"><?= Html::encode($this->title) ?></div>
            <div class="panel-body">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'login-form',
                    'method' => 'post',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                ]); ?>
                <?= $form->field($model, 'nickname') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <div class="form-group field-adm-password_hash">
                    <label class="col-lg-3 control-label" for="adm-enter"></label>
                    <div class="col-lg-9"
                         style="text-align:right;"><?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?></div>
                    <div class="col-sm-offset-3 col-lg-9">
                        <div class="help-block"></div>
                    </div>
                </div>
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

