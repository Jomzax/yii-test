<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'ผู้ใช้';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('สร้างผู้ใช้', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
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
                'attribute' => 'username',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'จัดการ',
                    'headerOptions' => ['class' => 'text-nowrap text-center','style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return [$action, 'id' => (string)$model->_id];
                    }
                ],
                ],
            ]); ?>
        </div>
</div>
