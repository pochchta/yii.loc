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
            $searchModel->status = Verification::ALL;     // в searchModel по умолчанию status = STATUS_ON; здесь перезаписываем ALL;
            if ($params['status'] == '') {                // если status не пустой, то он попадет в модель дальше: $searchModel->search($params);
                $params['status'] = Verification::ALL;    // <- это нужно ТОЛЬКО для создания ссылки фильтра для печати
            }
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
     */
    public function actionCreate($device_id)
    {
        $model = new Verification();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if ($model->checkLastVerification() == false) {
                    Yii::$app->session->setFlash('error', 'Запись не была сохранена (последняя поверка не определена)');
                } else {
                    Yii::$app->session->setFlash('success', 'Запись сохранена');
                    return $this->redirect(['device/view', 'id' => $model->device_id]);
                }
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
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
            if ($model->save()) {
                if ($model->checkLastVerification() == false) {
                    Yii::$app->session->setFlash('error', 'Запись не была сохранена (последняя поверка не определена)');
                } else {
                    Yii::$app->session->setFlash('success', 'Запись сохранена');
                    return $this->redirect(['device/view', 'id' => $model->device_id]);
                }
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
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
                Yii::$app->session->setFlash('error', 'Запись не была удалена (последняя поверка не определена)');
            } else {
                if ($model->deleted == Verification::NOT_DELETED) {
                    Yii::$app->session->setFlash('success', 'Запись восстановлена');
                } else {
                    Yii::$app->session->setFlash('success', 'Запись удалена');
                }
            }
        } else {
            $errors = $model->getFirstErrors();
            Yii::$app->session->setFlash('error', 'Запись не была удалена (' . array_pop($errors) . ')');
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
            'dataProvider', 'searchModel', 'params'
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
