<?php

namespace app\controllers;

use Yii;
use app\models\CategoryWord;
use app\models\CategoryWordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CategoryWordController extends Controller
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
     * Lists all CategoryWord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategoryWordSearch();
        $params = Yii::$app->request->queryParams;
        $arrSecondCategory = [];

        if ($params['firstCategory'] == CategoryWord::ALL || $params['firstCategory'] == 0) {
            $params['secondCategory'] = CategoryWord::ALL;
        } else {
            $arrSecondCategory = CategoryWord::getAllNames($params['firstCategory']);
            if ($arrSecondCategory[$params['secondCategory']] === NULL) {
                $params['secondCategory'] = CategoryWord::ALL;
            }
            if (empty($arrSecondCategory) == false) {
                $arrSecondCategory = [$params['firstCategory'] => 'нет'] + $arrSecondCategory;
            }
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'arrSecondCategory' => $arrSecondCategory
        ]);
    }

    /**
     * Displays a single CategoryWord model.
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
     * Creates a new CategoryWord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $model = new CategoryWord();

        return $this->saveModel($model, 'create');
    }

    /**
     * Updates an existing CategoryWord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        return $this->saveModel($model, 'update');
    }

    /**
     * Сохранение и загрузка массива категорий
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $model CategoryWord
     * @param $view
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function saveModel($model, $view)
    {
        $parent = NULL;
        if ($model->parent_id != 0) {
            $parent = $this->findModel($model->parent_id);
        }

        $arrSecondCategory = [];
        if ($model->parent_id != 0) {
            if ($parent->parent_id == 0) {
                $model->firstCategory = $model->parent_id;
                $model->secondCategory = 0;
            } else {
                $model->firstCategory = $parent->parent_id;
                $model->secondCategory = $model->parent_id;
            }
            if ($model->firstCategory != 0) {
                $arrSecondCategory = CategoryWord::getAllNames($model->firstCategory, $model->id);
            }
        }

        if ($model->load($arrayPost = Yii::$app->request->post())) {
            $arrSecondCategory = [];
            if ($model->firstCategory != 0) {   // TODO дублирующийся запрос
                $arrSecondCategory = CategoryWord::getAllNames($model->firstCategory, $model->id);
            }
            if ($model->secondCategory != 0) {
                $model->parent_id = $model->secondCategory;
            } else {
                $model->parent_id = $model->firstCategory;
            }

            if (isset($arrayPost['saveButton'])) {                     // сохранение
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Запись сохранена');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $errors = $model->getFirstErrors();
                    Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
                }
            }
        }

        return $this->render($view, compact(
            'model', 'arrSecondCategory'
        ));
    }

    /**
     * Deletes an existing CategoryWord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CategoryWord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CategoryWord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CategoryWord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
