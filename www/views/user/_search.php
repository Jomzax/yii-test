<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-search card p-3 mb-3">
  <?php $form = ActiveForm::begin([
      'action' => ['index'],
      'method' => 'get',
  ]); ?>

    <div class="row g-3">
        <div class="col-md-6">
        <?= $form->field($model, 'username')->textInput([]); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<span class="glyphicon glyphicon-search me-1"></span> ค้นหา', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-refresh me-1"></span> คืนค่า', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
