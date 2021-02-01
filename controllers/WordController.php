<?php

namespace app\controllers;

use app\models\Status;
use Yii;
use app\models\Word;
use app\models\WordSearch;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'list-auto-complete', 'delete'],
                        'roles' => ['ChangingWord'],
                    ],
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
        $categoryId = 0;
        if ($model->parent_id > 0) {
            $parent = $model->parent;
            $model->parentName = $parent->name;
            if ($parent->parent_id < 0) {
                $categoryId = $parent->parent_id;
            } elseif ($parent->parent->parent_id < 0) {
                $categoryId = $parent->parent->parent_id;
            }
        } else {
            $categoryId = (int)($model->parent_id);     // в новой модели parent_id = NULL
        }
        if ($key = array_search($categoryId, Word::FIELD_WORD)) {       // получение значения для select
            $model->categoryName = $key;
        } else {
            $model->categoryName = Status::NOT_CATEGORY;
        }

        if ($model->load($arrayPost = Yii::$app->request->post())) {
            if (isset($arrayPost['saveButton'])) {                     // сохранение
                $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

                $saveResult = false;
                if ($fileMutex->acquire($mutexName = 'word', Yii::$app->params['mutexTimeout'])) {
                    $saveResult = $model->save();
                    $fileMutex->release($mutexName);
                } else {    // добавляется ошибка к модели, после этого нельзя валидировать, т.к. ошибка затрется
                    $model->addError('name', 'Словарь редактируется, попробуйте еще раз');
                }

                if ($saveResult) {
                    Yii::$app->session->setFlash('success', 'Запись сохранена');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $errors = $model->getFirstErrors();
                    Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
                }
            }
        }

        return $this->render($view, compact(
            'model'
        ));
    }

    public function actionListAutoComplete()
    {
        $search = new WordSearch();
        $search->load(Yii::$app->request->queryParams);
        if ($search->validate() && isset(Word::FIELD_WORD[$search->parent])) {
            echo $search->findNames(2, true);
        }
        die();
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

        $fileMutex = Yii::$app->mutex;              /* @var $fileMutex yii\mutex\FileMutex */

        $saveResult = false;
        if ($fileMutex->acquire($mutexName = 'word', Yii::$app->params['mutexTimeout'])) {
            $saveResult = $model->save(false);
            $fileMutex->release($mutexName);
        } else {    // добавляется ошибка к модели, после этого нельзя валидировать, т.к. ошибка затрется
            $model->addError('name', 'Словарь редактируется, попробуйте еще раз');
        }

        if ($saveResult) {
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
