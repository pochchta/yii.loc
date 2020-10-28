<?php

use app\models\Device;
use app\models\Verification;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Device */
/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Приборы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

if ($model->deleted == Device::NOT_DELETED) {
    $deleteMessage = 'Вы уверены, что хотите удалить этот элемент?';
    $deleteTitle = 'Удалить';
    $deleteText = '';
} else {
    $deleteMessage = 'Вы уверены, что хотите восстановить этот элемент';
    $deleteTitle = 'Восстановить';
    $deleteText = ' (удален)';

}
?>
<div class="device-view">

    <h1><?= Html::encode($this->title) . $deleteText?></h1>

    <p>
        <?= Html::a('Печать', ['print', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Новая приемка', ['incoming/create', 'device_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Приемки', ['incoming/index', 'device_id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Новая поверка', ['verification/create', 'device_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Поверки', ['verification/index', 'device_id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a($deleteTitle, ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => $deleteMessage,
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'name',
            'number',
            'type',
            'description:ntext',
            [
                'value' => $model->activeVerification->last_date,
                'label' => 'Дата поверки',
                'format' => 'date',
            ],
            [
                'value' => $model->activeVerification->next_date,
                'label' => 'Дата cледующей поверки',
                'format' => 'date',
            ],
            [
                'value' => $model->activeVerification->period,
                'label' => 'Межповерочный период',
            ],
            [
                'attribute' => 'id_department',
                'value' => $model->department->name
            ],
            [
                'attribute' => 'id_scale',
                'value' => $model->scale->value
            ],
            'accuracy',
            'position',
            'created_at:date',
            'updated_at:date',
            [
                'attribute' => 'created_by',
                'value' => $model->creator->username,
            ],
            [
                'attribute' => 'updated_by',
                'value' => $model->updater->username,
            ],
        ],
    ]) ?>

    <h3>Список поверок:</h3>

    <p>
        <?= Html::a(
            'Создать новую запись',
            ['/verification/create', 'device_id' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
    //            'device_id',
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
                    ['view', 'id' => $model->id],
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
