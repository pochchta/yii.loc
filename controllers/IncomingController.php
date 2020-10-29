<?php

namespace app\controllers;

use app\models\Device;
use Yii;
use app\models\Incoming;
use app\models\IncomingSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IncomingController implements the CRUD actions for Incoming model.
 */
class IncomingController extends Controller
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
                        'actions' => ['index', 'view', 'print-list'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['ChangingIncoming'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Incoming models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IncomingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $device_id = $searchModel->getAttribute('device_id');
        if ($device_id != Device::ALL) {
            $modelDevice = Device::findOne(['id' => $device_id]);
        }

        return $this->render('index', compact(
            'searchModel','dataProvider', 'modelDevice'
        ));
    }

    /**
     * Displays a single Incoming model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Incoming model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $device_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($device_id)
    {
        $model = new Incoming();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Ошибка (приемка): Не удалось сохранить данные');
            }
        }

        $model->device_id = $device_id;     //  только для отображения

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Incoming model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->device_id = $model->getOldAttributes()['device_id'];    // device_id менять нельзя
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Ошибка (приемка): Не удалось сохранить данные');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Incoming model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->deleted == Incoming::NOT_DELETED ? $model->deleted = Incoming::DELETED :
            $model->deleted = Incoming::NOT_DELETED;
        if ($model->save()) {
            if ($model->deleted == Incoming::NOT_DELETED) {
                Yii::$app->session->setFlash('success', 'Данные восстановлены');
            } else {
                Yii::$app->session->setFlash('success', 'Данные удалены');
            }
        } else {
            throw new NotFoundHttpException('Ошибка (приемка): Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPrintList()
    {
        $this->layout = false;
        $searchModel = new IncomingSearch();
        $searchModel->limit = IncomingSearch::PRINT_LIMIT_RECORDS;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('print-list', compact(
            'searchModel','dataProvider'
        ));
    }

    /**
     * Finds the Incoming model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incoming the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incoming::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
