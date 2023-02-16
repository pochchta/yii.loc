<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class UserController extends Controller
{
    public function actionGetToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return (Yii::$app->user->identity->getAuthKey());
    }
}