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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name_id',
                'value' => function ($model) {
                    return $model->wordName->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'name', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'type_id',
                'value' => function ($model) {
                    return $model->wordType->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'type', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'department_id',
                'value' => function ($model) {
                    return $model->wordDepartment->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'department', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'position_id',
                'value' => function ($model) {
                    return $model->wordPosition->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'position', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'scale_id',
                'value' => function ($model) {
                    return $model->wordScale->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'scale', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'accuracy_id',
                'value' => function ($model) {
                    return $model->wordAccuracy->name;
                },
                'filter' => Html::activeInput('text', $searchModel,'accuracy', ['class' => 'form-control']),
            ],
            'number',
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
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-eye-open a-action"></span>',
                            ['view', 'id' => $model->id],
                            ['title' => 'Просмотр', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-log-in a-action"></span>',
                            ['incoming/create', 'device_id' => $model->id],
                            ['title' => 'Новая приемка', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-scale a-action"></span>',
                            ['verification/create', 'device_id' => $model->id],
                            ['title' => 'Новая поверка', 'data' => ['pjax' => 0]]
                        );
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>