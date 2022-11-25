<?php

namespace app\controllers;

use app\models\profile\ChangeNameForm;
use app\models\profile\ChangePassForm;
use app\models\profile\ChangeViewForm;
use app\models\User;
use app\modules\admin\models\AuthAssignment;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignUpForm;

class SiteController extends Controller
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
                    'logout' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignUp()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignUpForm();
        if ($model->load(Yii::$app->request->post()) && $model->signUp()) {
            return $this->render('sign-up-success', [
                'model' => $model
            ]);
        }

        $model->password = '';
        return $this->render('sign-up', [
            'model' => $model
        ]);
    }

    public function actionAssignment()
    {
        $dataProvider = new ActiveDataProvider([                    // вывод ролей для выбранного юзера
            'query' => AuthAssignment::find()
                ->where(['user_id' => Yii::$app->user->identity->id])
                ->with('item', 'permits.itemChild')
        ]);

        return $this->render('profile/assignment', compact(
            'dataProvider'
        ));
    }

    public function actionChangeName()
    {
        $model = new ChangeNameForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->updateName()) {
                // refresh identity
                Yii::$app->user->switchIdentity(User::findOne(Yii::$app->user->identity->id));
                Yii::$app->session->setFlash('success', 'Запись сохранена');
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        $model->clearPassFields();

        return $this->render('profile/change-name', compact(
            'model'
        ));
    }

    public function actionChangePass()
    {
        $model = new ChangePassForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->updatePass()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена');
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        $model->clearPassFields();

        return $this->render('profile/change-pass', compact(
            'model'
        ));
    }

    public function actionChangeView()
    {
        $model = new ChangeViewForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->updateProfileView()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена');
            } else {
                $errors = $model->getFirstErrors();
                Yii::$app->session->setFlash('error', 'Запись не была сохранена (' . array_pop($errors) . ')');
            }
        }

        return $this->render('profile/change-view', compact(
            'model'
        ));
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

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}
