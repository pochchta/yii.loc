<?php

namespace app\controllers;

use app\widgets\sort\Model;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class DataController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['read-column'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['write-column'],
                        'roles' => ['ChangingGridColumnSort'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }

    public function actionWriteColumn()
    {
        $params = Yii::$app->request->post();
        $model = Model::findOne([
            'role' => $params['role'],
            'name' => $params['name']
        ]);
        if (empty($model)) {
            $model = new Model();
        }

        if ($model->load($params) && $model->save()) {
            return true;
        }
        return false;
    }

}
