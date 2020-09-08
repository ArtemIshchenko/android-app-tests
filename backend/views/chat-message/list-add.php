<?php
$this->title = 'Создание списка чат-сообщений';
$this->params['breadcrumbs'][] = ['label' => 'Списки чат-ссобщений', 'url' => ['chat-message/index', 'type' => $type]];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_list-form', ['model' => $model, 'title' => $this->title]);