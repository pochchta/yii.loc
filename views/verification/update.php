<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Verification */

$this->title = 'Изменение записи: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->device->name, 'url' => ['device/view', 'id' => $model->device_id]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="verification-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
