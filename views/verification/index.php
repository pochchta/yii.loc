<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Verifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Verification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'device_id',
            'name',
            'type',
            'description',
            //'verif_date',
            //'verif_period',
            //'created_at',
            //'updated_at',
            //'creator',
            //'updater',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
