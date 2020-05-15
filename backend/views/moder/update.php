<?php
use yii\helpers;

$this->title = 'Редактирование администратора системы';
$this->params['breadcrumbs'][] = ['label' => 'Администраторы системы', 'url' => helpers\Url::toRoute(['moder/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .field-adm-is_root {
        margin: 0 0 0 390px !important;
    }

    .field-adm-is_moder {
        margin: 0 0 0 390px !important;
    }
</style>
<div class="content">
    <div class="row">
        <?php $form = \yii\widgets\ActiveForm::begin([
            'id' => 'moder-form',
            'method' => 'post',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                'labelOptions' => ['class' => 'col-lg-3 control-label'],
            ],
        ]); ?>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title ?></h3>
            </div>
            <div class="box-body">
                <?= $form->field($model, 'nickname')->textInput() ?>
                <?= $form->field($model, 'email')->textInput() ?>
                <?= $form->field($model, 'fullname')->textInput() ?>
                <?= $form->field($model, 'password')->textInput(['value' => '', 'id' => 'password']) ?>
                <?= $form->field($model, 'is_root')->checkbox(['id' => 'isRoot']) ?>
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= helpers\Html::button('Сгенерировать пароль', ['class' => 'btn btn-danger', 'id' => 'generatePassword']) ?>
                        <?= helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-info']) ?><br>
                    </div>
                </div>
                <div class="pall10"></div>
                <h4>Права доступа</h4>
                <?php foreach ($classList as $className) { ?>
                    <div class="row">
                        <div class="panel panel-info">
                            <div class="panel-heading"><?= $className["classDoc"][0] ? $className["classDoc"][0] : $className["className"][0]; ?></div>
                            <div class="panel-body">
                                <?php if (isset($className["method"])) {
                                    foreach ($className["method"] as $key => $method) { ?>
                                        <?php
                                        $rulesName = mb_strtoupper($className["className"][0] . '_' . $method);
                                        $checked = $model->rules[$rulesName] ? 'checked="checked"' : '';
                                        if ($model->is_root) {
                                            $checked = 'checked="checked"';
                                        }
                                        ?>
                                        <div class="form-group">
                                            <div class="col-lg-9">
                                                <label>
                                                    <input type="checkbox" id="adm-rules-categorycontroller_actionindex"
                                                           name="Adm[rules]<?= '[' . $className["className"][0] . '_' . $method . '][]' ?>"
                                                           value="1" <?= $checked ?>">
                                                    <?= $className["doc"][$key] ? $className["doc"][$key] : $className["className"][0] . " => " . $method ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="pall10"></div>
                <?php } ?>
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-info']) ?><br>
                    </div>
                </div>
            </div>
        </div>
        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
</div>