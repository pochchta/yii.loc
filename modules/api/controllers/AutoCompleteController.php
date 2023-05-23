<?php

namespace app\modules\api\controllers;

use app\modules\api\models\AutoCompleteSearch;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class AutoCompleteController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-auto-complete'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfData'],
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['get-rules'],
                'lastModified' => function () {
                    return 0;
                },
                'cacheControlHeader' => 'public, max-age=' . Yii::$app->params['cacheTimeOfParams'],
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

    public function actionGetAutoComplete()
    {
        $params = Yii::$app->request->queryParams;
        $search = new AutoCompleteSearch();
        $search->load($params);
        return $search->findAutoComplete();
    }

    public function actionGetRules()
    {
        return AutoCompleteSearch::RULES_AUTO_COMPLETE;
    }
}