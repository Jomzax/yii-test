<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "project".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $date_start
 * @property mixed $date_end
 * @property mixed $updated_at
 * @property mixed $created_by
 * @property mixed $updated_by
 */
class ProJect extends \yii\mongodb\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'date_start',
            'date_end',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'date_start', 'date_end', 'created_at', 'created_by', 'updated_at',  'updated_by'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'Name',
            'date_start' => 'วันที่เริ่มต้น',
            'date_end' => 'วันที่สิ้นสุด',
            'created_at' => 'วันที่สร้าง',
            'created_by' => 'ผู้สร้าง',
            'updated_at' => 'วันที่สร้าง',
            'updated_by' => 'สร้างโดย',
        ];
    }
}
