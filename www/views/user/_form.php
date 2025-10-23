<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\models\User;
use app\helpers\RoleHelper;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form widget">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'roles')->dropDownList(RoleHelper::getLabels(), [
                        'prompt' => 'เลือกประเภท',
                        'class' => 'form-select form-control',
                        'style' => 'width: 100%;'
                ]) ?>
            </div>

        </div>

    <div class="mt-3 text-center">
        <?= Html::submitButton($model->isNewRecord ? 'บันทึก' : 'อัปเดต',['class' => 'btn btn-success']) ?>
        <?= Html::a('ยกเลิก',['index'],['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
