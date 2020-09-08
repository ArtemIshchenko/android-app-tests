<?php
namespace backend\controllers;

use common\models\db\ChatMessageListRecord;
use common\models\db\ChatMessageRecord;
use backend\components\own\baseController\BackController;
use librariesHelpers\helpers\Type\Type_Cast;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\base\Model;
use librariesHelpers\helpers\Utf8\Utf8;

/**
 * Class ChatMessageController
 * @property-description Модуль управление чат сообщениями
 * @package backend\controllers
 * @author asisch
 */
class ChatMessageController extends BackController
{

    /**
     * @property-description Списки сообщений
     * @param integer $type
     */
    public function actionIndex($type = 0) {
        $params = [];
        switch($type) {
            case 1:
                $params = ['is_active' => ChatMessageListRecord::IS_ACTIVE];
                break;
            case 2:
                $params = ['is_active' => ChatMessageListRecord::IS_NOT_ACTIVE];
                break;
        }
        $model = new ChatMessageListRecord();
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @property-description Создание списка сообщений
     * @param integer $type
     * @return string
     */
    public function actionListAdd($type = 0) {

        $model = new ChatMessageListRecord();
        $model->setScenario('add');
        $model->name = '';
        $model->is_active = ChatMessageListRecord::IS_ACTIVE;

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['index', 'type' => $type]));
        }

        return $this->render('list-add', ['model' => $model, 'type' => $type]);
    }

    /**
     * @property-description Редактирование списка сообщений
     * @param integer $id
     * @param integer $type
     * @return string
     */
    public function actionListUpdate($id, $type = 0) {

        $model = ChatMessageListRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Список не найден", 404);
        }
        $model->setScenario('update');

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['index', 'type' => $type]));
        }

        return $this->render('list-update', ['model' => $model, 'type' => $type]);
    }

    /**
     * @property-description Активация/Деактивация списка
     * @param integer $id
     */
    public function actionListChangeActive($id)
    {
        $id = Type_Cast::toUInt($id);
        $model = ChatMessageListRecord::findOne($id);
        if(!is_null($model) && !empty($model)) {
            $model->setScenario('change-active');
            switch($model->is_active) {
                case ChatMessageListRecord::IS_NOT_ACTIVE:
                    $model->is_active = ChatMessageListRecord::IS_ACTIVE;
                    break;
                case ChatMessageListRecord::IS_ACTIVE:
                    $model->is_active = ChatMessageListRecord::IS_NOT_ACTIVE;
                    break;
            }
            $model->save();
        }
        $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @property-description сообщения
     * @param integer $id
     * @param integer $type
     */
    public function actionMessages($id, $type = 0) {
        $params = [];
        switch($type) {
            case 1:
                $params['is_active'] = ChatMessageRecord::IS_ACTIVE;
                break;
            case 2:
                $params['is_active'] = ChatMessageRecord::IS_NOT_ACTIVE;
                break;
        }

        $list = ChatMessageListRecord::findOne($id);
        if(is_null($list) || empty($list)) {
            throw new HttpException(404, "Список не найден", 404);
        }

        $model = new ChatMessageRecord();
        $params['list_id'] = $id;
        $dataProvider = $model->search(array_merge(\Yii::$app->request->get(), $params));
        return $this->render('messages', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'listId' => $id,
        ]);
    }

    /**
     * @property-description Создание сообщения
     * @param integer $type
     * @return string
     */
    public function actionMessageAdd($listId, $type = 0) {
        $list = ChatMessageListRecord::findOne($listId);
        if(is_null($list) || empty($list)) {
            throw new HttpException(404, "Список не найден", 404);
        }
        $model = new ChatMessageRecord();
        $model->setScenario('add');
        $model->list_id = $listId;
        $model->text = '';
        $model->message_type = ChatMessageRecord::TYPE['text'];
        $model->hold_time = 0;
        $model->is_active = ChatMessageRecord::IS_ACTIVE;
        $model->num = ChatMessageRecord::getNextPos($listId);

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['messages', 'id' => $listId, 'type' => $type]));
        }

        return $this->render('message-add', ['model' => $model, 'listId' => $listId, 'type' => $type]);
    }

    /**
     * @property-description Редактирование списка сообщений
     * @param integer $id
     * @param integer $type
     * @return string
     */
    public function actionMessageUpdate($listId, $id, $type = 0) {

        $list = ChatMessageListRecord::findOne($listId);
        if(is_null($list) || empty($list)) {
            throw new HttpException(404, "Список не найден", 404);
        }
        $model = ChatMessageRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Сообщение не найдено", 404);
        }
        $model->setScenario('update');


        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute(['messages', 'id' => $listId, 'type' => $type]));
        }

        return $this->render('message-update', ['model' => $model, 'listId' => $listId, 'type' => $type]);
    }

    /**
     * @property-description Активация/Деактивация сообщения
     * @param integer $id
     */
    public function actionMessageChangeActive($id)
    {

        $model = ChatMessageRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Сообщение не найдено", 404);
        }
        $model->setScenario('change-active');
        switch($model->is_active) {
            case ChatMessageRecord::IS_NOT_ACTIVE:
                $model->is_active = ChatMessageRecord::IS_ACTIVE;
                break;
            case ChatMessageListRecord::IS_ACTIVE:
                $model->is_active = ChatMessageRecord::IS_NOT_ACTIVE;
                break;
        }
        $model->save();
        $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @property-description Сообщение на позицию вверх
     * @param integer $listId
     * @param integer $id
     * @param integer $type
     * @return string
     */
    public function actionMessageUp($listId, $id, $type = 0) {

        $list = ChatMessageListRecord::findOne($listId);
        if(is_null($list) || empty($list)) {
            throw new HttpException(404, "Список не найден", 404);
        }
        $model = ChatMessageRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Сообщение не найдено", 404);
        }
        $model->setScenario('change-num');
        $prevNum = $model->num - 1;
        $prevModel = ChatMessageRecord::findOne(['list_id' => $listId, 'num' => $prevNum]);
        if(!is_null($prevModel) && !empty($prevModel)) {
            $prevModel->setScenario('change-num');
            $prevModel->num = $model->num;
            $model->num = $prevNum;
            $model->save();
            $prevModel->save();
        }
        $this->redirect(Url::toRoute(['messages', 'id' => $listId, 'type' => $type]));
    }

    /**
     * @property-description Сообщение на позицию вниз
     * @param integer $listId
     * @param integer $id
     * @param integer $type
     * @return string
     */
    public function actionMessageDown($listId, $id, $type = 0) {

        $list = ChatMessageListRecord::findOne($listId);
        if(is_null($list) || empty($list)) {
            throw new HttpException(404, "Список не найден", 404);
        }
        $model = ChatMessageRecord::findOne($id);
        if(is_null($model) || empty($model)) {
            throw new HttpException(404, "Сообщение не найдено", 404);
        }
        $model->setScenario('change-num');
        $nextNum = $model->num + 1;
        $nextModel = ChatMessageRecord::findOne(['list_id' => $listId, 'num' => $nextNum]);
        if(!is_null($nextModel) && !empty($nextModel)) {
            $nextModel->setScenario('change-num');
            $nextModel->num = $model->num;
            $model->num = $nextNum;
            $model->save();
            $nextModel->save();
        }
        $this->redirect(Url::toRoute(['messages', 'id' => $listId, 'type' => $type]));
    }


    public function actionTest() {

    }


}