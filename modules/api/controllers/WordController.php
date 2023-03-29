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
                'only' => ['get-children', 'get-name'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfWord'],
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-version'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfWordVersion'],
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

    public function actionGetName()
    {
        $params = Yii::$app->request->queryParams;
        $wordSearch = new WordSearch();
        $wordSearch->load($params);
        if ($wordSearch->validate()) {
            $out = Word::find()->select('name')->where(['id' => $wordSearch->id])->asArray()->one();
            if (isset($out)) {
                return $out;
            }
        }
        return ['name' => 'не найдено'];
    }

    public function actionGetVersion()
    {
        return Word::find()->max('updated_at');
    }
}