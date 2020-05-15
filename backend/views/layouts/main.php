<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use backend\components\own\rbac\AdmAccess;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
$contrAction = Yii::$app->urlManager->parseRequest(Yii::$app->request)[0];
$jspath = Yii::getAlias('@webroot/js/' . $contrAction . ".js");
if (is_file($jspath)) {
    $jsView = file_get_contents($jspath);
    $this->registerJs($jsView, View::POS_END);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="http://tools.goroskop.plus/favicon.png" type="image/png">
    <link rel="shortcut icon" href="http://tools.goroskop.plus/favicon.png" type="image/png">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    if (!Yii::$app->user->isGuest) {
        NavBar::begin([
            'brandLabel' => 'THOUSANDS-TESTS',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        print Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'navbar-nav navbar-left'],
            'items' => (new AdmAccess())->menuAdm(),
        ]);
        print Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [['label' => '<i class="fa fa-power-off color-tomato"></i> Выход', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post'], 'visible' => !Yii::$app->user->isGuest]]
        ]);
        NavBar::end();
    }
    ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>
<?php if (!Yii::$app->user->isGuest) { ?>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; <?= date('Y') ?></p>
        </div>
    </footer>
<?php } ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
