<?php
$this->title = 'Создание чат-сообщения';
$this->params['breadcrumbs'][] = ['label' => 'Списки чат-ссобщений', 'url' => ['chat-message/messages', 'id' => $listId, 'type' => $type]];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_message-form', ['model' => $model, 'title' => $this->title]);