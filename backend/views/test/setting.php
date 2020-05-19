<?php

use common\models\db\SettingRecord;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content">
    <div class="nav-tabs-custom">
        <div class="pall10"></div>
        <div class="tab-content">
            <div class="">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'test-setting-form',
                    'method' => 'post',
                    'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-md-2\">{input}</div>\n<div class=\"col-sm-offset-2 col-md-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-md-5 control-label'],
                    ],
                ]); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading"><?= $this->title ?></div>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <?php foreach ($settings as $number => $setting) {
                                            if (SettingRecord::SETTINGS[$number]['type'] == SettingRecord::TYPE['input']) {
                                                echo $form->field($setting, "[$number]value")->label($setting->description);
                                            } elseif (SettingRecord::SETTINGS[$number]['type'] == SettingRecord::TYPE['checkbox']) {
                                                echo $form->field($setting, "[$number]value")->checkbox(['label' => ''])->label($setting->description);
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="pull-right">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Сохранить', ['class' => 'btn btn-info', 'id' => 'save-push']) ?><br>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>