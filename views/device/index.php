<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Devices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Device', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'type',
            'description:ntext',
            [
                'attribute' => 'last_date',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'last_date_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'last_date_end')
            ],
            [
                'attribute' => 'next_date',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'next_date_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'next_date_end')
            ],
//            'period',
//            'created_at:date',
//            'updated_at:date',
/*            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model->creator->username;
                }
            ],*/
/*            [
                'attribute' => 'updated_by',
                'value' => function($model) {
                    return $model->updater->username;
                }
            ],*/

            [
                'format' => 'raw',
                'filter' => Html::a(
                    '',
                    ['index'],
                    ['class' => 'glyphicon glyphicon-remove', 'title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    return
                        Html::a(
                            '',
                            ['view', 'id' => $model->id],
                            ['class' => 'glyphicon glyphicon-eye-open a-action', 'title' => 'Просмотр']
                        )
                        . Html::a(
                            '',
                            ['update', 'id' => $model->id],
                            ['class' => 'glyphicon glyphicon-pencil a-action', 'title' => 'Редактировать']
                        )
                        . Html::a(
                            '',
                            ['delete', 'id' => $model->id],
                            ['class' => 'glyphicon glyphicon-trash a-action', 'title' => 'Удалить', 'data' => [
                                'method' => 'post',
                                'confirm' => 'Вы уверены, что хотите удалить этот элемент?'
                            ]]
                        );
                }
            ],
        ],
    ]); ?>


</div>
