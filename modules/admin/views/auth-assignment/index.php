<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\modules\admin\models\AssignmentSearch */

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
                'value' => function ($model) {
                    return Html::a(
                        $model->item_name,
                        ['auth/view', 'id' => $model->item_name],
                        ['title' => $model->item->description]
                    );
                },
                'format' => 'html',
                'label' => 'Роль'
            ],
            [
                'attribute' => 'user_id',
                'label' => 'ID Пользователя'
            ],
            [
                'attribute' => 'username',
                'value' => function ($model) {
                    return Html::a($model->user->username, ['user/view', 'id' => $model->user_id]);
                },
                'format' => 'html',
                'label' => 'Имя пользователя',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'created_at_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'created_at_end')
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}', 'header' => Html::a(
                '',
                ['index'],
                ['class' => 'glyphicon glyphicon-remove', 'title' => 'Очистить все фильтры']
            )],
        ],
    ]); ?>

</div>
