<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Word */

$this->title = 'Update Word: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Words', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="word-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model','arrFirstCategory', 'arrSecondCategory'
    )) ?>

</div>
