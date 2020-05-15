<?php
use yii\bootstrap\Nav;
use yii\grid\GridView;
use yii\helpers;

$this->title = 'Администраторы системы';
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= helpers\Html::encode($this->title) ?></h3>
<div class="content">
    <div class="nav-tabs-custom">
        <?= Nav::widget([
            'items' => [
                [
                    'label' => 'Активные',
                    'url' => helpers\Url::toRoute(['moder/index']),
                    'active' => Yii::$app->request->get('status') == 0 ? true : false
                ],
                [
                    'label' => 'Удаленые',
                    'url' => helpers\Url::toRoute(['moder/index', 'status' => 1]),
                    'active' => Yii::$app->request->get('status') == 1 ? true : false
                ],
            ],
            'options' => ['class' => 'nav-tabs'], // set this to nav-tab to get tab-styled navigation
        ]);
        ?>
        <div class="tab-content">
            <div class="pall10"></div>
            <?php if (Yii::$app->rbacManager->checkAccess('moder/add')) { ?>
                <?= helpers\Html::a('<i class="fa fa-plus"></i> Добавить администратора', helpers\Url::toRoute(['moder/add']), ['class' => 'btn btn-info',]) ?>
            <?php } ?>
            <div class="pall10"></div>
            <div class="">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $model,
                    'summary' => "Элементы {begin} - {end} из {totalCount}",
                    'pager' => [
                        'firstPageLabel' => 'Первая',
                        'lastPageLabel' => 'Последняя',
                    ],
                    'layout' => '{pager}<div class="summary">{summary}</div><div class="box-body">{items}</div>{pager}',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'nickname',
                            'format' => ['text']
                        ],
                        [
                            'attribute' => 'fullname',
                            'format' => ['text']
                        ],
                        [
                            'attribute' => 'email',
                            'format' => ['text'],
                        ],
                        [
                            'attribute' => 'is_root',
                            'format' => ['raw'],
                            'value' => function ($data) {
                                return $data->is_root ? '<i class="fa fa-check-circle-o"></i>' : '<i class="fa fa-circle-o"></i>';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view} {update} {delete}',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    if (Yii::$app->rbacManager->checkAccess('moder/view')) {
                                        return helpers\Html::a('<i class="fa fa-eye fa-2x"></i>', helpers\Url::toRoute(['/moder/view', 'id' => $model['id']]), ['title' => 'Просмотреть']);
                                    }
                                },
                                'update' => function ($url, $model) {
                                    if (Yii::$app->rbacManager->checkAccess('moder/update')) {
                                        return helpers\Html::a('<i class="fa fa-pencil fa-2x"></i>', helpers\Url::toRoute(['/moder/update', 'id' => $model['id']]), ['title' => 'Редактировать']);
                                    }
                                },
                                'delete' => function ($url, $model) {
                                    if (Yii::$app->rbacManager->checkAccess('moder/delete')) {
                                        return helpers\Html::a('<i class="fa fa-trash-o fa-2x"></i>', helpers\Url::toRoute(['/moder/delete', 'id' => $model['id']]), ['title' => 'Удалить']);
                                    }
                                }
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
