<?php
$this->title = 'Создание теста';
$this->params['breadcrumbs'][] = ['label' => 'Тесты', 'url' => ['test/tests']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_test-form', ['model' => $model, 'title' => $this->title]);