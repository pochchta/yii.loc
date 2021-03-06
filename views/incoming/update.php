<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Incoming */

$this->title = 'Изменение записи: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Приемки', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="incoming-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
