<?php

use app\models\Status;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Device */
/* @var $params array */
/* @var $arrDepartments array */
/* @var $arrScales array */

$this->title = 'Приборы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list'], $params), [
            'class' => 'btn btn-warning',
        ]) ?>
        <?= Html::a('Создать новую запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>

    <?php  echo $this->render('_search', [
        'model' => $searchModel
    ]); ?>

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
                'attribute' => 'department_id',
                'value' => function ($model) {
                    return $model->department->name;
                },
                'filter' => ''
/*                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'department_id',
                    [Word::ALL => 'все'] + CategoryWord::getAllNames()
                )*/
            ],
            [
                'attribute' => 'scale_id',
                'value' => function ($model) {
                    return $model->scale->name;
                },
                'filter' => ''
/*                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'scale_id',
                    [Word::ALL => 'все'] + CategoryWord::getAllNames()
                )*/
            ],
            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == Status::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Status::NOT_DELETED => 'нет',
                    Status::DELETED => 'да',
                    Status::ALL => 'все'
                ])
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
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-eye-open a-action"></span>',
                            ['view', 'id' => $model->id],
                            ['title' => 'Просмотр']
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-log-in a-action"></span>',
                            ['incoming/create', 'device_id' => $model->id],
                            ['title' => 'Новая приемка']
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-scale a-action"></span>',
                            ['verification/create', 'device_id' => $model->id],
                            ['title' => 'Новая поверка']
                        );
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>