<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Roles $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'จัดการบทบาท', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', '_id' => (string) $model->_id]];
$this->params['breadcrumbs'][] = 'แก้ไขจัดการบทบาท';
?>
<div class="roles-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
