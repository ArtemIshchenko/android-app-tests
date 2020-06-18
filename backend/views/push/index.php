<?php
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\db\TestRecord;
use common\models\db\DeeplinkRecord;

$this->title = 'Пуши';
$this->params['breadcrumbs'][] = $this->title;

$type = \Yii::$app->request->get('type',0);
$addButton = '';
if (Yii::$app->rbacManager->checkAccess('push/index')) {
    $addButton = '<div class="pull-right mtop20">';
    $addButton .= Html::a('<i class="fa fa-plus"></i> Добавить', Url::toRoute(['push/add', 'type' => $type]), ['class' => 'btn btn-success']);
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
                    'label' => 'В процессе отправки',
                    'url' => Url::toRoute(['push/index', 'type' => 0]),
                    'active' => $type == 0 ? true : false,
                ],
                [
                    'label' => 'Отправленные',
                    'url' => Url::toRoute(['push/index', 'type' => 1]),
                    'active' => $type == 1 ? true : false,
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
                            'attribute' => 'title',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->title . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'text',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->text . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'deeplink_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '';
                                $deeplinkModel = DeeplinkRecord::findOne($model->deeplink_id);
                                if (!is_null($deeplinkModel) && !empty($deeplinkModel)) {
                                    $result = $deeplinkModel->name;
                                }
                                return '<div>' . $result . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'gtest_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '';
                                $testList = TestRecord::getGreyTestList();
                                foreach ($testList as $id => $title) {
                                    if ($id == $model->gtest_id) {
                                        $result = $title;
                                        break;
                                    }
                                }
                                return '<div>' . $result . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'wtest_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '';
                                $testList = TestRecord::getAppTestList();
                                foreach ($testList as $id => $title) {
                                    if ($id == $model->wtest_id) {
                                        $result = $title;
                                        break;
                                    }
                                }
                                return '<div>' . $result . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'registration_from',
                            'contentOptions' => ['style' => 'width:50px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '--';
                                if ($model->registration_from > 0) {
                                    $result = '<div>' . date('d.m.Y, H:i', $model->registration_from) . '</div>';
                                }
                                return $result;
                            }
                        ],
                        [
                            'attribute' => 'registration_to',
                            'contentOptions' => ['style' => 'width:50px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '--';
                                if ($model->registration_to > 0) {
                                    $result = '<div>' . date('d.m.Y, H:i', $model->registration_to) . '</div>';
                                }
                                return $result;
                            }
                        ],
                        [
                            'attribute' => 'push_at',
                            'contentOptions' => ['style' => 'width:50px;'],
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '<div>' . date('d.m.Y, H:i',$model->push_at) . '</div>';
                                return $result;
                            }
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>