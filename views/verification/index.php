<?php

use app\models\Status;
use app\models\Verification;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Verification */
/* @var $modelDevice app\models\Device */
/* @var $params array */

$this->title = 'Поверки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-index">

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
                $modelDevice->name . ', №' . $modelDevice->number . ($modelDevice->deleted == Status::DELETED ? ' (удален)' : ''),
                ['device/view', 'id' => $modelDevice->id]
            )
        ?></p>
    <?php endif ?>

    <?= GridView::widget([
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
                        ['title' => $model->device->name . ', № ' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : '')]
                    );
                },
                'label' => 'ID приб.'
            ],
            [
                'attribute' => 'device.name',
                'filter' => Html::activeInput(
                    'text',
                    $searchModel,
                    'deviceName',
                    ['class' => 'form-control']
                ),
                'label' => 'Имя приб.'
            ],
            [
                'attribute' => 'device.number',
                'filter' => Html::activeInput(
                    'text',
                    $searchModel,
                    'deviceNumber',
                    ['class' => 'form-control']
                ),
                'label' => '№ приб.'
            ],
            [
                'attribute' => 'device.department.name',
                'value' => function ($model) {
                    return $model->device->department->name;
                },
/*                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'deviceIdDepartment',
                    [Status::ALL => 'все'] + Department::getAllNames()
                )*/
            ],
            'type',
            [
                'attribute' => 'last_date',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'last_date_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'last_date_end'),
                'filterOptions' => ['class' => 'filter-date']
            ],
            [
                'attribute' => 'next_date',
                'format' => 'date',
                'filter' => Html::activeInput('date', $searchModel, 'next_date_start')
                    . Yii::$app->formatter->asNtext("\n")
                    . Html::activeInput('date', $searchModel, 'next_date_end'),
                'filterOptions' => ['class' => 'filter-date']
            ],
            'period',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status == Verification::STATUS_ON) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Последняя поверка"></span>';
                    } else {
                        return '';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'status', [
                    Verification::STATUS_OFF => 'off',
                    Verification::STATUS_ON => 'on',
                    Status::ALL => 'все'
                ]),
                'label' => 'Посл.'
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
                            ['verification/view', 'id' => $model->id],
                            ['title' => 'Просмотр']
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-pencil a-action"></span>',
                            ['verification/update', 'id' => $model->id],
                            ['title' => 'Редактировать']
                        )
                        . Html::a(
                            '<span class="' . $deleteCssClass . '"></span>',
                            ['verification/delete', 'id' => $model->id],
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
