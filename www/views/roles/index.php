<?php
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\RolesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'จัดการบทบาท';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('สร้างบทบาท', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <div class="card">
        <div class="card-header bg-light py-3">
        <h5 class="m-0"><i class="bi bi-search "></i> รายการบทบาท</h5>
    </div>
        <div class="card-body  bg-light py-3">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'ลำดับ',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 50px;'],
                    'contentOptions' => ['class' => 'text-center'],
                
                ],

                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'type',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => fn($m) => match($m->type) {
                        \app\models\Roles::TYPE_SUPER_ADMIN => '<span class="badge bg-danger">Super Admin</span>',
                        \app\models\Roles::TYPE_ADMIN => '<span class="badge bg-primary">Admin</span>',
                        \app\models\Roles::TYPE_USER => '<span class="badge bg-secondary">User</span>',
                        default => '<span class="badge bg-dark">'.htmlspecialchars($m->type).'</span>',
                    },
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'จัดการ',
                    'headerOptions' => ['class' => 'text-nowrap text-center','style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return [$action, '_id' => (string)$model->_id];
                    }
                ],
                ],
            ]); ?>
        </div>
    </div>
</div>
