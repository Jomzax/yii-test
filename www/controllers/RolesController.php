<?php

namespace app\controllers;

use Yii;
use app\models\Roles;
use app\models\RolesSearch;
use app\helpers\AlertHelper;
use app\helpers\ErrorHelper;
use app\helpers\RoleHelper;
use app\models\Menu;
use app\models\MenuPermissoin;
use app\models\MenuRoles;
use app\models\Permissoin;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use MongoDB\BSON\ObjectId;

class RolesController extends Controller
{
    public function behaviors()
    {
        return [
            // ป้องกันการลบด้วย GET
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new RolesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionView($_id)
    {
        $model = $this->findModel($_id);
        return $this->render('view', compact('model'));
    }

    public function actionCreate()
    {
        $model = new Roles();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'บันทึกบทบาทสำเร็จ');
            return $this->redirect(['view', 'id' => (string)$model->_id]);
        }
        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model  = $this->findModel($id);
        $roleId = (string)$model->_id;

        // ---------- ค่า checked เริ่มต้นให้ฟอร์ม ----------
        $menus = \app\models\Menu::find()->all();

        // เมนูที่ role นี้มีอยู่แล้ว -> map menu_id => menuroles_id
        $menuRoleRows    = \app\models\MenuRoles::find()->where(['role_id' => $roleId])->all();
        $menuCheckedById = [];
        foreach ($menuRoleRows as $r) {
            $menuCheckedById[(string)$r->menu_id] = (string)$r->_id;
        }

        // สิทธิ์ (abbr) ที่มีอยู่แล้วภายใต้ menuroles_ids ของ role นี้
        $permCheckedAbbrs = [];
        if (!empty($menuCheckedById)) {
            $permCheckedAbbrs = \app\models\MenuPermissoin::find()
                ->select(['abbr'])
                ->where(['menuroles_id' => ['$in' => array_values($menuCheckedById)]])
                ->column(); // ['org-master-create', ...]
        }

        // ทำ selected สำหรับฟอร์ม เช่น ['org-master-menu','org-master-create',...]
        $selected = [];
        foreach ($menus as $m) {
            $mid  = (string)$m->_id;
            $base = $m->link; // เช่น org-master
            if (isset($menuCheckedById[$mid])) {
                $selected[] = "{$base}-menu";
            }
        }
        $selected = array_values(array_unique(array_merge($selected, $permCheckedAbbrs)));
        $model->permissions = $selected;

        // ---------- รับ POST ----------
        if (\Yii::$app->request->isPost) {
            $post        = \Yii::$app->request->post();
            $postedPerms = array_values(array_unique($post['Perm'] ?? [])); // ['org-master-menu','org-master-create',...]

            if ($model->load($post) && $model->save()) {

                // Map เมนู: base(link) -> {id, link, name}
                $menuMap = [];
                foreach ($menus as $m) {
                    $menuMap[$m->link] = [
                        'id'   => (string)$m->_id,
                        'link' => $m->link,
                        'name' => $m->name,
                    ];
                }

                // ---------- 1) SYNC menu_roles จากค่า *-menu ----------
                $selectedMenuIds = [];
                foreach ($postedPerms as $p) {
                    if (str_ends_with($p, '-menu')) {
                        $base = substr($p, 0, -5); // ตัด "-menu"
                        if (isset($menuMap[$base])) {
                            $selectedMenuIds[] = $menuMap[$base]['id'];
                        }
                    }
                }
                $selectedMenuIds = array_values(array_unique($selectedMenuIds));

                if (empty($selectedMenuIds)) {
                    // ไม่เลือกเมนูเลย -> ลบ menu_roles ทั้งหมดของ role นี้ + ลูก menu_permissoin ทั้งหมด
                    \app\models\MenuRoles::deleteAll(['role_id' => $roleId]);

                    $orphans = array_values($menuCheckedById);
                    if ($orphans) {
                        \app\models\MenuPermissoin::deleteAll(['menuroles_id' => ['$in' => $orphans]]);
                    }
                    $menuCheckedById = [];
                } else {
                    // ลบเฉพาะ menu ที่เคยมีแต่ถูกยกเลิกติ๊ก (และลบลูก)
                    $toDeleteMenuIds = array_diff(array_keys($menuCheckedById), $selectedMenuIds);
                    if ($toDeleteMenuIds) {
                        // เก็บ menuroles_id ของเมนูที่จะลบ เพื่อไปลบลูก
                        $delMenuRolesIds = [];
                        foreach ($toDeleteMenuIds as $mid) {
                            if (isset($menuCheckedById[$mid])) $delMenuRolesIds[] = $menuCheckedById[$mid];
                        }

                        // ลบลูกก่อน
                        if ($delMenuRolesIds) {
                            \app\models\MenuPermissoin::deleteAll(['menuroles_id' => ['$in' => $delMenuRolesIds]]);
                        }
                        // ลบ menu_roles
                        \app\models\MenuRoles::deleteAll(['role_id' => $roleId, 'menu_id' => ['$in' => array_values($toDeleteMenuIds)]]);
                    }

                    // upsert menu_roles สำหรับเมนูที่ถูกเลือก
                    foreach ($selectedMenuIds as $mid) {
                        $mr = \app\models\MenuRoles::findOne(['role_id' => $roleId, 'menu_id' => $mid]);
                        if (!$mr) {
                            $mr = new \app\models\MenuRoles();
                            $mr->role_id = $roleId;
                            $mr->menu_id = $mid;
                            $mr->save(false);
                        }
                        // อัปเดต map menu_id -> menuroles_id ใช้ต่อในข้อ 2
                        $menuCheckedById[$mid] = (string)$mr->_id;
                    }
                }

                // ---------- 2) SYNC menu_permissoin (เฉพาะ fields: menuroles_id, name, abbr) ----------
                $actions = ['create', 'update', 'delete', 'view'];

                foreach ($menuMap as $base => $info) {
                    $mid = $info['id'];

                    // ถ้าเมนูนี้ไม่ได้ถูกเลือก -> ลูกถูกลบทิ้งแล้ว ข้าม
                    if (!isset($menuCheckedById[$mid])) continue;

                    $menurolesId = $menuCheckedById[$mid];

                    // abbr ที่ติ๊กสำหรับเมนูนี้ (เช่น org-master-create, org-master-update, ...)
                    $abbrWanted = [];
                    foreach ($actions as $a) {
                        $abbr = "{$base}-{$a}";
                        if (in_array($abbr, $postedPerms, true)) $abbrWanted[] = $abbr;
                    }

                    // ไม่ติ๊ก action ใดๆ -> ลบลูกทั้งหมดของเมนูนี้ แล้วข้าม
                    if (empty($abbrWanted)) {
                        \app\models\MenuPermissoin::deleteAll(['menuroles_id' => $menurolesId]);
                        continue;
                    }

                    // ตรวจสิทธิ์จริงจากคอลเลกชัน Permissoin ของเมนูนี้ (กันกรณีมีการโพสต์ abbr แปลกๆ)
                    $permDocs = \app\models\Permissoin::find()
                        ->where(['menu_id' => $mid, 'abbr' => ['$in' => $abbrWanted]])
                        ->all();

                    $keepByAbbr = []; // abbr => name
                    foreach ($permDocs as $pd) {
                        $keepByAbbr[(string)$pd->abbr] = (string)$pd->name;
                    }
                    $keepAbbrs = array_keys($keepByAbbr);

                    if (empty($keepAbbrs)) {
                        // ไม่มีสิทธิ์ที่ตรงจริงเลย -> ลบลูกให้หมด
                        \app\models\MenuPermissoin::deleteAll(['menuroles_id' => $menurolesId]);
                        continue;
                    } else {
                        // ลบอันที่ไม่ได้อยู่ในชุดที่จะคงไว้
                        \app\models\MenuPermissoin::deleteAll([
                            'menuroles_id' => $menurolesId,
                            'abbr'         => ['$nin' => $keepAbbrs],
                        ]);
                    }

                    // upsert menuroles_id, name, abbr
                    foreach ($keepByAbbr as $abbr => $name) {
                        $mp = \app\models\MenuPermissoin::findOne([
                            'menuroles_id' => $menurolesId,
                            'abbr'         => $abbr,
                        ]);
                        if (!$mp) {
                            $mp = new \app\models\MenuPermissoin();
                            $mp->menuroles_id = $menurolesId;
                            $mp->abbr         = $abbr;
                        }
                        $mp->name = $name;
                        $mp->save(false);
                    }
                }

                \app\helpers\AlertHelper::alert('success', 'บันทึกสิทธิ์เรียบร้อย');
                return $this->redirect(['index']);
            }

            \app\helpers\AlertHelper::alert('warning', 'ไม่สามารถบันทึกข้อมูลได้');
        }

        return $this->render('update', ['model' => $model]);
    }



    // public function actionUpdate($id)
    // {
    //     $model = $this->findModel($id);
    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         Yii::$app->session->setFlash('success', 'แก้ไขบทบาทสำเร็จ');
    //         return $this->redirect(['view', 'id' => (string)$model->_id]);
    //     }
    //     return $this->render('update', compact('model'));
    // }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'ลบบทบาทแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * หาโมเดลโดยรองรับทั้ง ObjectId 24 ตัว และ _id แบบสตริง
     */
    protected function findModel($id)
    {
        if (($model = Roles::findOne(['_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ไม่พบบทบาทที่ต้องการ');
    }
}
