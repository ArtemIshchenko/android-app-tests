<?php
$this->title = 'Создание диплинка';
$this->params['breadcrumbs'][] = ['label' => 'Диплинки', 'url' => ['test/index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_deeplink-form', ['model' => $model, 'title' => $this->title]);