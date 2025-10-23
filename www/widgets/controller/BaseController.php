<?php

namespace app\widgets\controller;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;
use yii\helpers\Url;

/**
 * คอนโทรลเลอร์แม่ ที่รวม:
 *  - บังคับล็อกอิน (เก็บ returnUrl อัตโนมัติ)
 *  - ทางลัด login/logout flow (รวม goBack)
 *  - ตัวช่วย RBAC
 */
class BaseController extends Controller
{
    /** route ที่อนุญาตให้ Guest เข้าได้ (ตัวพิมพ์เล็ก) */
    protected array $guestAllowRoutes = [
        'site/login',
        'site/error',
        'site/captcha',
        // เพิ่มได้ตามต้องการ
        // 'site/request-password-reset',
        // 'site/signup',
    ];

    /** สำหรับลูกคลาสเปิด action แบบสาธารณะเพิ่มเติม: ['index','view'] */
    public array $publicActions = [];

    /** ใช้ชื่อพารามิเตอร์ returnUrl แยกจากของเดิมได้ */
    protected string $returnUrlParam = '__returnUrl';

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $route = strtolower($this->id . '/' . $action->id);

        // รายการ route ที่ปล่อย guest
        $allow = array_map('strtolower', array_merge(
            $this->guestAllowRoutes ?? ['site/login', 'site/error', 'site/captcha'],
            array_map(fn($a) => $this->id . '/' . $a, $this->publicActions ?? [])
        ));

        if (Yii::$app->user->isGuest && !in_array($route, $allow, true)) {
            // เก็บ URL เดิม (relative) เพื่อกลับมาหลังล็อกอิน
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);

            Yii::$app->response->redirect(['site/login']);
            return false; // ← หยุด action ทันที (ห้าม send() แล้ว return true)
        }

        return true;
    }

    /* ===========================
       SECTION: Login/Logout Helpers
       =========================== */

    /**
     * เส้นทางหน้า Login (แก้ที่นี่ที่เดียว ถ้าเปลี่ยน route)
     */
    protected function getLoginRoute(): string
    {
        return 'site/login';
    }

    /**
     * คืนค่า home URL (หน้า default หลังล็อกอิน ถ้าไม่มี returnUrl)
     */
    protected function getHomeUrl(): string
    {
        return Yii::$app->homeUrl ?: '/';
    }

    /**
     * เก็บ returnUrl โดยใช้คีย์เฉพาะของคลาสนี้
     */
    protected function setReturnUrl(string $url): void
    {
        Yii::$app->session->set($this->returnUrlParam, $url);
        // sync ให้ user component รู้ด้วย (รองรับ goBack() ของ Yii)
        Yii::$app->user->setReturnUrl($url);
    }

    /**
     * อ่าน returnUrl (ถ้าไม่มีจะคืน null)
     */
    protected function getReturnUrl(): ?string
    {
        $sessUrl = Yii::$app->session->get($this->returnUrlParam);
        return $sessUrl ?: Yii::$app->user->getReturnUrl();
    }

    /**
     * เคลียร์ returnUrl
     */
    protected function clearReturnUrl(): void
    {
        Yii::$app->session->remove($this->returnUrlParam);
        // Yii::$app->user->setReturnUrl(null); // ไม่จำเป็นต้องล้างก็ได้
    }

    /**
     * เรียกใน actionLogin ของ SiteController แทนการเขียน flow เอง
     * - โหลด/validate/login
     * - เด้งกลับหน้าเดิม (หรือหน้า Home) โดยจัดการ returnUrl ให้ครบ
     *
     * @param string $loginFormFqcn  FQCN ของแบบฟอร์ม login (เช่น app\models\LoginForm)
     * @param string $loginView      ชื่อวิวสำหรับแสดงฟอร์ม (เช่น 'login')
     * @param array  $extraParams    พารามิเตอร์ส่งให้วิว (เช่น คำอธิบายเพิ่ม)
     */
    protected function handleLoginFlow(
        string $loginFormFqcn = 'app\models\LoginForm',
        string $loginView = 'login',
        array $extraParams = []
    ): Response|string {
        if (!class_exists($loginFormFqcn)) {
            throw new \RuntimeException("Login form class not found: {$loginFormFqcn}");
        }

        if (!Yii::$app->user->isGuest) {
            return $this->redirect($this->getHomeUrl());
        }

        /** @var \yii\base\Model $model */
        $model = new $loginFormFqcn();

        // POST: พยายามล็อกอิน
        if ($model->load(Yii::$app->request->post()) && method_exists($model, 'login') && $model->login()) {
            // หลังล็อกอิน: เด้งกลับ returnUrl ถ้ามี, ไม่งั้นกลับ Home
            $target = $this->getReturnUrl() ?: $this->getHomeUrl();
            $this->clearReturnUrl();
            return $this->redirect($target);
        }

        // GET หรือ validate ไม่ผ่าน -> แสดงฟอร์ม
        if (property_exists($model, 'password')) {
            $model->password = ''; // ล้างช่องรหัสเพื่อความปลอดภัย
        }

        return $this->render($loginView, array_merge(['model' => $model], $extraParams));
    }

    /**
     * เรียกใน actionLogout ของ SiteController
     */
    protected function handleLogoutFlow(bool $destroySession = false): Response
    {
        Yii::$app->user->logout($destroySession);
        $this->clearReturnUrl();
        return $this->redirect($this->getHomeUrl());
    }

    /* ===========================
       SECTION: RBAC Helper
       =========================== */
    protected function requirePermission(string $permission): void
    {
        if (!Yii::$app->user->can($permission)) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }
    }
}
