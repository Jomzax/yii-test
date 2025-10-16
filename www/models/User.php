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
        // เอาเท่าที่ใช้จริงตอนนี้
        return ['_id', 'username', 'password'];
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
}
