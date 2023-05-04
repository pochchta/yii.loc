<?php

namespace app\modules\api\controllers;

use app\widgets\gcs\Model;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class GcsController extends Controller
{
    /**
     * {@inheritdoc}
     */

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => ['write-column'],
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
        if(! Yii::$app->user->can('ChangingGridColumnSort')) {
            throw new HttpException(403);
        }
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

    public function actionReadColumn()
    {
        $params = Yii::$app->request->post();
        $model = Model::findOne([
            'role' => $params['role'],
            'name' => $params['name']
        ]);
        if ($model) {
            return json_decode($model->col);
        }
        return [];
    }

}
