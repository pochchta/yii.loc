<?php

namespace app\controllers;

use app\models\Status;
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
                'class' => VerbFilter::class,
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

        $firstCategory = & $params['firstCategory'];
        $secondCategory = & $params['secondCategory'];
        $thirdCategory = & $params['thirdCategory'];

        $firstCategory = $firstCategory ?? Status::ALL;
        $secondCategory = $secondCategory ?? Status::ALL;
        $thirdCategory = $thirdCategory ?? Status::ALL;

        $firstCategory = (int) $firstCategory;
        $secondCategory = (int) $secondCategory;
        $thirdCategory = (int) $thirdCategory;

        if ($firstCategory == Status::NOT_CATEGORY) {
            $secondCategory = Status::ALL;
            $thirdCategory = Status::ALL;
        } else {
            $arrSecondCategory = Word::getAllNames($firstCategory);
            if ($firstCategory != Status::ALL && empty($arrSecondCategory) == false) {
                $arrSecondCategory = [Status::NOT_CATEGORY => 'нет'] + $arrSecondCategory;
            }
            if (isset($arrSecondCategory[$secondCategory]) == false) {
                $secondCategory = Status::ALL;
            }
            if ($secondCategory == Status::NOT_CATEGORY) {
                $thirdCategory = Status::ALL;
            } else {
                if ($secondCategory == Status::ALL) {
                    $arrThirdCategory = Word::getAllNames($firstCategory, 2);
                } else {
                    $arrThirdCategory = Word::getAllNames($secondCategory);
                    if (empty($arrThirdCategory) == false) {
                        $arrThirdCategory = [Status::NOT_CATEGORY => 'нет'] + $arrThirdCategory;
                    }
                }
                if (isset($arrThirdCategory[$thirdCategory]) == false) {
                    $thirdCategory = Status::ALL;
                }
            }
        }
        $arrSecondCategory = [Status::ALL => 'все'] + $arrSecondCategory;
        $arrThirdCategory = [Status::ALL => 'все'] + $arrThirdCategory;

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
     */
    public function saveModel($model, $view)
    {
        $parent = $model->parent;
        $parentOfParent = $parent->parent;

        if ($model->parent_id <= 0) {
            $model->firstCategory = $model->parent_id;
            $model->secondCategory = 0;
            $model->thirdCategory = 0;
        } elseif ($parent->parent_id <= 0) {
            $model->firstCategory = $parent->parent_id;
            $model->secondCategory = $model->parent_id;
            $model->thirdCategory = 0;
        } else {
            $model->firstCategory = $parentOfParent->parent_id;
            $model->secondCategory = $parent->parent_id;
            $model->thirdCategory = $model->parent_id;
        }

        if ($model->load($arrayPost = Yii::$app->request->post())) {
            if ($model->thirdCategory > 0) {
                $model->parent_id = $model->thirdCategory;
            } elseif ($model->secondCategory > 0) {
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

        $arrSecondCategory = [];
        $arrThirdCategory = [];
        if ($model->firstCategory != Status::NOT_CATEGORY) {
            $arrSecondCategory = Word::getAllNames($model->firstCategory, 1, false, $model->id);
            if ($model->secondCategory != Status::NOT_CATEGORY && in_array($model->secondCategory, array_keys($arrSecondCategory))) {
                $arrThirdCategory = Word::getAllNames($model->secondCategory, 1, false, $model->id);
            }
        }

        return $this->render($view, compact(
            'model', 'arrSecondCategory', 'arrThirdCategory'
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
        $model = $this->findModel($id);

        $model->deleted == Status::NOT_DELETED ? $model->deleted = Status::DELETED :
            $model->deleted = Status::NOT_DELETED;
        if ($model->save(false)) {
            if ($model->deleted == Status::NOT_DELETED) {
                Yii::$app->session->setFlash('success', 'Запись восстановлена');
            } else {
                Yii::$app->session->setFlash('success', 'Запись удалена');
            }
        } else {
            $errors = $model->getFirstErrors();
            Yii::$app->session->setFlash('error', 'Запись не была удалена (' . array_pop($errors) . ')');
        }

        return $this->redirect(Yii::$app->request->referrer);
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
