<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Device */

$this->title = 'Изменение записи: ' . $model->wordName->name;
$this->params['breadcrumbs'][] = ['label' => 'Приборы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wordName->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="device-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model'
    )); ?>

</div>
