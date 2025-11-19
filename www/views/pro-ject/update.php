<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProJect $model */

$this->title = 'Update Pro Ject: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pro Jects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', '_id' => (string) $model->_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pro-ject-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
