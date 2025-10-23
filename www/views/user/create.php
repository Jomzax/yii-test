<?php

use yii\helpers\Html;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'สร้างผู้ใช้';
$this->params['breadcrumbs'][] = ['label' => 'ผู้ใช้', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
