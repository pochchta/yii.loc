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
            if (empty($arrSecondCategory) == false) {
                $arrSecondCategory = ['0' => 'нет'] + $arrSecondCategory;
            }
            if ($arrSecondCategory[$params['secondCategory']] === NULL) {
                $params['secondCategory'] = CategoryWord::ALL;
            }
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', compact(
            'searchModel','dataProvider','arrSecondCategory'
        ));
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
     */
    public function saveModel($model, $view)
    {
        $parent = $model->parent;

        if ($model->parent_id <= 0) {
            $model->firstCategory = $model->parent_id;
            $model->secondCategory = 0;
        } else {
            $model->firstCategory = $parent->parent_id;
            $model->secondCategory = $model->parent_id;
        }

        if ($model->load($arrayPost = Yii::$app->request->post())) {
            if ($model->secondCategory > 0) {
                $model->parent_id = $model->secondCategory;
            } else {
                $model->parent_id = $model->firstCategory;
            }

            if (isset($arrayPost['saveButton'])) {                     // сохранение
                $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

                $saveResult = false;
                if ($fileMutex->acquire('category_word', Yii::$app->params['mutexTimeout'])) {
                    $saveResult = $model->save();
                } else {
                    $model->addError('name', 'Категории словаря редактируются, попробуйте еще раз');
                }

                $fileMutex->release('category_word');

                if ($saveResult) {
                    Yii::$app->session->setFlash('success', 'Запись сохранена');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $errors = $model->getFirstErrors();
                    Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
                }
            }
        }

        $arrSecondCategory = [];
        if ($model->firstCategory != 0) {
            $arrSecondCategory = CategoryWord::getAllNames($model->firstCategory, $model->id);
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
        $model = $this->findModel($id);

        $model->deleted == CategoryWord::NOT_DELETED ? $model->deleted = CategoryWord::DELETED :
            $model->deleted = CategoryWord::NOT_DELETED;

        $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

        $saveResult = false;
        if ($fileMutex->acquire('category_word', Yii::$app->params['mutexTimeout'])) {
            $saveResult = $model->save();
        } else {
            $model->addError('name', 'Категории словаря редактируются, попробуйте еще раз');
        }

        $fileMutex->release('category_word');

        if ($saveResult) {
            if ($model->deleted == CategoryWord::NOT_DELETED) {
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
