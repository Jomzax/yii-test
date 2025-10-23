<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Menu $model */

$permissions = isset($permissions) && is_array($permissions) ? $permissions : [['abbr' => '', 'name' => '']];


$css = <<<CSS
.permission-item.dragging { opacity: .6; }
.drag-handle { cursor: grab; user-select: none; }
.drag-handle:active { cursor: grabbing; }
.permission-item.drag-over-top { border-top: 2px dashed var(--bs-primary, #0d6efd); }
.permission-item.drag-over-bottom { border-bottom: 2px dashed var(--bs-primary, #0d6efd); }
CSS;
$this->registerCss($css);
?>

<div class="widget">
  <header>
    <h5>ข้อมูลเมนู</h5>
  </header>

  <div class="widget-body">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
      <div class="col-md-6">
        <?= $form->field($model, 'name') ?>
      </div>
      <div class="col-md-6">
        <?= $form->field($model, 'link') ?>
      </div>

    </div>

    <?php if (!$model->isNewRecord): ?>
      <br>
      <h6>รายการสิทธิ์</h6>
      <hr class="section-divider" />

      <div id="permissions-wrapper" class="mb-3">
        <div id="permissions-container">
          <?php foreach ($permissions as $i => $p): ?>
            <div class="permission-item row g-2 mb-2">
              <div class="col-md-5">
                <?= Html::textInput("Menu[permission_lists][$i][abbr]", $p['abbr'] ?? '', [
                  'class' => 'form-control',
                  'placeholder' => 'abbr',
                  'data-field' => 'abbr',
                ]) ?>
              </div>
              <div class="col-md-5">
                <?= Html::textInput("Menu[permission_lists][$i][name]", $p['name'] ?? '', [
                  'class' => 'form-control',
                  'placeholder' => 'Name',
                  'data-field' => 'name',
                ]) ?>
              </div>
              <div class="col-md-2 d-flex gap-1 justify-content-end">
                <button type="button" class="btn btn-outline-secondary btn-sm drag-handle" title="ลากเพื่อสลับตำแหน่ง">↕︎</button>
                <button type="button" class="btn btn-danger btn-sm remove-permission">ลบ</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="text-end">
          <button type="button" class="btn btn-success btn-sm" id="add-permission">+ เพิ่มสิทธิ์</button>
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group mt-4 d-flex justify-content-center gap-2">
      <?= Html::submitButton('<span class="glyphicon glyphicon-save"></span> บันทึกข้อมูล', ['class' => 'btn btn-primary']) ?>
      <?= Html::a('<span class="glyphicon glyphicon-backward"></span> กลับ', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>

<template id="permission-template">
  <div class="permission-item row g-2 mb-2">
    <div class="col-md-5">
      <input type="text" class="form-control" placeholder="abbr" data-field="abbr">
    </div>
    <div class="col-md-5">
      <input type="text" class="form-control" placeholder="name" data-field="name">
    </div>
    <div class="col-md-2 d-flex gap-1 justify-content-end">
      <button type="button" class="btn btn-outline-secondary btn-sm drag-handle" title="ลากเพื่อสลับตำแหน่ง">↕︎</button>
      <button type="button" class="btn btn-danger btn-sm remove-permission">ลบ</button>
    </div>
  </div>
</template>

<?php
$js = <<<JS
(function(){
  const container = document.getElementById('permissions-container');
  const addBtn    = document.getElementById('add-permission');
  const tmpl      = document.getElementById('permission-template');

  function reindex(){
    const rows = container.querySelectorAll('.permission-item');
    rows.forEach((row, idx) => {
      const abbrInput = row.querySelector('input[data-field="abbr"]');
      const nameInput = row.querySelector('input[data-field="name"]');
      if (abbrInput) abbrInput.name = `Menu[permission_lists][\${idx}][abbr]`;
      if (nameInput) nameInput.name = `Menu[permission_lists][\${idx}][name]`;
    });
  }

  function addRow(defaults){
    const node = tmpl.content.cloneNode(true);
    const abbrInput = node.querySelector('input[data-field="abbr"]');
    const nameInput = node.querySelector('input[data-field="name"]');
    if (defaults && typeof defaults === 'object') {
      if (abbrInput) abbrInput.value = defaults.abbr || '';
      if (nameInput) nameInput.value = defaults.name || '';
    }
    container.appendChild(node);
    reindex();
  }

  // Add row
  addBtn.addEventListener('click', function(){ addRow(); });

  // Remove row (เว้นให้เหลือแถวว่างอย่างน้อย 1)
  container.addEventListener('click', function(e){
    if (e.target && e.target.classList.contains('remove-permission')) {
      const rows = container.querySelectorAll('.permission-item');
      if (rows.length <= 1) {
        const abbrInput = rows[0].querySelector('input[data-field="abbr"]');
        const nameInput = rows[0].querySelector('input[data-field="name"]');
        if (abbrInput) abbrInput.value = '';
        if (nameInput) nameInput.value = '';
        return;
      }
      e.target.closest('.permission-item').remove();
      reindex();
    }
  });

  // --- Drag & Drop ---
  let dragSrc = null;
  let dragActivatedRow = null;

  // อนุญาตให้ลากได้เมื่อกดที่ปุ่ม .drag-handle เท่านั้น
  container.addEventListener('pointerdown', (e) => {
    const handle = e.target.closest('.drag-handle');
    if (!handle) return;
    const row = handle.closest('.permission-item');
    if (!row) return;
    row.setAttribute('draggable', 'true');
    dragActivatedRow = row;
  });

  // ถ้าไม่ได้ลากจริง ให้ปิด draggable กลับ
  window.addEventListener('pointerup', () => {
    if (dragActivatedRow) {
      dragActivatedRow.removeAttribute('draggable');
      dragActivatedRow = null;
    }
  });

  container.addEventListener('dragstart', (e) => {
    const row = e.target.closest('.permission-item');
    if (!row || !row.hasAttribute('draggable')) { e.preventDefault(); return; }
    dragSrc = row;
    row.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', ''); // บางเบราว์เซอร์บังคับให้ setData
  });

  container.addEventListener('dragover', (e) => {
    e.preventDefault();
    const target = e.target.closest('.permission-item');
    // ล้างไฮไลต์ก่อน
    container.querySelectorAll('.drag-over-top,.drag-over-bottom').forEach(el => {
      el.classList.remove('drag-over-top','drag-over-bottom');
    });
    if (!target || target === dragSrc) return;

    const rect = target.getBoundingClientRect();
    const before = (e.clientY - rect.top) < rect.height / 2;
    target.classList.add(before ? 'drag-over-top' : 'drag-over-bottom');
  });

  container.addEventListener('dragleave', (e) => {
    const row = e.target.closest('.permission-item');
    if (row) row.classList.remove('drag-over-top','drag-over-bottom');
  });

  container.addEventListener('drop', (e) => {
    e.preventDefault();
    const target = e.target.closest('.permission-item');

    container.querySelectorAll('.drag-over-top,.drag-over-bottom').forEach(el => {
      el.classList.remove('drag-over-top','drag-over-bottom');
    });

    if (!target || target === dragSrc) return;

    const rect = target.getBoundingClientRect();
    const before = (e.clientY - rect.top) < rect.height / 2;

    if (before) {
      container.insertBefore(dragSrc, target);
    } else {
      container.insertBefore(dragSrc, target.nextElementSibling);
    }
    reindex();
  });

  container.addEventListener('dragend', (e) => {
    const row = e.target.closest('.permission-item');
    if (row) {
      row.classList.remove('dragging');
      row.removeAttribute('draggable'); // ปิด draggable กลับเพื่อกันลากพลาด
    }
    dragSrc = null;
    container.querySelectorAll('.drag-over-top,.drag-over-bottom').forEach(el => {
      el.classList.remove('drag-over-top','drag-over-bottom');
    });
  });

  // init
  reindex();
})();
JS;
$this->registerJs($js);
?>