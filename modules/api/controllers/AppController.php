<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class AppController extends Controller
{
    public function actionGetParams()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return (Yii::$app->params);
    }
}