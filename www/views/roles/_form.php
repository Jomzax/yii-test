<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\helpers\RoleHelper;
use app\models\Menu;
use app\models\Roles;


/** @var yii\web\View $this */
/** @var app\models\Roles $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="roles-form card shadow-sm p-4">
  <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'type')->dropDownList(RoleHelper::getLabels(), [
                    'prompt' => 'เลือกประเภท',
                    'class' => 'form-select form-control',
                    'style' => 'width: 100%;'
            ]) ?>
        </div>
    </div>
    
  <?php if (!$model->isNewRecord): ?>
    <hr>
    <h6 class="mb-3">กำหนดสิทธิ์การเข้าถึงเมนู</h6>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th style="width: 6%">ลำดับ</th>
            <th style="width: 34%">ชื่อเมนู</th>
            <th>เมนู</th>
            <th>สร้าง</th>
            <th>แก้ไข</th>
            <th>ลบ</th>
            <th>ดู</th>
          </tr>
        </thead>
        <tbody>
          <?php

          $menus = Menu::find()->all();
          $selected = (array)$model->permissions;   // เช่น ['org-master_menu', 'org-master_create']

          $i = 1;
          foreach ($menus as $menu):
              $base = $menu->link; // ใช้ link เป็นรหัสหลัก เช่น "org-master"

              // สิทธิ์ทั้งหมด (เพิ่ม menu เข้าไปด้วย)
              $actions = [
                  'menu'   => 'เมนู',
                  'create' => 'สร้าง',
                  'update' => 'แก้ไข',
                  'delete' => 'ลบ',
                  'view'   => 'ดู'
              ];

              echo "<tr>";
              echo "<td class='text-center'>{$i}</td>";
              echo "<td>" . htmlspecialchars($menu->name, ENT_QUOTES, 'UTF-8') . "</td>";

              foreach ($actions as $key => $label) {
                  $permVal   = "{$base}-{$key}";
                  $isChecked = in_array($permVal, $selected) ? 'checked' : '';
                  echo "<td class='text-center'>
                          <input type='checkbox' name='Perm[]' value='{$permVal}' class='form-check-input' {$isChecked}>
                        </td>";
              }

              echo "</tr>";
              $i++;
          endforeach;
          ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>



    <div class="mt-3 text-center">
        <?= Html::submitButton($model->isNewRecord ? 'บันทึก' : 'อัปเดต',['class' => 'btn btn-success']) ?>
        <?= Html::a('ยกเลิก',['index'],['class' => 'btn btn-secondary ms-2']) ?>
    </div>

  <?php ActiveForm::end(); ?>
</div>
