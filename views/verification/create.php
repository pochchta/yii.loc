<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Verification */

$this->title = 'Создать новую запись';
$this->params['breadcrumbs'][] = ['label' => $model->device->wordName->name, 'url' => ['device/view', 'id' => $model->device_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
