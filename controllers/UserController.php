<?php

namespace app\controllers;

use app\models\AuthAssignment;
use app\models\AuthItem;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $modelAssign = new AuthAssignment();                        // модель связи юзер-роль для добавления ролей
        if ($modelAssign->load(Yii::$app->request->post()) && $modelAssign->save()) {
            return $this->refresh();
        }

        $model = $this->findModel($id);                             // модель юзер для вывода его данных

        $dataProvider = new ActiveDataProvider([                    // вывод ролей для выбранного юзера
            'query' => AuthAssignment::find()->where(['user_id' => $model->id])->joinWith(['item']),
        ]);

        $allRoles = AuthItem::find()->select(['name', 'type'])->asArray()->all();     // array of string all roles
        foreach($allRoles as $key => $item) {
            $type = $item['type'] == AuthItem::$ROLE ? 'Роль' : 'Разрешение';
            $allRoles[$type][$item['name']] = $item['name'];
            unset($allRoles[$key]);
        }

        $allRolesByUser = Yii::$app->authManager->getChildRoles('admin');
        $allPermsByUser = Yii::$app->authManager->getPermissionsByRole('admin');

        return $this->render('view', compact(
            'model', 'dataProvider', 'modelAssign', 'allRoles', 'allRolesByUser', 'allPermsByUser'
        ));
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
