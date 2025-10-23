<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\SignupForm;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['logout'],
                    'rules' => [
                        [
                            'actions' => ['logout'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        // 'logout' => ['post'],
                        'update-session' => ['post'],
                    ],
                ],
            ]
        );
    }

    public function beforeAction($action)
    {
        $actionCheckSessionWithOutUpdate = ['dashboard', 'index'];

        if (in_array($action->id, $actionCheckSessionWithOutUpdate)) {
            return parent::beforeAction($action);
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //         ],
    //         'captcha' => [
    //             'class' => 'yii\captcha\CaptchaAction',
    //             'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
    //         ],
    //     ];
    // }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }

        $this->layout = 'auth';

        $model = new LoginForm();

        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            // ✅ ถ้าล็อกอินสำเร็จ ให้กลับหน้า Home ทันที
            return $this->redirect(['site/index']);
        }

        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }



    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $user = new \app\models\User();
            $user->username = $model->username;
            $user->password = Yii::$app->security->generatePasswordHash($model->password);

            if ($user->save(false)) {
                Yii::$app->session->setFlash('success', 'สมัครสมาชิกสำเร็จแล้ว!');
                return $this->redirect(['site/login']);
            }

            Yii::$app->session->setFlash('error', 'บันทึกไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        }

        return $this->render('signup', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->destroy(); // ถ้าต้องการล้าง session ทั้งหมด
        return $this->redirect(['site/login']);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
