<?php

namespace app\helpers;

use app\models\Roles;
use app\models\Users;
use Yii;

class RoleHelper
{
  public static function getLabels($key = null)
  {
    $labels = [
      Roles::TYPE_SUPER_ADMIN => 'ผู้ดูแลระบบสูงสุด (Super Admin)',
      Roles::TYPE_ADMIN => 'ผู้ดูแลระบบ (Admin)',
      Roles::TYPE_USER => 'ผู้ใช้ทั่วไป (User)',
    ];

    if (is_null($key)) {
      return $labels;
    } else {
      return $labels[$key] ?? null;
    }
  }

  public static function getTypesAvailableByType($type)
  {
    $types = [
      Roles::TYPE_SUPER_ADMIN => [
        Roles::TYPE_SUPER_ADMIN,
        Roles::TYPE_ADMIN,
        Roles::TYPE_USER,
      ],
      Roles::TYPE_ADMIN => [
        Roles::TYPE_ADMIN,
        Roles::TYPE_USER,
      ],
    ];

    return $types[$type] ?? [];
  }

  public static function getOrderType($type)
  {
    $orderTypes = [
      Roles::TYPE_SUPER_ADMIN => Roles::ORDER_TYPE_SUPER_ADMIN,
      Roles::TYPE_ADMIN => Roles::ORDER_TYPE_ADMIN,
      Roles::TYPE_USER => Roles::ORDER_TYPE_USER,
    ];

    return $orderTypes[$type] ?? 0;
  }

  public static function getRolesDisabled($roles, $roleTypeAvailable = [])
  {
    if (!is_array($roleTypeAvailable) || empty($roleTypeAvailable)) {
      return $roles;
    }

    return array_filter($roles, function ($item) use ($roleTypeAvailable) {
      return !in_array($item->type, $roleTypeAvailable);
    });
  }

  //public static function getMyPermissions()
  //{
    //$userId = (string)Yii::$app->user->identity->_id;

    //return UserHelper::getPermissionListByUserId($userId)['menu_allow'] ?? [];
  //}

  public static function getRoleNameById($id)
  {
    $role = Roles::findOne(['_id' => $id]);
    return $role->name ?? "";
  }

  public static function getRoleTypeById($id)
  {
    $role = Roles::findOne(['_id' => $id]);
    return $role->type ?? "";
  }

  public static function getIdsByRoleType($roleType)
  {
    $roles = Roles::find()->where(['type' => $roleType])->all();

    return array_map(function ($item) {
      return (string)$item->_id;
    }, $roles);
  }

  public static function isTypeUser($id)
  {
    $role = Roles::findOne(['_id' => $id]);
    return $role->type == Roles::TYPE_USER;
  }

  public static function amSuperAdmin($roleId = null)
  {
    if (!$roleId) {
      $roleId = Yii::$app->user->identity->role_id;
    }

    $role = Roles::findOne(['_id' => $roleId]);

    return ($role->type ?? "-") == Roles::TYPE_SUPER_ADMIN;
  }

  public static function getPermissionLabels()
  {
    return [
      'create' => 'สร้าง',
      'read'   => 'อ่าน',
      'update' => 'แก้ไข',
      'delete' => 'ลบ',
    ];
  }

  
}
