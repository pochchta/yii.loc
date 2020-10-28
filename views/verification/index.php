<?php

use app\models\Device;
use app\models\Verification;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поверки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'device_id',
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
            'name',
            'type',
            //            'description',
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
            'period',
            //            'created_at:date',
            //            'updated_at:date',
            /*            [
                            'attribute' => 'created_by',
                            'value' => function ($model) {
                                return $model->creator->username;
                            }
                        ],*/
            /*            [
                            'attribute' => 'updated_by',
                            'value' => function ($model) {
                                return $model->updater->username;
                            }
                        ],*/
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status == Verification::STATUS_ON) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Действующая поверка"></span>';
                    } else {
                        return '';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'status', [
                    Verification::STATUS_OFF => 'off',
                    Verification::STATUS_ON => 'on',
                    Verification::ALL => 'все'
                ]),
            ],
            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == Verification::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Verification::NOT_DELETED => 'нет',
                    Verification::DELETED => 'да',
                    Verification::ALL => 'все'
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
                    if ($model->deleted == Verification::NOT_DELETED) {
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
