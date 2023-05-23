<?php

namespace app\modules\api\controllers;

use app\models\Word;
use app\modules\api\models\WordSearch;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class WordController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-names'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfData'],
            ],
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

    public function actionGetNames()
    {
        $params = Yii::$app->request->queryParams;
        $wordSearch = new WordSearch();
        $wordSearch->load($params);
        return $wordSearch->findNames();
    }

    public function actionGetVersion()
    {
        return Word::find()->max('updated_at');
    }
}