<?php

namespace app\controllers;

use app\models\CategoryWord;
use Yii;
use app\models\Word;
use app\models\WordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WordController implements the CRUD actions for Word model.
 */
class WordController extends Controller
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
     * Lists all Word models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WordSearch();
        $params = Yii::$app->request->queryParams;
        $arrSecondCategory = [];
        $arrThirdCategory = [];

        if ($params['firstCategory'] == CategoryWord::ALL || $params['firstCategory'] == 0) {
            $params['secondCategory'] = CategoryWord::ALL;
            $params['thirdCategory'] = CategoryWord::ALL;
        } else {
            $arrSecondCategory = CategoryWord::getAllNames($params['firstCategory']);
            if (empty($arrSecondCategory) == false) {
                $arrSecondCategory = [$params['firstCategory'] => 'нет'] + $arrSecondCategory;
            }
            if ($params['secondCategory'] == CategoryWord::ALL || $params['secondCategory'] == 0) {
                $params['thirdCategory'] = CategoryWord::ALL;
            } else {
                $arrThirdCategory = CategoryWord::getAllNames($params['secondCategory']);
                if (empty($arrThirdCategory) == false) {
                    $arrThirdCategory = [$params['firstCategory'] => 'нет'] + $arrThirdCategory;
                }
            }
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', compact(
            'searchModel', 'dataProvider', 'arrSecondCategory', 'arrThirdCategory'
        ));
    }

    /**
     * Displays a single Word model.
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
     * Creates a new Word model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $model = new Word();

        return $this->saveModel($model, 'create');
    }

    /**
     * Updates an existing Word model.
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
     * @param $model Word
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
     * Deletes an existing Word model.
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
     * Finds the Word model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Word the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Word::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
