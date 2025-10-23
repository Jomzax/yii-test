<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\helpers\RoleHelper;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'รายละเอียดผู้ใช้';
$this->params['breadcrumbs'][] = ['label' => 'ผู้ใช้', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$fmt = Yii::$app->formatter;
$fmt->timeZone = 'Asia/Bangkok';

// ฟังก์ชันแปลงเวลา (รับได้ทั้ง string และ timestamp)
$asDateTime = function($v) use ($fmt) {
    if (!$v) return '—';
    $ts = is_numeric($v) ? (int)$v : strtotime((string)$v);
    return $ts ? $fmt->asDatetime($ts, 'php:Y-m-d H:i') : '—';
};
?>
<div class="user-view container-fluid py-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h1 class="h4 mb-2"><?= Html::encode($this->title) ?></h1>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary">บัญชีผู้ใช้</span>
        <small class="text-muted">
          รหัส: <code class="user-select-all"><?= Html::encode((string)$model->_id) ?></code>
        </small>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="row g-3">
    <!-- Left: รายละเอียดผู้ใช้ -->
    <div class="col-lg-8">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>ข้อมูลผู้ใช้</strong>
        </div>
        <div class="card-body">
          <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-borderless mb-0'],
            'template' => '<tr><th style="width:220px">{label}</th><td>{value}</td></tr>',
            'attributes' => [
              [
                'attribute' => 'username',
                'label' => 'ชื่อผู้ใช้',
                'value' => $model->username ?: '—',
              ],
              [
                'attribute' => 'password',
                'label' => 'รหัสผ่าน',
                'value' => Html::encode($model->password),
                'format' => 'raw',
              ],
              [
                'attribute' => 'roles',
                'label' => 'บทบาท',
                'value' => RoleHelper::getLabels($model->roles),
                'format' => 'raw',
              ],
            ],
          ]) ?>
        </div>
      </div>
    </div>

    <!-- Right: ข้อมูลระบบ -->
    <div class="col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>ข้อมูลระบบ</strong>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-5">วันที่สร้าง</dt>
            <dd class="col-7"><?= $asDateTime($model->created_at ?? null) ?></dd>

            <dt class="col-5">แก้ไขล่าสุด</dt>
            <dd class="col-7"><?= $asDateTime($model->updated_at ?? null) ?></dd>

            <dt class="col-5">สร้างโดย</dt>
            <dd class="col-7"><?= $model->created_by ? Html::encode((string)$model->created_by) : '—' ?></dd>

            <dt class="col-5">แก้ไขโดย</dt>
            <dd class="col-7"><?= $model->updated_by ? Html::encode((string)$model->updated_by) : '—' ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Buttons -->
  <div class="mt-4 text-center">
    <?= Html::a('ย้อนกลับ', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
  </div>
</div>
