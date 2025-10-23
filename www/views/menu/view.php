<?php

use app\helpers\DateHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Menu $model */

$this->title = "รายละเอียดเมนู";
$this->params['breadcrumbs'][] = ['label' => 'เมนูทั้งหมด', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="widget">
    <div class="widget-body">
        <div>
            <h6 class="font-weight-bold">
                ข้อมูลเมนู
            </h6>
        </div>

        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">ชื่อเมนู :</span>
            </div>
            <div class="col-md-8">
                <?= $model->name ?? "" ?>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">ลิ้งค์เมนู :</span>
            </div>
            <div class="col-md-8">
                <?= $model->link ?? "" ?>
            </div>
        </div>


        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">วันที่สร้างเมนู :</span>
            </div>
            <div class="col-md-8">
                <?= DateHelper::DateThaiTime($model->created_at) ?? "" ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">สร้างเมนูโดย :</span>
            </div>
            <div class="col-md-8">
                <?= $model->created_by ?? "" ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">วันที่แก้ไขล่าสุด :</span>
            </div>
            <div class="col-md-8">
                <?= DateHelper::DateThaiTime($model->updated_at) ?? "" ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 form-control-label text-md-end">
                <span class="font-weight-bold">แก้ไขเมนูโดย :</span>
            </div>
            <div class="col-md-8">
                <?= $model->updated_by ?? "" ?>
            </div>
        </div>

        <div class="form-group d-flex justify-content-center gap-2">
            <?= Html::a('<span class="glyphicon glyphicon-backward"></span> กลับ', ['index'], ['class' => 'btn btn-default']) ?>
        </div>

    </div>
</div>