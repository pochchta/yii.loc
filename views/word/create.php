<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Word */

$this->title = 'Create Word';
$this->params['breadcrumbs'][] = ['label' => 'Words', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="word-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model','arrFirstCategory', 'arrSecondCategory'
    )) ?>

</div>
