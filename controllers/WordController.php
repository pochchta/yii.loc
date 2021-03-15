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

        $dataProvider = $searchModel->search($params);

        return $this->render('index', compact(
            'searchModel', 'dataProvider'
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
    private function saveModel($model, $view)
    {
        $model->parent_name = $model->parent->name;
        $categoryId = Word::getParentByLevel($model, 0)->id;
        if ($key = array_search($categoryId, Word::FIELD_WORD)) {       // получение значения для select
            $model->category_name = $key;
        } else {
            $model->category_name = Status::NOT_CATEGORY;
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
        $wordSearch = new WordSearch();
        $wordSearch->load(Yii::$app->request->queryParams);
        if ($wordSearch->validate()) {
            if (in_array($wordSearch->term_name, WordSearch::COLUMN_SEARCH)) {
                echo $wordSearch->findNamesByFieldName();
            } else {
                if ($wordSearch->term_name == 'category2') {
                    $arrayCondition[] = [
                        'parents' => [$wordSearch->term_p1],
                        'depth' => 1,
                        'withParent' => false
                    ];
                } elseif ($wordSearch->term_name == 'category3') {
                    $arrayCondition[] = [
                        'parents' => [$wordSearch->term_p1, $wordSearch->term_p2],
                        'depth' => 2,
                        'withParent' => false
                    ];
                } elseif ($wordSearch->term_name == 'category4') {
                    $arrayCondition[] = [
                        'parents' => [$wordSearch->term_p1, $wordSearch->term_p2, $wordSearch->term_p3],
                        'depth' => 3,
                        'withParent' => false
                    ];
                } else {                                    // word/_form
                    $arrayCondition[] = [
                        'parents' => [$wordSearch->term_p1],
                        'depth' => Word::MAX_NUMBER_PARENTS - 1,
                        'withParent' => true
                    ];
                }
                echo $wordSearch->findNamesByParents($arrayCondition);
            }
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
