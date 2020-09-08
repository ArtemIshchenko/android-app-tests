<?php
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\db\TestRecord;
use common\models\db\DeeplinkRecord;

$this->title = 'Списки чат-сообщений';
$this->params['breadcrumbs'][] = $this->title;

$type = \Yii::$app->request->get('type',0);
$addButton = '';
if (Yii::$app->rbacManager->checkAccess('chat-message/list-add')) {
    $addButton = '<div class="pull-right mtop20">';
    $addButton .= Html::a('<i class="fa fa-plus"></i> Добавить', Url::toRoute(['chat-message/list-add', 'type' => $type]), ['class' => 'btn btn-success']);
    $addButton .= '</div>';
}
$addButton .= '<div class="clearfix"></div><div class="ptop10"></div>';
?>

<div class="content">
    <div class="nav-tabs-custom">
        <div class="pall10"></div>
        <?= Nav::widget([
            'items' => [
                [
                    'label' => 'Все',
                    'url' => Url::toRoute(['chat-message/index', 'type' => 0]),
                    'active' => $type == 0 ? true : false,
                ],
                [
                    'label' => 'Активные',
                    'url' => Url::toRoute(['chat-message/index', 'type' => 1]),
                    'active' => $type == 1 ? true : false,
                ],
                [
                    'label' => 'Не активные',
                    'url' => Url::toRoute(['chat-message/index', 'type' => 2]),
                    'active' => $type == 2 ? true : false,
                ],
            ],
            'encodeLabels' => false,
            'options' => ['class' => 'nav-tabs'],
        ]);
        ?>
        <div class="pall10"></div>
        <?= $addButton ?>
        <div class="tab-content">
            <div class="">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $model,
                    'summary' => "Элементы {begin} - {end} из {totalCount}",
                    'pager' => [
                        'firstPageLabel' => 'Первая',
                        'lastPageLabel' => 'Последняя',
                    ],
                    'layout' => '<div class="pull-left">{pager}</div><div class="summary">{summary}</div><div class="box-body">{items}</div>{pager}',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'width' => '50px',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'name',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->name . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'is_active',
                            'contentOptions' => ['style' => 'width:80px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                if($model->is_active) {
                                    $result = '<i class="fa fa-check-circle-o fa-2x"><i>';
                                }else{
                                    $result = '<i class="fa fa-circle-o fa-2x"><i>';
                                }
                                if (Yii::$app->rbacManager->checkAccess('chat-message/list-change-active')) {
                                    return '<a href="' . Url::toRoute(['/chat-message/list-change-active', 'id' => $model->id]) . '">' . $result . '</a>';
                                }
                                return $result;
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'contentOptions' => ['style' => 'width:50px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '<div>' . date('d.m.Y, H:i',$model->created_at) . '</div>';
                                return $result;
                            }
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'contentOptions' => ['style' => 'width:40px;'],
                            'template' => "{update} {messages}",
                            'buttons' => [
                                'update' => function($url, $model) use($type) {
                                    if (Yii::$app->rbacManager->checkAccess('chat-message/list-update')) {
                                        return '<a href="' . Url::toRoute(['/chat-message/list-update', 'id' => $model->id, 'type' => $type]) . '" class="fa fa-pencil fa-2x" title="Редактирование"></a>';
                                    }
                                },
                                'messages' => function($url, $model) use($type) {
                                    if (Yii::$app->rbacManager->checkAccess('chat-message/messages')) {
                                        return '<a href="' . Url::toRoute(['/chat-message/messages', 'id' => $model->id]) . '" class="fa fa-list fa-2x" title="Сообщения"></a>';
                                    }
                                },
                            ]
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>