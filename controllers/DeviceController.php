<?php

namespace app\controllers;

use app\models\Word;
use app\models\Status;
use Yii;
use app\models\Device;
use app\models\DeviceSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeviceController implements the CRUD actions for Device model.
 */
class DeviceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'print-list', 'view', 'print'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['ChangingDevice'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Device models.
     * @return mixed
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->queryParams;

        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', compact(
            'searchModel', 'dataProvider', 'params'
        ));
    }

    /**
     * Displays a single Device model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', compact(
           'model'
        ));
    }

    /**
     * Creates a new Device model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->saveModel(new Device(), 'create');
    }

    /**
     * Updates an existing Device model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        return $this->saveModel($this->findModel($id), 'update');
    }

    public function saveModel($model, $view)
    {
        $params = Yii::$app->request->post();
        $searchModel = new DeviceSearch();

        $searchModel->id = $model->id;          // для формирования url для pjax
        if (empty($params)) {
            Word::setParams($params, $model->department, Word::FIELD_WORD['Department']);
            Word::setParams($params, $model->scale, Word::FIELD_WORD['Scale']);
        }
        $searchModel->getArrFilters($params);
        $searchModel->load($params);

        if ($model->load($params) && Yii::$app->request->isPjax == false) {
            $model->department_id = $searchModel->thirdDepartment;
            $model->scale_id = $searchModel->thirdScale;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                if ($errorDepartment = $model->getFirstError('department_id')) {
                    $searchModel->addError('thirdDepartment', $errorDepartment);
                }
                if ($errorScale = $model->getFirstError('scale_id')) {
                    $searchModel->addError('thirdScale', $errorScale);
                }
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        return $this->render($view, compact(
            'model', 'searchModel'
        ));
    }

    /**
     * Deletes an existing Device model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->deleted == Status::NOT_DELETED ? $model->deleted = Status::DELETED :
            $model->deleted = Status::NOT_DELETED;
        if ($model->save()) {
            if ($model->deleted == Status::NOT_DELETED) {
                Yii::$app->session->setFlash('success', 'Данные восстановлены');
            } else {
                Yii::$app->session->setFlash('success', 'Данные удалены');
            }
        } else {
            $errors = $model->getFirstErrors();
            Yii::$app->session->setFlash('error', 'Запись не была удалена (' . array_pop($errors) . ')');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPrint($id)
    {
        $this->layout = false;
        $model = $this->findModel($id);

        return $this->render('print', [
            'model' => $model
        ]);
    }

    public function actionPrintList()
    {
        $this->layout = false;

        $params = Yii::$app->request->queryParams;
        $searchModel = new DeviceSearch();
        $searchModel->limit = DeviceSearch::PRINT_LIMIT_RECORDS;
        $dataProvider = $searchModel->search($params);

        return $this->render('print-list', compact(
            'dataProvider', 'params'
        ));
    }

    /**
     * Finds the Device model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Device the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
