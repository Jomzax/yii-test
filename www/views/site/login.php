<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

$this->title = 'เข้าสู่ระบบ';
?>

<div class="text-center mb-4">
    <h3 class="fw-bold text-primary"><?= Html::encode($this->title) ?></h3>
    <p class="text-muted small">กรุณากรอกชื่อผู้ใช้และรหัสผ่านเพื่อเข้าสู่ระบบ</p>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['autocomplete' => 'off', 'class' => 'needs-validation'],
    'fieldConfig' => [
        'template' => "<div class=\"form-floating mb-3\">{input}\n{label}\n{error}</div>",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback d-block small'],
    ],
]); ?>

<?= $form->field($model, 'username')
    ->textInput(['placeholder' => 'ชื่อผู้ใช้', 'autofocus' => true])
    ->label('ชื่อผู้ใช้') ?>

<?= $form->field($model, 'password')
    ->passwordInput(['placeholder' => 'รหัสผ่าน'])
    ->label('รหัสผ่าน') ?>

<div class="form-check mb-3">
    <?= $form->field($model, 'rememberMe', [
        'template' => "{input} {label}\n{error}",
        'options' => ['class' => 'form-check'],
    ])->checkbox(['class' => 'form-check-input'], false)->label('จดจำฉันไว้ในระบบ', ['class' => 'form-check-label']) ?>
</div>

<div class="d-grid gap-2">
    <?= Html::submitButton('เข้าสู่ระบบ', ['class' => 'btn btn-primary btn-lg fw-semibold', 'name' => 'login-button']) ?>
</div>

<div class="text-center gap-2 mt-3">
    <?= Html::a('สมัครสมาชิก', ['site/signup'], ['class' => 'btn btn-outline-success btn-lg fw-semibold']) ?>
</div>

<?php ActiveForm::end(); ?>