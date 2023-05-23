<?php

namespace app\controllers;

use app\models\CatalogTabs;
use app\models\Device;
use app\models\Status;
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
                        'actions' => ['create', 'update', 'list-auto-complete', 'delete'],
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
            $searchModel->status_id = Status::ALL;     // в searchModel по умолчанию status_id = STATUS_ON; здесь перезаписываем ALL;
            if ($params['status_id'] == '') {                // если status_id не пустой, то он попадет в модель дальше: $searchModel->search($params);
                $params['status_id'] = Status::ALL;    // <- это нужно ТОЛЬКО для создания ссылки фильтра для печати
            }
            $modelDevice = Device::findOne(['id' => $device_id]);
        }

        $dataProvider = $searchModel->search($params);

        $headerMenu = [
            'device_name',
            'device_department',
            'device_number',
            'name',
            'status_id',
            'created_at',
            'updated_at',
            'deleted',
        ];
        $menu = (new CatalogTabs($headerMenu))
            ->setSource(['device_number' => 'text', 'name' => 'text', 'status' => 'vStatus', 'created_at' => 'date', 'updated_at' => 'date', 'deleted' => 'deleted'])
            ->setLabel(['device_number' => 'Номер приб.', 'name' => 'Название', 'status' => 'Статус', 'created_at' => 'Дата создания', 'updated_at' => 'Дата изменения', 'deleted' => 'Удален'])
            ->setAutoComplete(['device' => ['device_name', 'device_department', 'device_number'], 'verification' => ['name']]);

        return $this->render('index', compact(
            'dataProvider', 'searchModel', 'params', 'modelDevice', 'menu'
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
     * @return mixed
     * @throws NotFoundHttpException отсутствует
     */
    public function actionCreate()
    {
        return $this->actionUpdate(NULL);
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
        if (isset($id)) {       // update
            $model = $this->findModel($id);
            $model->type = $model->vtype->name;
            $view = 'update';
        } else {                // create
            $model = new Verification();
            $model->device_id = Yii::$app->request->get('device_id');             // только для отображения
            $model->last_date = (new DateTime())->getTimestamp();
            $model->period = Verification::PERIOD_BY_DEFAULT;
            $view = 'create';
        }

        if ($model->load(Yii::$app->request->post())) {
            $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

            $saveResult = false;
            if ($fileMutex->acquire('verification', Yii::$app->params['mutexTimeout'])) {
                if ($model->save()) {
                    if ($model->checkLastVerification()) {
                        $saveResult = true;
                    } else {
                        $model->addError('name', 'Произошла ошибка при вычислении последней поверки');
                    }
                }
                $fileMutex->release('verification');
            } else {
                $model->addError('name', 'Поверки редактируются, попробуйте еще раз');
            }

            if ($saveResult) {
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
     * Deletes an existing Verification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->deleted_id == Status::NOT_DELETED ? $model->deleted_id = Status::DELETED :
            $model->deleted_id = Status::NOT_DELETED;
        $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

        $saveResult = false;
        if ($fileMutex->acquire('verification', Yii::$app->params['mutexTimeout'])) {
            if ($model->save(false)) {      // validation == false, т.к. в валидаторе преобразуется дата из php:Y-m-d в TimeStamp и при удалении нечего валидировать
                if ($model->checkLastVerification()) {
                    $saveResult = true;
                } else {
                    $model->addError('name', 'Произошла ошибка при вычислении последней поверки');
                }
            }
            $fileMutex->release('verification');
        } else {
            $model->addError('name', 'Поверки редактируются, попробуйте еще раз');
        }

        $textMessage = $model->deleted_id == Status::NOT_DELETED ? 'восстановлена' : 'удалена';
        if ($saveResult) {
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
        $searchModel = new VerificationSearch();
        $searchModel->limit = Yii::$app->params['maxLinesPrint'];
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