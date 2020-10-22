<?php

namespace app\controllers;

use app\models\Device;
use app\models\VerificationSearch;
use DateTime;
use Yii;
use app\models\Verification;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VerificationController implements the CRUD actions for Verification model.
 */
class VerificationController extends Controller
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
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['ChangingVerification'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Verification models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VerificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Verification model.
     * @param string $id
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
     * Creates a new Verification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $device_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($device_id)
    {
        $model = new Verification();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->checkLastVerification() == false) {
                    throw new NotFoundHttpException('Verification: Не удалось обновить статусы');
                }
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Verification: Не удалось сохранить данные');
            }
        }

        $model->device_id = $device_id;
        $model->last_date = (new DateTime())->getTimestamp();
        $model->period = '1';
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Verification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->device_id = $model->getOldAttributes()['device_id'];
            if ($model->save()) {
                if ($model->checkLastVerification() == false) {
                    throw new NotFoundHttpException('Verification: Не удалось обновить статусы');
                }
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Verification: Не удалось сохранить данные');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Verification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
        public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted == Verification::NOT_DELETED ? $model->deleted = Verification::DELETED :
            $model->deleted = Verification::NOT_DELETED;
        if ($model->save()) {
            if ($model->checkLastVerification() == false) {
                throw new NotFoundHttpException('Verification: Не удалось обновить статусы');
            }
            if ($model->deleted == Verification::NOT_DELETED) {
                Yii::$app->session->setFlash('success', 'Данные восстановлены');
            } else {
                Yii::$app->session->setFlash('success', 'Данные удалены');
            }
        } else {
            throw new NotFoundHttpException('Verification: Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Verification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Verification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Verification::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }

    /**
     * Finds the Device model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Device the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelDevice($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
