<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class AppController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-params'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfParams'],
            ],
        ];
    }

    public function actionGetParams()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return (Yii::$app->params);
    }
}