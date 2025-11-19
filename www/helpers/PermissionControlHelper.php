<?php

namespace app\helpers;

use app\models\MenuPermission;
use app\models\MenuRoles;
use Yii;
use yii\helpers\Url;

class PermissionControlHelper
{
    /**
     * ตรวจสิทธิ์เข้าถึงเมนู 
     */
    public static function haveAccessPermissionMenu(string $routeName): array
    {
        $user = Yii::$app->user;
        if ($user->isGuest) {
            return [
                'success'  => false,
                'text'     => 'กรุณาเข้าสู่ระบบก่อน',
                'type'     => 'warning',
                'redirect' => Url::to(['/site/login']),
            ];
        }

        $identity = $user->identity;
        $roles = $identity->roles ?? null;
        $isPermission = MenuPermission::find()->where(['abbr' => $routeName, 'role_id' => $roles])->one();

        if (empty($isPermission)) {
            return [
                'success'  => false,
                'text'     => 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้',
                'type'     => 'error',
                'redirect' => Url::to(['/site/index']),
            ];
        }

        return ['success' => true];
    }

    /**
     * ตรวจสิทธิ์ทำ Action (เช่น view/create/update/delete)
     */
    public static function getActionPermission(string $action, ?string $message = null): array
    {
        $user = Yii::$app->user;

        if ($user->isGuest) {
            return [
                'success'  => false,
                'text'     => 'กรุณาเข้าสู่ระบบก่อน',
                'type'     => 'warning',
                'redirect' => Url::to(['/site/login']),
            ];
        }

        $identity = $user->identity;
        $roles = $identity->roles ?? null;
        $isPermission = MenuPermission::find()->where(['abbr' => $action, 'role_id' => $roles])->one();
        if (empty($isPermission)) {
            return [
                'success'  => false,
                'text'     => $message ?? 'คุณไม่มีสิทธิ์ในการดำเนินการนี้',
                'type'     => 'error',
                'redirect' => Url::to(['/site/index']),
            ];
        }

        return ['success' => true];
    }

    /**
     * ตรวจสิทธิ์แบบ boolean (true/false)
     */
    public static function canAccessPermission(string $action): bool
    {
        $user = Yii::$app->user;
        if ($user->isGuest) return false;

        $identity = $user->identity;
        $roles = $identity->roles ?? null;
        $isPermission = MenuPermission::find()->where(['abbr' => $action, 'role_id' => $roles])->one();
        if (!empty($isPermission)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ดึงสิทธิ์สำหรับการแสดงผล (เช่น คอลัมน์ที่อนุญาตให้ดู)
     */
    // public static function getViewPermission(string $routeName): array
    // {
    //     $user = Yii::$app->user;
    //     if ($user->isGuest) {
    //         return [
    //             'success'     => false,
    //             'text'        => 'กรุณาเข้าสู่ระบบก่อน',
    //             'type'        => 'warning',
    //             'access_role' => null,
    //             'redirect'    => Url::to(['/site/login']),
    //         ];
    //     }

    //     $permissions = (array)($user->identity->permissions ?? []);
    //     if (in_array($routeName, $permissions, true)) {
    //         return [
    //             'success'     => true,
    //             'access_role' => $permissions,
    //         ];
    //     }

    //     return [
    //         'success'     => false,
    //         'text'        => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้',
    //         'type'        => 'error',
    //         'access_role' => null,
    //         'redirect'    => Url::to(['/site/index']),
    //     ];
    // }

    /* -------------------------------------------------
     * ✅ ตัวแปรคงที่ (Action Permission Codes)
     * -------------------------------------------------*/
    public const ACTION_PERMISSION_MENU_VIEW   = 'menu-view';
    public const ACTION_PERMISSION_MENU_CREATE = 'menu-create';
    public const ACTION_PERMISSION_MENU_UPDATE = 'menu-update';
    public const ACTION_PERMISSION_MENU_DELETE = 'menu-delete';

    public const ACTION_PERMISSION_ROLES_VIEW   = 'roles-view';
    public const ACTION_PERMISSION_ROLES_CREATE = 'roles-create';
    public const ACTION_PERMISSION_ROLES_UPDATE = 'roles-update';
    public const ACTION_PERMISSION_ROLES_DELETE = 'roles-delete';
}
