<?php

namespace app\controllers;

use app\models\Device;
use app\models\Status;
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
        $params = Yii::$app->request->queryParams;
        $searchModel = new IncomingSearch();
        $dataProvider = $searchModel->search($params);

        $device_id = $searchModel->getAttribute('device_id');
        if ($device_id != NULL) {
            $modelDevice = Device::findOne(['id' => $device_id]);
        }

        return $this->render('index', compact(
            'searchModel','dataProvider', 'params', 'modelDevice'
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
     * @return mixed
     * @throws NotFoundHttpException отсутствует
     */
    public function actionCreate()
    {
        return $this->actionUpdate(NULL);
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
        if (isset($id)) {       // update
            $model = $this->findModel($id);
            $view = 'update';
        } else {                // create
            $model = new Incoming();
            $model->device_id = Yii::$app->request->get('device_id');             // только для отображения
            $view = 'create';
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена');
                return $this->redirect(['index', 'device_id' => $model->device_id]);
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        return $this->render($view, [
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

        $model->deleted == Status::NOT_DELETED ? $model->deleted = Status::DELETED :
            $model->deleted = Status::NOT_DELETED;
        $textMessage = $model->deleted == Status::NOT_DELETED ? 'восстановлена' : 'удалена';
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', "Запись $textMessage");
        } else {
            $errors = $model->getFirstErrors();
            Yii::$app->session->setFlash('error', "Запись не была $textMessage (" . array_pop($errors) . ')');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPrintList()
    {
        $this->layout = false;

        $params = Yii::$app->request->queryParams;
        $searchModel = new IncomingSearch();
        $searchModel->limit = Yii::$app->params['maxLinesPrint'];
        $dataProvider = $searchModel->search($params);

        return $this->render('print-list', compact(
            'dataProvider', 'params'
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
