<?php
$this->title = 'Редактирование пуша';
$this->params['breadcrumbs'][] = ['label' => 'Пуши', 'url' => ['index', 'type' => $type]];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_form', ['model' => $model, 'title' => $this->title]);