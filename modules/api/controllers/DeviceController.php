<?php

namespace app\modules\api\controllers;

use app\models\Device;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class DeviceController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-version'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfVersion'],
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

    public function actionGetVersion()
    {
        return Device::find()->max('updated_at');
    }
}