<?php

use app\models\DeviceSearch;
use app\models\Incoming;
use app\models\Status;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IncomingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Incoming */
/* @var $modelDevice app\models\Device */
/* @var $params array */

$this->title = 'Приемки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incoming-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list'], $params), [
            'class' => 'btn btn-warning',
            'data' => ['pjax' => 0]
        ]) ?>
    </p>

    <?php if ($modelDevice != NULL): ?>
    <p><?=
        'Записи относятся только к прибору: '
        . Html::a(
            $modelDevice->wordName->name . ', №' . $modelDevice->number . ($modelDevice->deleted == Status::DELETED ? ' (удален)' : ''),
            ['device/view', 'id' => $modelDevice->id],
            ['data' => ['pjax' => 0]]
        )
    ?></p>
    <?php endif ?>

    <?= GridView::widget([
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'device_id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(
                        $model->device_id,
                        ['device/view', 'id' => $model->device_id],
                        ['title' => $model->device->wordName->name . ', № ' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : '')]
                    );
                },
                'label' => 'ID приб.',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'device_id',
                ] + DeviceSearch::getAutoCompleteOptions('id', 'device'))
            ],
            [
                'attribute' => 'device.name_id',
                'value' => function ($model) {
                    return $model->device->wordName->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'device_name',
                ] + DeviceSearch::getAutoCompleteOptions('name', 'device'))
            ],
            [
                'attribute' => 'device.number',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'device_number',
                ] + DeviceSearch::getAutoCompleteOptions('number', 'device')),
                'label' => '№ приб.'
            ],
            [
                'attribute' => 'device.department_id',
                'value' => function ($model) {
                    return $model->device->wordDepartment->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'device_department',
                ] + DeviceSearch::getAutoCompleteOptions('department', 'device')),
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status == Incoming::INCOMING) {
                        return '<span class="glyphicon glyphicon-log-in color-err" title="Принят"></span>';
                    } elseif ($model->status == Incoming::READY) {
                        return '<span class="glyphicon glyphicon-ok-circle color-war" title="Готов"></span>';
                    } elseif ($model->status == Incoming::OUTGOING) {
                        return '<span class="glyphicon glyphicon-log-out color-ok" title="Выдан"></span>';
                    }
                    return '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'status', [
                    Status::ALL => 'все',
                    Incoming::INCOMING => 'принят',
                    Incoming::READY => 'готов',
                    Incoming::OUTGOING => 'выдан',
                ]),
            ],
            [
                'attribute' => 'payment',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->payment == Incoming::PAID) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Оплачен"></span>';
                    }
                    return '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'payment', [
                    Status::ALL => 'все',
                    Incoming::NOT_PAID => 'нет',
                    Incoming::PAID => 'да',

                ]),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'created_at_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'created_at_end'),
                'filterOptions' => ['class' => 'filter-date']
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'updated_at_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'updated_at_end'),
                'filterOptions' => ['class' => 'filter-date']
            ],
            //'created_by',
            //'updated_by',

            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == Status::DELETED) {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                    return '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Status::NOT_DELETED => 'нет',
                    Status::DELETED => 'да',
                    Status::ALL => 'все'
                ])
            ],

            [
                'format' => 'raw',
                'filter' => Html::a(
                    '<span class="glyphicon glyphicon-remove a-action"></span>',
                    ['index'],
                    ['title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    if ($model->deleted == Status::NOT_DELETED) {
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
                            ['title' => 'Просмотр', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-pencil a-action"></span>',
                            ['update', 'id' => $model->id],
                            ['title' => 'Редактировать', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="' . $deleteCssClass . '"></span>',
                            ['delete', 'id' => $model->id],
                            ['title' => $deleteTitle, 'data' => [
                                'method' => 'post',
                                'confirm' => $deleteMessage,
                                'pjax' => 0
                            ]]
                        );
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>