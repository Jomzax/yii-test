<?php

use app\components\MyGridView;
use app\helpers\AccessControlHelper;
use app\helpers\DateHelper;
use app\helpers\PermissionControlHelper;
use app\models\Menu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\MenuSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'เมนู';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <div class="d-flex flex-wrap g-3 justify-content-between align-items-center mb-3">
        <h2 class="text-white"><?= Html::encode($this->title) ?></h2>
        <?= Html::a('<span class="glyphicon glyphicon-plus me-2"></span>สร้าง' . $this->title, ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <section class="widget">
        <div class="widget-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </section>

    <section class="widget">
        <div class="widget-body">
            <div class="table-responsive">
                <?= GridView::widget([
                    'id' => 'menu-grid',
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-striped'],
                    'summary' => 'แสดง {begin} - {end} จากทั้งหมด {totalCount} รายการ',
                    'emptyText' => 'ไม่พบข้อมูล',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'header' => 'ลำดับ',
                            'headerOptions' => ['class' => 'text-center', 'style' => 'width: 50px;'],
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                        'name',
                        [
                            'attribute' => 'link',
                            'headerOptions' => ['class' => 'text-nowrap'],
                            'format' => 'raw',
                            'value' => function ($model) {
                                $link = $model->link;
                                return <<<HTML
                                        <span class="text-ellipsis">{$link}</span>
                                        HTML;
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'headerOptions' => ['class' => 'text-nowrap'],
                            'contentOptions' => ['class' => 'text-nowrap'],
                            'value' => function ($model) {
                                return DateHelper::DateThaiTime($model->created_at);
                            }
                        ],
                        [
                            'class' => ActionColumn::class,
                            'urlCreator' => function ($action, Menu $model, $key, $index, $column) {
                                return Url::toRoute([$action, '_id' => (string) $model->_id]);
                            },
                            'header' => 'จัดการ',
                            'headerOptions' => [
                                'class' => 'text-nowrap text-center',
                                'style' => 'width: 120px;'
                            ],
                            'contentOptions' => ['class' => 'text-nowrap text-center'],
                            'template' => '<div class="btn-group flex-nowrap">{view} {update} {delete}</div>',
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    if (!PermissionControlHelper::canAccessPermission(PermissionControlHelper::ACTION_PERMISSION_MENU_VIEW)) {
                                        return '';
                                    }
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-search"></span> <span class="d-none d-lg-inline ms-1">ดู</span>',
                                        $url,
                                        [
                                            'class' => 'btn btn-sm btn-info',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'title' => 'ดู'
                                        ]
                                    );
                                },
                                'update' => function ($url, $model) {
                                    if (!PermissionControlHelper::canAccessPermission(PermissionControlHelper::ACTION_PERMISSION_MENU_UPDATE)) {
                                        return '';
                                    }
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-pencil"></span> <span class="d-none d-lg-inline ms-1">แก้ไข</span>',
                                        $url,
                                        [
                                            'class' => 'btn btn-sm btn-warning',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'title' => 'แก้ไข'
                                        ]
                                    );
                                },
                                'delete' => function ($url, $model) {
                                    if (!PermissionControlHelper::canAccessPermission(PermissionControlHelper::ACTION_PERMISSION_MENU_DELETE)) {
                                        return '';
                                    }
                                    return Html::a(
                                        '<span class="glyphicon glyphicon-trash"></span><span class="d-none d-lg-inline ms-1">ลบ</span>',
                                        $url,
                                        [
                                            'class' => 'btn btn-sm btn-danger',
                                            'data-method' => 'POST',
                                            'data-confirm' => 'คุณต้องการลบใช่หรือไม่?',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'title' => 'ลบ'
                                        ]
                                    );
                                },
                            ]
                        ],
                    ],
                    'pager' => [
                        'options' => [
                            'class' => 'pagination pagination-bordered justify-content-center',
                        ],
                        'linkOptions' => ['class' => 'page-link'],
                        'linkContainerOptions' => ['class' => 'page-item'],
                        'nextPageLabel' => "next",
                        'prevPageLabel' => "pre",
                    ]
                ]); ?>


            </div>
        </div>
    </section>
</div>