<?php

namespace app\modules\api\controllers;

use app\models\Word;
use app\models\WordSearch;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\Response;

class WordController extends Controller
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => ['write'],
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-children'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheControlTime'],
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

    public function actionGetChildren()
    {
        return WordSearch::findNamesByParentId(Yii::$app->request->queryParams);
    }

    public function actionGetVersion()
    {
        return Word::find()->max('updated_at');
    }
}