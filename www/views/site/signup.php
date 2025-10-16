<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign Up';
?>

<div class="site-signup container" style="max-width:640px">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Create your account</p>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'form-signup',
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'options' => ['autocomplete' => 'off'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n<small class=\"text-danger\">{error}</small>",
            'labelOptions' => ['class' => 'form-label fw-semibold'],
            'options' => ['class' => 'mb-3']
        ],
    ]); ?>


    <?= $form->field($model, 'username')->textInput([
        'maxlength' => true,
        'placeholder' => 'Username',
        'autofocus' => true,
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput([
        'maxlength' => true,
        'placeholder' => 'Password',
    ]) ?>

    <div class="d-grid gap-2 mt-2">
        <?= Html::submitButton('Sign up', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <p class="mt-3 text-center">
        Already have an account?
        <?= Html::a('Log in', 'login') ?>
    </p>

    <?php ActiveForm::end(); ?>
</div>