<?php

namespace app\controllers;

use app\models\Device;
use DateTime;
use Yii;
use app\models\Verification;
use yii\data\ActiveDataProvider;
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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
        $dataProvider = new ActiveDataProvider([
            'query' => Verification::find(),
        ]);

        return $this->render('index', [
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
        $model->device_id = $device_id;
        $model->last_date = (new DateTime())->getTimestamp();
        $model->period = '1';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Device::findOne($model->device_id)->updateDate() == false) {
                throw new NotFoundHttpException('Device: Не удалось обновить даты');
            }
            return $this->redirect(['/device/view', 'id' => $model->device_id]);
        }

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Device::findOne($model->device_id)->updateDate() == false) {
                throw new NotFoundHttpException('Device: Не удалось обновить даты');
            }
            return $this->redirect(['/device/view', 'id' => $model->device_id]);
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
        $deleteId = $model->device_id;
        $model->delete();
        if (Device::findOne($deleteId)->updateDate() == false) {
            throw new NotFoundHttpException('Device: Не удалось обновить даты');
        }

        return $this->redirect(['/device/view', 'id' => $deleteId]);
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

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
