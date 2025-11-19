<?php
namespace app\controllers;

use app\models\ProJect;
use app\models\ProJectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ProJectController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel  = new ProJectSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    public function actionView($_id)
    {
        return $this->render('view', ['model' => $this->findModel($_id)]);
    }

    public function actionCreate()
    {
        $model = new ProJect();
        if ($this->request->isPost &&
            $model->load($this->request->post()) &&
            $model->save()) {
            return $this->redirect(['view', '_id' => (string)$model->_id]);
        }
        
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($_id)
    {
        $model = $this->findModel($_id);
        if ($this->request->isPost &&
            $model->load($this->request->post()) &&
            $model->save()) {
            return $this->redirect(['view', '_id' => (string)$model->_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($_id)
    {
        $this->findModel($_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($_id)
    {
        // ถ้าใช้ MongoDB: แปลง string เป็น ObjectId ก่อนค้น
        if (is_string($_id) && strlen($_id) === 24) {
            $_id = new \MongoDB\BSON\ObjectId($_id);
        }
        if (($model = ProJect::findOne(['_id' => $_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
