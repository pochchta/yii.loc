<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\AssignmentSearch */

$this->title = 'Назначения ролей';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-assignment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'item_name',
                'label' => 'Роль'
            ],
            'user_id',
            [
                'attribute' => 'username',
                'value' => function ($model) {
                    return $model->user->username;
                },
                'label' => 'Имя пользователя',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'created_at_start').
                    Yii::$app->formatter->asNtext(",\n").
                    Html::activeInput('date', $searchModel, 'created_at_end')
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
        ],
    ]); ?>

</div>
