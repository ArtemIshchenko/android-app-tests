<?php
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\db\TestRecord;
use common\models\db\DeeplinkRecord;

$this->title = 'Логи';
$this->params['breadcrumbs'][] = $this->title;
$addButton = '<div class="clearfix"></div><div class="ptop10"></div>';
$type = \Yii::$app->request->get('type',0);
?>
<div class="pall10"></div>
<div class="content">
    <div class="nav-tabs-custom">
        <div class="pall10"></div>
        <?= Nav::widget([
            'items' => [
                [
                    'label' => 'Получение теста',
                    'url' => Url::toRoute(['test/log', 'type' => 0]),
                    'active' => $type == 0 ? true : false,
                ],
                [
                    'label' => 'Установка пуша',
                    'url' => Url::toRoute(['test/log', 'type' => 1]),
                    'active' => $type == 1 ? true : false,
                ],
            ],
            'encodeLabels' => false,
            'options' => ['class' => 'nav-tabs'],
        ]);
        ?>
        <div class="pall10"></div>

        <?= $this->render('_filters', [
            'statisticFilter' => $statisticFilter,
            'url' => Url::toRoute(['test/log']),
            'useTime' => true,
            'field' => ['dateRange'],
            'excludeField' => ['pageSize'],
        ]) ?>
        <div class="pall10"></div>
        <?php foreach ($data as $i => $item) {
            $label = 'label-success';
            if (($i+1) % 2 == 0) {
                $label = 'label-info';
            } elseif (($i+1) % 3 == 0) {
                $label = 'label-warning';
            } elseif (($i+1) % 4 == 0) {
                $label = 'label-danger';
            }
            ?>
            <div><span class="label <?= $label ?>"><?= $item['name'] ?>:</span> - <?= $item['c'] ?></div>
        <?php } ?>
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
                            'width' => '50px',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'device_id',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->device_id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'deeplink',
                            'format' => 'raw',
                            'hidden' => $type > 0,
                            'value' => function($model){
                                return '<div>' . $model->deeplink . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'lang',
                            'width' => '50px',
                            'format' => 'raw',
                            'hidden' => $type > 0,
                            'value' => function($model){
                                return '<div>' . $model->lang . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'token',
                            'format' => 'raw',
                            'hidden' => $type == 0,
                            'value' => function($model){
                                return '<div>' . $model->token . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'test_id',
                            'width' => '50px',
                            'format' => 'raw',
                            'hidden' => $type == 0,
                            'value' => function($model){
                                return '<div>' . $model->test_id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'width' => '50px',
                            'format' => 'raw',
                            'value' => function($model){
                                $result = '<div>' . date('d.m.Y, H:i',$model->created_at) . '</div>';
                                return $result;
                            }
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>