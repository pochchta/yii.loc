<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Word */

$this->title = 'Создание новой записи';
$this->params['breadcrumbs'][] = ['label' => 'Словарь', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="word-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model', 'arrSecondCategory', 'arrThirdCategory'
    )) ?>

</div>
