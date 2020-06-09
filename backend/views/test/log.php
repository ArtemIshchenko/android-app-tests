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

?>

<div class="content">
    <div class="nav-tabs-custom">
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
                            'attribute' => 'device_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->device_id . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'deeplink',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->deeplink . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'lang',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->lang . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'token',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->token . '</div>';
                            }
                        ],
                        [
                            'attribute' => 'test_id',
                            'width' => '20%',
                            'format' => 'raw',
                            'value' => function($model){
                                return '<div>' . $model->test_id . '</div>';
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
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>