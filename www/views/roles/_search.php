<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\helpers\RoleHelper;

/** @var yii\web\View $this */
/** @var app\models\RolesSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="roles-search card p-3 mb-3">
  <?php $form = ActiveForm::begin([
      'action' => ['index'],
      'method' => 'get',
  ]); ?>

  <div class="row g-3">
    <div class="col-md-6">
      <?= $form->field($model, 'name')->textInput([]); ?>
    </div>
     <div class="col-md-6">
      <?= $form->field($model, 'type')->dropDownList(RoleHelper::getLabels(), [
        'prompt' => 'เลือกประเภท',
        'class' => 'form-select form-control',
        'style' => 'width: 100%;'
      ]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<span class="glyphicon glyphicon-search me-1"></span> ค้นหา', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-refresh me-1"></span> คืนค่า', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>
