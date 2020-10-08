<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шкалы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scale-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'value',
            'description:ntext',
            'deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
