<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProJect $model */

$this->title = 'Create Pro Ject';
$this->params['breadcrumbs'][] = ['label' => 'Pro Jects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pro-ject-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>