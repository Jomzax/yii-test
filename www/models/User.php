<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function collectionName()
    {
        return'user';
    }

      public function attributes()
    {
        return [
            '_id',
            'username',
            'password',
            'roles',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => 'กรุณากรอกข้อมูล {attribute}'],
            [['username', 'password', 'password_haah', 'roles', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        // เอาเท่าที่ใช้จริงตอนนี้
        return [
            '_id'=> 'ID',
            'username'=> 'ชื่อผู้ใช่',
            'password' => 'รหัสผ่าน',
            'password_haah' => 'รหัสผ่าน haah',
            'roles' => 'บทบาท',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
            'created_by' => 'สร้างโดย',
            'updated_by' => 'แก้ไขโดย',
        ];
    }

    /* ===== IdentityInterface ===== */
    public static function findIdentity($id)
    {
        // yii2-mongodb รองรับส่ง string id ได้
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; 
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        // แปลง _id เป็น string
        return (string)$this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return '';
    }
    public function validateAuthKey($authKey)
    {
        return true;
    }

    public function validatePassword($password)
    {
        // ในฐานมี field 'password' เป็น hash
        return Yii::$app->security->validatePassword($password, (string)$this->password);
    }

    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = (string)Yii::$app->user->id;
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = (string)Yii::$app->user->id;

        return parent::beforeSave($insert);
    }

    
}
