<?php
use backend\models\db\adm\LogRecord;
use yii\grid\GridView;
use yii\helpers;
use yii\widgets\DetailView;

$this->title = 'Просмотр администратора - ' . $model->nickname;
$this->params['breadcrumbs'][] = ['label' => 'Администраторы системы', 'url' => helpers\Url::toRoute(['moder/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= helpers\Html::encode($this->title) ?></h3>
<div class="content">
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= $this->title ?></h3>
            </div>
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'nickname',
                        'fullname',
                        'email',
                        'password_email',
                        'last_active_at:datetime',
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><a href="#">Права доступа</a></h3>
            </div>
            <div class="box-body" style="display:none;">
                <div>
                    <label class="checkbox">
                        <span>Супер пользователь&nbsp;&nbsp;</span>
                        <?= $model->is_root ? '<i class="fa fa-check-circle-o"></i>' : '<i class="fa fa-circle-o"></i>'; ?>
                    </label>
                </div>
                <hr>
                <?php foreach ($classList as $className) { ?>
                    <div class="row">
                        <div class="panel panel-info">
                            <div class="panel-heading"><?= $className["classDoc"][0] ? $className["classDoc"][0] : $className["className"][0]; ?></div>
                            <div class="panel-body">
                                <?php if (isset($className["method"])) {
                                    foreach ($className["method"] as $key => $method) { ?>
                                        <div>
                                            <label class="checkbox">
                                                <?php
                                                $rulesName = mb_strtoupper($className["className"][0] . '_' . $method);
                                                $checked = $rules[$rulesName] ? '<i class="fa fa-check-circle-o"></i>' : '<i class="fa fa-circle-o"></i>';
                                                if ($model->is_root) {
                                                    $checked = '<i class="fa fa-check-circle-o"></i>';
                                                }
                                                ?>
                                                <span><?= $className["doc"][$key] ? $className["doc"][$key] : $className["className"][0] . " => " . $method ?>
                                                    &nbsp;&nbsp;</span>
                                                <?= $checked ?>
                                            </label>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="pall10"></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>