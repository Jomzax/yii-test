<?php

namespace app\controllers;

use app\helpers\AlertHelper;
use app\helpers\ErrorHelper;
use app\models\Menu;
use app\models\MenuSearch;
use app\widgets\controller\BaseController;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Permissoin;
use MongoDB\BSON\ObjectId;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Menu models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param int $_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($_id),
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Menu();

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
                $menuId = (string)$model->_id;

                // ✅ บันทึกลงคอลเลกชัน "permissoin" โดยเก็บ menu_id
                $perm = new Permissoin();          // ❌ ห้ามใช้ yii\mongodb\rbac\Permission
                $perm->menu_id = $menuId;
                // ใส่ค่าตัวเลือกอื่นได้ตามต้องการ
                // $perm->name = $model->name;
                // $perm->abbr = 'a.b.b.r';
                $perm->save(false);

                \app\helpers\AlertHelper::alert('success', 'บันทึกข้อมูลสำเร็จ');
                return $this->redirect(['index']);
            }

            \app\helpers\AlertHelper::alert('warning', 'ไม่สามารถบันทึกข้อมูลได้');
        }

        return $this->render('create', [
            'model' => $model,

        ]);
    }


    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $_id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    
    public function actionUpdate($_id)
    {
        $model  = $this->findModel($_id);
        $menuId = (string)$model->_id;

        // 1) ดึงสิทธิ์เดิมจาก collection "permissoin"
        $permDocs = Permissoin::find()->where(['menu_id' => $menuId])->all();
        $permissions = [];
        foreach ($permDocs as $doc) {
            $permissions[] = [
                'abbr' => (string)($doc->abbr ?? ''),
                'name'  => (string)($doc->name  ?? ''),   // ถ้าเคยใช้ label ให้แก้ดึงมาเป็น name ให้เรียบร้อย
            ];
        }

        if (empty($permissions)) {
            $permissions = [['abbr' => '', 'name' => '']];
        } // ให้มีแถวว่าง 1 แถว


        // 2) รับ POST แล้ว "แยก" permission_lists ออกมาก่อน จากนั้นลบทิ้งใน payload ของ Menu
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            // เอาไว้ sync กับคอลเลกชัน permissoin
            $postPerms = $post['Menu']['permission_lists'] ?? [];

            // ตัด permission_lists ออกจากข้อมูลของ Menu เพื่อไม่ให้ถูก save ลง collection "menu"
            unset($post['Menu']['permission_lists']);

            // 3) ค่อย load/save เฉพาะฟิลด์ของ Menu จริง ๆ
            if ($model->load($post) && $model->save()) {

                // 4) จากนั้นจึง sync สิทธิ์ไปที่คอลเลกชัน "permissoin"
                $clean = [];
                foreach ((array)$postPerms as $row) {
                    $v = trim($row['abbr'] ?? '');
                    $n = trim($row['name']  ?? '');
                    if ($v === '' && $n === '') continue;
                    $clean[] = ['abbr' => $v, 'name' => ($n !== '' ? $n : $v)];
                }

                // ลบสิทธิ์ที่ไม่อยู่แล้ว
                $keepAbbs = array_map(fn($r) => $r['abbr'], $clean);
                if (empty($keepAbbs)) {
                    Permissoin::deleteAll(['menu_id' => $menuId]);
                } else {
                    Permissoin::deleteAll([
                        'menu_id' => $menuId,
                        'abbr'    => ['$nin' => $keepAbbs],
                    ]);
                }

                // upsert
                foreach ($clean as $r) {
                    $doc = Permissoin::findOne(['menu_id' => $menuId, 'abbr' => $r['abbr']]);
                    if ($doc === null) {
                        $doc = new Permissoin();
                        $doc->menu_id = $menuId;
                        $doc->abbr    = $r['abbr'];
                    }
                    $doc->name = $r['name'];
                    $doc->save(false);
                }

                \app\helpers\AlertHelper::alert('success', 'บันทึกข้อมูลสำเร็จ');
                return $this->redirect(['index']);
            }

            \app\helpers\AlertHelper::alert('warning', 'ไม่สามารถบันทึกข้อมูลได้');
        }

        // 5) ส่ง $permissions ไปที่ view เพื่อแสดงค่าเดิมในฟอร์ม
        return $this->render('update', [
            'model'       => $model,
            'permissions' => $permissions,
        ]);
    }



    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $_id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($_id)
    {
        $model = $this->findModel($_id);
        $menuId = (string)$model->_id; // แปลง ObjectId เป็น string เพื่อใช้ลบใน permissoin

        if ($model->delete()) {

            // ✅ ลบสิทธิ์ทั้งหมดของเมนูนี้ด้วย
            Permissoin::deleteAll(['menu_id' => $menuId]);

            AlertHelper::alert('success', 'ลบข้อมูลสำเร็จ');
        } else {
            $errValue = ErrorHelper::getErrorsValueArray($model->errors ?? []);
            AlertHelper::alert('warning', [
                'title' => 'ไม่สามารถลบข้อมูลได้',
                'text' => $errValue[0] ?? "",
            ]);
        }

        return $this->redirect(['index']);
    }


    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $_id ID
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($_id)
    {
        $m = Menu::findOne(['_id' => new ObjectId($_id)]);
        if ($m === null) throw new \yii\web\NotFoundHttpException('ไม่พบข้อมูลเมนู');
        return $m;
    }
}
