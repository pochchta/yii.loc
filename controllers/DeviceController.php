<?php

namespace app\controllers;

use app\models\Word;
use app\models\Status;
use Yii;
use app\models\Device;
use app\models\DeviceSearch;
use yii\filters\AccessControl;
use yii\validators\StringValidator;
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
                        'actions' => ['create', 'update', 'ajax-one', 'delete'],
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

    /**
     * @param Device $model
     * @param $view
     * @return string|\yii\web\Response
     */
    public function saveModel($model, $view)
    {
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

    public function actionAjaxOne()
    {
        $data = [];
        $validator = new StringValidator();
        $validator->min = 1;
        $validator->max = Yii::$app->params['maxLenGetParam'];
        $term = Yii::$app->request->get('term');
        $parentName = Yii::$app->request->get('parent');
        if ($validator->validate($term, $error) && $validator->validate($parentName, $error)) {
            $depth = 1;
            if ($parentName == 'department') {
                $depth = 2;
            } elseif (isset(Word::FIELD_WORD[ucfirst($parentName)])) {
                $depth = 3;
            }
            list('condition' => $condition, 'bind' => $bind) =
                Word::getCondition($parentName, $depth, true);
            if (isset($condition)){
                $data = Word::find()
                    ->select(['name as value'])
                    ->where(['like', 'name', $term])
                    ->andOnCondition($condition, $bind)
                    ->orderBy('name')
                    ->limit(Yii::$app->params['maxLinesAutoComplete'])->asArray()->all();
            }
        }
        echo json_encode($data);
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
            $model->name = $model->wordName->name;
            $model->type = $model->wordType->name;
            $model->department = $model->wordDepartment->name;
            $model->position = $model->wordPosition->name;
            $model->scale = $model->wordScale->name;
            $model->accuracy = $model->wordAccuracy->name;
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
