<?php

use app\models\Device;
use app\models\Incoming;
use yii\helpers\Html;
use yii\grid\GridView;

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

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list'], $params), [
            'class' => 'btn btn-warning',
        ]) ?>
    </p>

    <?php if ($modelDevice != NULL): ?>
    <p><?=
        'Записи относятся только к прибору: '
        . Html::a(
            $modelDevice->name . ', №' . $modelDevice->number . ($modelDevice->deleted == Device::DELETED ? ' (удален)' : ''),
            ['device/view', 'id' => $modelDevice->id]
        )
    ?></p>
    <?php endif ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'device_id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(
                        $model->device_id,
                        ['device/view', 'id' => $model->device_id],
                        ['title' => $model->device->name . ', № ' . $model->device->number . ($model->device->deleted == Device::DELETED ? ' (удален)' : '')]
                    );
                },
            ],
            [
                'format' => 'html',
                'label' => 'Прибор',
                'value' => function ($model) {
                    return
                        '<div class="device-full-name">'
                        . $model->device->name . ', № ' . $model->device->number
                        . '</div>';
                },
            ],
            'description:ntext',
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
                    Incoming::ALL => 'все',
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
                    Incoming::ALL => 'все',
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
                    if ($model->deleted == Incoming::DELETED) {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                    return '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Incoming::NOT_DELETED => 'нет',
                    Incoming::DELETED => 'да',
                    Incoming::ALL => 'все'
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
                    if ($model->deleted == Incoming::NOT_DELETED) {
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