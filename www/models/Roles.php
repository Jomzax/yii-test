<?php

namespace app\models;

use Yii;
use app\helpers\RoleHelper;

/**
 * This is the model class for collection "roles".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $name
 * @property mixed $type
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $created_by
 * @property mixed $updated_by
 */
class Roles extends \yii\mongodb\ActiveRecord
{
    public $permissions;
    const TYPE_SUPER_ADMIN = 'super_admin';
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';

    const ORDER_TYPE_SUPER_ADMIN = 1;
    const ORDER_TYPE_ADMIN = 2;
    const ORDER_TYPE_USER = 3;
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'type',
            'role_id',  
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'menu',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required', 'message' => 'กรุณากรอกข้อมูล {attribute}'],
            [['name', 'type', 'role_id','created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            ['type', 'in', 'range' => array_keys(RoleHelper::getLabels())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => 'ขื่อบทบาท',
            'type' => 'ประเภทบทบาท',
            'role_id' => 'Role ID',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
            'created_by' => 'สร้างโดย',
            'updated_by' => 'แก้ไขโดย',
            'permissions' => 'สิทธิ์',
            'menu' =>'เมนู',
        ];
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

    public static function getPermissionByRoleId($roleId)
    {
        $role = self::findOne(['_id' => (string)$roleId]);

        return array_merge($role->menu_allow ?? [],  []);
    }

    public static function getRolesName($id)
    {
        $rolesName = self::findOne(['_id' => $id]);
        return  $rolesName ? $rolesName->name : null;
    }

    public static function getRolesList(){
        $roles = self::find()->all();
        $roleList = [];
        foreach ($roles as $role) {
            $roleList[(string)$role->_id] = $role->name;
        }
        return $roleList;
    }

}
