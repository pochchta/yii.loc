<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;

$this->title = 'Регистрация завершена успешно';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-sign-up">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Вы зарегистрированы под следующим именем:</p>
    <h3><?= Html::encode($model->username) ?></h3>

</div>
