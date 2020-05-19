<?php
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Тесты';
$this->params['breadcrumbs'][] = $this->title;

$type = \Yii::$app->request->get('type',0);
$addButton = '';
if (Yii::$app->rbacManager->checkAccess('test/tests')) {
    $addButton = '<div class="pull-right mtop20">';
    $addButton .= Html::a('<i class="fa fa-plus"></i> Добавить', Url::toRoute(['test/test-add', 'type' => $type]), ['class' => 'btn btn-success']);
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
                    'url' => Url::toRoute(['test/tests', 'type' => 0]),
                    'active' => $type == 0 ? true : false,
                ],
                [
                    'label' => 'Активные',
                    'url' => Url::toRoute(['test/tests', 'type' => 1]),
                    'active' => $type == 1 ? true : false,
                ],
                [
                    'label' => 'Не активные',
                    'url' => Url::toRoute(['test/tests', 'type' => 2]),
                    'active' => $type == 2 ? true : false,
                ],
            ],
            'encodeLabels' => false,
            'options' => ['class' => 'nav-tabs'],
        ]);
        ?>
        <div class="pall10"></div>
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
                    'layout' => '<div class="pull-left">{pager}</div>'.$addButton.'<div class="summary">{summary}</div><div class="box-body">{items}</div>{pager}',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'contentOptions' => ['style' => 'width:50px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'name',
                            'width' => '200px',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->name . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'structure',
                            'format' => 'raw',
                            'value' => function($model) {
                                $model->convReverse();
                                return '<div><textarea rows="20" cols="120" disabled>' . $model->structure . '</textarea></div>';
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
                                if (Yii::$app->rbacManager->checkAccess('test/test-change-active')) {
                                    return '<a href="' . Url::toRoute(['/test/test-change-active', 'id' => $model->id]) . '">' . $result . '</a>';
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
                                    if (Yii::$app->rbacManager->checkAccess('test/test-update')) {
                                        return '<a href="' . Url::toRoute(['/test/test-update', 'id' => $model->id, 'type' => $type]) . '" class="fa fa-pencil fa-2x" title="Редактирование"></a>';
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