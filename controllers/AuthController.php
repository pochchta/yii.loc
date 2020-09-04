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
        $modelChildItem = new AuthItemChild();
        if ($modelChildItem->load(Yii::$app->request->post()) && $modelChildItem->save()) {
            return $this->refresh();
        }

        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => AuthItemChild::find()->where(['parent' => $model->name])->with('itemChild'),
        ]);

        return $this->render('view', compact(
            'model', 'modelChildItem', 'dataProvider'
        ));
    }

    public function actionCreate()
    {
        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = $model->createItem();
            } catch (\Exception $e) {
                $success = false;
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            if ($success) {
                return $this->redirect(['view', 'id' => $model->name]);
            }
        }

        return $this->render('create', compact(
            'model'
        ));
    }

    public function actionUpdate($id)
    {
        $model = new AuthItem();
        $model->findItem($id);

        if ($model->load(Yii::$app->request->post())) {
            try {
                $success = $model->updateItem($id);
            } catch (\Exception $e) {
                $success = false;
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            if ($success) {
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
        $model = new AuthItem();
        $model->deleteItem($id);

        return $this->redirect(['index']);
    }

    /**
     * Deletes link between child and parent
    */

    public function actionDeleteChild($parent, $child) {
        $role = Yii::$app->authManager->getRole($parent);
        $permit = Yii::$app->authManager->getPermission($child);
        if ($role == NULL || $permit == NULL) throw new NotFoundHttpException('Не найдены элементы');
        if (Yii::$app->authManager->removeChild($role, $permit)) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
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

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
