<?php

namespace app\modules\api\controllers;

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
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-children'],
                'lastModified' => function ($action, $params) {
                    return 0;
//                    $q = new \yii\db\Query();
//                    return $q->from('word')->max('updated_at');
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheControlTime'],
//                'sessionCacheLimiter' => 'public'
            ],
        ];
    }

    public function actionGetChildren()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return (WordSearch::findNamesByParentId(Yii::$app->request->queryParams));
    }
}