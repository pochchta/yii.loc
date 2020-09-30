<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приборы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать новую запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'number',
            'type',
//            'description:ntext',
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
            [
                'attribute' => 'deleted',
                'value' => function ($model) {
                    return ($model->deleted == 0) ? 'нет' : 'да';
                },
//                'filter' => Html::dropDownList('number', 0, ['0' => 'нет', '1' => 'да', '-1' => 'все'])
                'filter' => Html::activeDropDownList($searchModel, 'deleted', ['0' => 'нет', '1' => 'да', '-1' => 'все'])
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
                    '<span class="glyphicon glyphicon-remove a-action"></span>',
                    ['index'],
                    ['title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    if ($model->deleted == 0) {
                        $deleteMessage = 'Вы уверены, что хотите удалить этот элемент?';
                        $deleteTitle = 'Удалить';
                        $deleteCssClass = 'glyphicon glyphicon-trash a-action';
                    } else {
                        $deleteMessage = 'Вы уверены, что хотите восстановить этот элемент';
                        $deleteTitle = 'Восстановить';
                        $deleteCssClass = 'glyphicon glyphicon-refresh a-action';
                    }
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-eye-open a-action"></span>',
                            ['view', 'id' => $model->id],
                            ['title' => 'Просмотр']
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-pencil a-action"></span>',
                            ['update', 'id' => $model->id],
                            ['title' => 'Редактировать']
                        )
                        . Html::a(
                            '<span class="' . $deleteCssClass . '"></span>',
                            ['delete', 'id' => $model->id],
                            ['title' => $deleteTitle, 'data' => [
                                'method' => 'post',
                                'confirm' => $deleteMessage
                            ]]
                        );
                }
            ],
        ],
    ]); ?>

</div>