<?php

use app\helpers\AccessControlHelper;
use app\models\Secret;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\widgets\MoobaanAutocomplete;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="forest-form widget">


    <header>
        <h5 class="mt-2">
            ข้อมูลการลักลอบตัดไม้ทำลายป่า
        </h5>
    </header>

    <div class="widget-body">
        <?php $form = ActiveForm::begin([
            'id' => 'forest-form',
            'enableClientValidation' => true,
            'fieldConfig' => [],
            'options' => ['autocomplete' => 'off'],
        ]);
        ?>

        <br>
        <h6>ข้อมูลเบื้องต้น</h6>
        <hr class="section-divider">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'date_start')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'date_end')->textInput() ?>
            </div>
        </div>



    </div>
    <div class="form-group mt-4 d-flex justify-content-center gap-2">
        <?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> บันทึกข้อมูล', ['class' => 'btn btn-primary', 'id' => 'btn-submit-form']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-backward"></span> กลับ', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>