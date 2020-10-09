<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Scale */

$this->title = 'Обновление записи: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Шкалы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="scale-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>