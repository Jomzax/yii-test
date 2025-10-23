<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\RoleHelper; // ✅ เพิ่ม import

/** @var yii\web\View $this */
/** @var app\models\Roles $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'จัดการบทบาท', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'รายละเอียดจัดการบทบาท';

$fmt = Yii::$app->formatter;
$fmt->timeZone = 'Asia/Bangkok';

// helper แปลงประเภทให้เป็นข้อความไทย (เผื่อ fallback)
$typeText = function($type) {
  if ($type === 'system') return 'ระบบ (System)';
  if ($type === 'custom') return 'กำหนดเอง (Custom)';
  // ถ้าใช้ RoleHelper แล้วไม่เจอ ให้คืนค่าดิบ
  return (string)$type;
};

// ฟอร์แมตเวลาให้ทนทั้ง int / string
$asDateTime = function($v) use ($fmt) {
  if (!$v) return '—';
  $ts = is_numeric($v) ? (int)$v : strtotime((string)$v);
  return $ts ? $fmt->asDatetime($ts, 'php:Y-m-d H:i') : '—';
};
?>
<div class="roles-view">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h1 class="h4 mb-2"><?= Html::encode($this->title) ?></h1>
      <div class="d-flex align-items-center gap-2">
        <!-- ลองใช้ RoleHelper ถ้าไม่มี label จะ fallback เป็น typeText -->
        <?php
          $label = RoleHelper::getLabels($model->type);
          $label = $label ?: $typeText($model->type);
        ?>
        <span class="badge bg-primary"><?= Html::encode($label) ?></span>
        <small class="text-muted">รหัส: <code class="user-select-all"><?= Html::encode((string)$model->_id) ?></code></small>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>รายละเอียดบทบาท</strong>
        </div>
        <div class="card-body">
          <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-borderless mb-0'],
            'template' => '<tr><th style="width:220px">{label}</th><td>{value}</td></tr>',
            'attributes' => [
              [
                'attribute' => 'name',
                'label' => 'ชื่อบทบาท',
                'value' => $model->name ?: '—',
              ],
              [
                'attribute' => 'type',
                'label' => 'ประเภท',
                'value' => Html::encode($label), // ✅ ใช้ label จาก RoleHelper
                'format' => 'raw',
              ],
            ],
          ]) ?>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>ข้อมูล</strong>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-5">วันที่สร้าง</dt>
            <dd class="col-7"><?= $asDateTime($model->created_at) ?></dd>

            <dt class="col-5">แก้ไขล่าสุด</dt>
            <dd class="col-7"><?= $asDateTime($model->updated_at) ?></dd>

            <dt class="col-5">ผู้สร้าง</dt>
            <dd class="col-7"><?= $model->created_by ? Html::encode((string)$model->created_by) : '—' ?></dd>

            <dt class="col-5">ผู้แก้ไข</dt>
            <dd class="col-7"><?= $model->updated_by ? Html::encode((string)$model->updated_by) : '—' ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4 text-center">
    <?= Html::a('กลับ', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
    <?= Html::a('แก้ไข', ['update', 'id' => (string)$model->_id], ['class' => 'btn btn-primary me-2']) ?>
    <?= Html::a('ลบ', ['delete', 'id' => (string)$model->_id], [
      'class' => 'btn btn-danger',
      'data' => ['confirm' => 'ยืนยันการลบบทบาทนี้หรือไม่?', 'method' => 'post'],
    ]) ?>
  </div>

</div>
