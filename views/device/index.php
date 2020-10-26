<?php

use app\models\Department;
use app\models\Device;
use app\models\Scale;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $params array */
$this->title = 'Приборы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list-device'], $params), [
            'class' => 'btn btn-warning',
        ]) ?>
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
                'attribute' => 'id_department',
                'value' => function ($model) {
                    return $model->department->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'id_department',
                    [Department::ALL => 'все'] + Department::getAllNames())
            ],
            [
                'attribute' => 'id_scale',
                'value' => function ($model) {
                    return $model->scale->value;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'id_scale',
                    [Scale::ALL => 'все'] + Scale::getAllValues())
            ],
            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == Device::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Device::NOT_DELETED => 'нет',
                    Device::DELETED => 'да',
                    Device::ALL => 'все'
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
                    if ($model->deleted == Device::NOT_DELETED) {
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