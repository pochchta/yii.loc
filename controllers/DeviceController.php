<?php

namespace app\controllers;

use app\models\Word;
use app\models\Status;
use app\models\WordSearch;
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
                        'actions' => ['create', 'update', 'list-auto-complete', 'delete'],
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
     * @throws NotFoundHttpException отсутствует
     */
    public function actionCreate()
    {
        return $this->actionUpdate(NULL);
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
        if (isset($id)) {       // update
            $model = $this->findModel($id);
            $model->name = $model->wordName->name;
            $model->type = $model->wordType->name;
            $model->department = $model->wordDepartment->name;
            $model->crew = $model->wordCrew->name;
            $view = 'update';
        } else {                // create
            $model = new Device();
            $view = 'create';
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        return $this->render($view, compact(
            'model'
        ));
    }

    public function actionListAutoComplete()
    {
        $deviceSearch = new DeviceSearch();
        $deviceSearch->load(Yii::$app->request->queryParams);
        if (in_array($deviceSearch->term_name, DeviceSearch::COLUMN_SEARCH)) {
            if ($deviceSearch->validate()) {
                echo $deviceSearch->findNames();
            }
        } else {
            $wordSearch = new WordSearch();
            $wordSearch->load(Yii::$app->request->queryParams);
            if ($wordSearch->validate()) {
                $depth = 1;
                if (isset(Word::FIELD_WORD[ucfirst($wordSearch->term_name)])) {
                    $depth = Word::MAX_NUMBER_PARENTS;
                }
                $arrayCondition[] = [
                    'parents' => [$wordSearch->term_name],
                    'depth' => $depth,
                    'withParent' => true
                ];
                echo $wordSearch->findNamesByParents($arrayCondition);
            }
        }
        die();
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
        $textMessage = $model->deleted == Status::NOT_DELETED ? 'восстановлена' : 'удалена';
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', "Запись $textMessage");
        } else {
            $errors = $model->getFirstErrors();
            Yii::$app->session->setFlash('error', "Запись не была $textMessage (" . array_pop($errors) . ')');
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
        $searchModel->limit = Yii::$app->params['maxLinesPrint'];
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
