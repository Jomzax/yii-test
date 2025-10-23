<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $password;
    public static function collectionName()
    {
        return 'user';
    }

    public function attributes()
    {
        return [
            '_id',
            'username',
            'password_hash',
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
            [['username', 'password', 'password_hash', 'roles', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        // เอาเท่าที่ใช้จริงตอนนี้
        return [
            '_id' => 'ID',
            'username' => 'ชื่อผู้ใช่',
            'password' => 'รหัสผ่าน',
            'password_hash' => 'รหัสผ่าน hash',
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
        // ใช้ hash จากฐานข้อมูล (password_hash)
        $hash = (string)$this->password_hash;

        // ป้องกันเคสไม่มี hash (เช่น user เก่า)
        if (empty($hash)) {
            return false;
        }

        // ตรวจสอบรหัสผ่านจริงกับ hash ในฐาน
        return Yii::$app->security->validatePassword($password, $hash);
    }



    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->created_by = (string)Yii::$app->user->id;
        }

        $this->updated_at = date('Y-m-d H:i:s');
        $this->updated_by = (string)Yii::$app->user->id;

        // ถ้าฟอร์มกรอกรหัสผ่านมา ให้สร้าง hash แล้วเขียนลง password_hash
        if (!empty($this->password)) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $this->password = null; // เคลียร์ค่า plain
        }

        return parent::beforeSave($insert);
    }
}
