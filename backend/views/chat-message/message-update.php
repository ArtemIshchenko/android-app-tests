<?php
$this->title = "Редактирование чат-сообщения";
$this->params['breadcrumbs'][] = ['label' => 'Чат-собщения', 'url' => ['chat-message/messages', 'id' => $listId, 'type' => $type]];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_message-form', ['model' => $model, 'title' => $this->title]);