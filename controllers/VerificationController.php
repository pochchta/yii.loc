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
                        'actions' => ['index', 'view', 'print-list'],
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
        $params = Yii::$app->request->queryParams;
        $searchModel = new VerificationSearch();

        $device_id = $params['device_id'];
        if ($device_id != NULL) {
            $searchModel->status = Verification::ALL;
            $modelDevice = Device::findOne(['id' => $device_id]);
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', compact(
            'searchModel','dataProvider', 'params', 'modelDevice'
        ));
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
                    throw new NotFoundHttpException('Ошибка (поверка): активная поверка не определена');
                }
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Ошибка (поверка): Не удалось сохранить данные');
            }
        }

        $model->device_id = $device_id;     // только для отображения
        $model->last_date = (new DateTime())->getTimestamp();
        $model->period = Verification::PERIOD_BY_DEFAULT;
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
            $model->device_id = $model->getOldAttributes()['device_id'];    // device_id менять нельзя
            if ($model->save()) {
                if ($model->checkLastVerification() == false) {
                    throw new NotFoundHttpException('Ошибка (поверка): активная поверка не определена');
                }
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['device/view', 'id' => $model->device_id]);
            } else {
                throw new NotFoundHttpException('Ошибка (поверка): Не удалось сохранить данные');
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
                throw new NotFoundHttpException('Ошибка (поверка): активная поверка не определена');
            }
            if ($model->deleted == Verification::NOT_DELETED) {
                Yii::$app->session->setFlash('success', 'Данные восстановлены');
            } else {
                Yii::$app->session->setFlash('success', 'Данные удалены');
            }
        } else {
            throw new NotFoundHttpException('Ошибка (поверка): Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPrintList()
    {
        $this->layout = false;

        $params = Yii::$app->request->queryParams;
        $searchModel = new VerificationSearch();
        $searchModel->limit = VerificationSearch::PRINT_LIMIT_RECORDS;
        $dataProvider = $searchModel->search($params);

        return $this->render('print-list', compact(
            'dataProvider', 'params'
        ));
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
}
