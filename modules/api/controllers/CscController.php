<?php

namespace app\modules\api\controllers;

use app\widgets\csc\Model;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class CscController extends Controller
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
        $params = Yii::$app->request->post();
        if ($params['widget_name'] === 'CatalogTabsSort') {
            if(! Yii::$app->user->can('ChangingCatalogTabsSort')) {
                throw new HttpException(403);
            }
        } elseif ($params['widget_name'] === 'GridColumnSort') {
            if(! Yii::$app->user->can('ChangingGridColumnSort')) {
                throw new HttpException(403);
            }
        } else {
            throw new HttpException(403);
        }
        $model = Model::findOne([
            'role' => $params['role'],
            'name' => $params['name'],
            'widget_name' => $params['widget_name'],
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
            'name' => $params['name'],
            'widget_name' => $params['widget_name'],
        ]);
        if ($model) {
            return $model->col;
        }
        return json_encode([]);
    }

}
