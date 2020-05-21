<?php
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\db\TestRecord;
use common\models\db\DeeplinkRecord;

$this->title = 'Диплинки';
$this->params['breadcrumbs'][] = $this->title;

$type = \Yii::$app->request->get('type',0);
$addButton = '';
if (Yii::$app->rbacManager->checkAccess('test/index')) {
    $addButton = '<div class="pull-right mtop20">';
    $addButton .= Html::a('<i class="fa fa-plus"></i> Добавить', Url::toRoute(['test/deeplink-add', 'type' => $type]), ['class' => 'btn btn-success']);
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
                    'url' => Url::toRoute(['test/index', 'type' => 0]),
                    'active' => $type == 0 ? true : false,
                ],
                [
                    'label' => 'Активные',
                    'url' => Url::toRoute(['test/index', 'type' => 1]),
                    'active' => $type == 1 ? true : false,
                ],
                [
                    'label' => 'Не активные',
                    'url' => Url::toRoute(['test/index', 'type' => 2]),
                    'active' => $type == 2 ? true : false,
                ],
                [
                    'label' => 'Прогревочные',
                    'url' => Url::toRoute(['test/index', 'type' => 3]),
                    'active' => $type == 3 ? true : false,
                ],
                [
                    'label' => 'Имиджевые',
                    'url' => Url::toRoute(['test/index', 'type' => 4]),
                    'active' => $type == 4 ? true : false,
                ],
                [
                    'label' => 'Боевые',
                    'url' => Url::toRoute(['test/index', 'type' => 5]),
                    'active' => $type == 5 ? true : false,
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
                            'attribute' => 'description',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->description . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'url',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->url . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'test_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . TestRecord::getNameById($model->test_id) . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'app_test_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '';
                                if (isset(TestRecord::APP_TESTS[$model->app_test_id])) {
                                    $result = TestRecord::APP_TESTS[$model->app_test_id];
                                }
                                return '<div>' . $result . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'mode',
                            'width' => '80px',
                            'format' => 'raw',
                            'value' => function($model){
                                $label = '';
                                if ($model->mode == DeeplinkRecord::MODE['warming']) {
                                    $label = 'Прогревочный';
                                } elseif ($model->mode == DeeplinkRecord::MODE['image']) {
                                    $label = 'Имеджевый';
                                } elseif ($model->mode == DeeplinkRecord::MODE['fighting']) {
                                    $label = 'Боевой';
                                }
                                return '<div>' . $label . '</div>';
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
                                if (Yii::$app->rbacManager->checkAccess('test/deeplink-change-active')) {
                                    return '<a href="' . Url::toRoute(['/test/deeplink-change-active', 'id' => $model->id]) . '">' . $result . '</a>';
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
                            'template' => "{update}",
                            'buttons' => [
                                'update' => function($url, $model) use($type) {
                                    if (Yii::$app->rbacManager->checkAccess('test/deeplink-update')) {
                                        return '<a href="' . Url::toRoute(['/test/deeplink-update', 'id' => $model->id, 'type' => $type]) . '" class="fa fa-pencil fa-2x" title="Редактирование"></a>';
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