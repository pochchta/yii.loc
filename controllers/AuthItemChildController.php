<?php

namespace app\controllers;

use app\models\AuthItemChild;
use Yii;
use app\models\AuthItem;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthController implements the CRUD actions for AuthItem model.
 */
class AuthItemChildController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionDelete($parent, $child) {
        $role = Yii::$app->authManager->getRole($parent);
        $permit = Yii::$app->authManager->getPermission($child);
        if ($role == NULL || $permit == NULL) throw new NotFoundHttpException('Не найдены элементы');
        if (Yii::$app->authManager->removeChild($role, $permit)) {
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            throw new NotFoundHttpException('Операция не выполнена');
        }
    }

}
