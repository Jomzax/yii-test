<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "menu".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $link
 * @property mixed $permission_lists
 * @property mixed $created_at
 * @property mixed $created_by
 * @property mixed $updated_at
 * @property mixed $updated_by
 */
class Menu extends \yii\mongodb\ActiveRecord
{   
    public $permission_lists;
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'link',
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
            [['name', 'link', 'permission_lists', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'ชื่อ',
            'link' => 'ลิงค์',
            'permission_lists' => 'รายงานสิทธิ์การเข้าถึง',
            'created_at' => 'สร้างเมื่อ',
            'created_by' => 'สร้างโดย',
            'updated_at' => 'แก้ไขเมื่อ',
            'updated_by' => 'แก้ไขโดย',
        ];
    }

    public function beforeSave($insert)
    {
    if ($insert) {
      $this->created_at = date('Y-m-d H:i:s', strtotime('+7 hours'));
      $this->created_by = (string)Yii::$app->user->id;
    }

    $this->updated_at = date('Y-m-d H:i:s', strtotime('+7 hours'));
    $this->updated_by = (string)Yii::$app->user->id;

    return parent::beforeSave($insert);
    }

  
}
