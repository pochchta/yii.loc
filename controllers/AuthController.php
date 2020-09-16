<?php

namespace app\controllers;

use app\models\AuthItemChild;
use Yii;
use app\models\AuthItem;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthController implements the CRUD actions for AuthItem model.
 */
class AuthController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-child' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['ChangingAuthItem'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AuthItem::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $modelChildItem = new AuthItemChild();
        if ($modelChildItem->load(Yii::$app->request->post()) && $modelChildItem->save()) {
            try {
                $model->touch('updated_at');        // method touch throws exception
            } catch (\Exception $e) {
                // дата не проставлена
            }
            return $this->refresh();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => AuthItemChild::find()->where(['parent' => $model->name])->with('itemChild'),
        ]);

        return $this->render('view', compact(
            'model', 'modelChildItem', 'dataProvider'
        ));
    }

    /**
     * Creating AuthItem model.
     * If creating is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->name]);
            }
        }

        return $this->render('create', compact(
            'model'
        ));
    }

    /**
     * Updating AuthItem model.
     * If updating is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $validator = new \yii\validators\CompareValidator();
        $validator->compareValue = $model->type;        // '==' by default

        if ($model->load(Yii::$app->request->post())) {
            $isValidData = $model->validate();
            $isValidType = $validator->validate($model->type);        // отдельная валидация на изменение типа
            if ($isValidType == false) {
                $model->addError('type', 'Тип менять нельзя');
            }
            if ($isValidData && $isValidType) {
                $manager = Yii::$app->authManager;
                if ($model->oldAttributes['type'] == AuthItem::$ROLE) {
                    $item = $manager->getRole($model->oldAttributes['name']);
                } else {
                    $item = $manager->getPermission($model->oldAttributes['name']);
                }
                if ($item) {
                    $item->name = $model->name;
                    $item->description = $model->description;
                    try {
                        $manager->update($model->oldAttributes['name'], $item);
                    } catch (\Exception $e) {
                        throw new NotFoundHttpException('Не удалось обновить');
                    }
                } else {
                    throw new NotFoundHttpException('Не найден элемент для обновления');
                }

                return $this->redirect(['view', 'id' => $model->name]);
            }
        }

        return $this->render('update', compact(
            'model'
        ));
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $manager = Yii::$app->authManager;
        if ($model->type == AuthItem::$ROLE) {
            $item = $manager->getRole($model->name);
        } else {
            $item = $manager->getPermission($model->name);
        }
        if ($item) {
            $manager->remove($item);
        } else {
            throw new NotFoundHttpException('Не найден элемент для удаления');
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes link between child and parent
     * @param string $parent
     * @param string $child
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDeleteChild($parent, $child) {
        $modelChild = AuthItemChild::findOne(['parent' => $parent, 'child' => $child]);
        try {
            if ($modelChild->delete()) {     // not false AND not 0
                AuthItem::findOne($parent)->touch('updated_at');    // method touch throws exception
            }
            return $this->redirect(Yii::$app->request->referrer);
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('Операция не выполнена');
        }
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
