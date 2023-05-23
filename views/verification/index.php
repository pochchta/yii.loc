<?php

use app\assets\GridAsset;
use app\models\CatalogTabs;
use app\models\Status;
use app\models\Verification;
use app\widgets\csc\CatalogTabsSort;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Verification */
/* @var $modelDevice app\models\Device */
/* @var $params array */
/* @var $menu CatalogTabs */


$this->title = 'Поверки';
$this->params['breadcrumbs'][] = $this->title;

GridAsset::register($this);

$catalogTabsSort = new CatalogTabsSort($menu, [
    'required' => Yii::$app->user->can('ChangingCatalogTabsSort') ? ['Настройки'] : [],
    'token' => Yii::$app->user->identity->getAuthKey(),
    'write_url' => '/api/csc/write-column',
    'read_url' => '/api/csc/read-column',
    'class' => '\app\models\verification',
    'role' => Yii::$app->user->identity->getProfileView(),
]);
?>
<div class="verification-index" id="page-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list'], $params), [
            'class' => 'btn btn-warning',
            'data' => ['pjax' => 0]
        ]) ?>
    </p>

    <?= $this->render('/catalog-tabs/grid', compact(
        'catalogTabsSort'
    )); ?>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>

    <?php if ($modelDevice != NULL): ?>
        <p><?=
            'Записи относятся только к прибору: '
            . Html::a(
                $modelDevice->wordName->name . ', №' . $modelDevice->number . ($modelDevice->deleted_id == Status::DELETED ? ' (удален)' : ''),
                ['device/view', 'id' => $modelDevice->id],
                ['data' => ['pjax' => 0]]
            )
            ?></p>
    <?php endif ?>

    <?= GridView::widget([
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
            ],
            [
                'attribute' => 'device_id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(
                        $model->device_id,
                        ['device/view', 'id' => $model->device_id],
                        ['title' => $model->device->wordName->name . ', № ' . $model->device->number . ($model->device->deleted_id == Status::DELETED ? ' (удален)' : '')]
                    );
                },
                'label' => 'ID приб.',
            ],
            [
                'attribute' => 'device.name_id',
                'value' => function ($model) {
                    return $model->device->wordName->name;
                },
            ],
            [
                'attribute' => 'device.number',
                'label' => '№ приб.'
            ],
            [
                'attribute' => 'device.department_id',
                'value' => function ($model) {
                    return $model->device->wordDepartment->name;
                },
            ],
            [
                'attribute' => 'type_id',
                'value' => function ($model) {
                    return $model->vtype->name;
                },
            ],
            [
                'attribute' => 'last_date',
                'format' => 'date',
            ],
            [
                'attribute' => 'next_date',
                'format' => 'date',
            ],
            'period',
            [
                'attribute' => 'status_id',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status_id == Verification::STATUS_ON) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Последняя поверка"></span>';
                    } else {
                        return '';
                    }
                },
                'label' => 'Посл.'
            ],
            [
                'attribute' => 'deleted_id',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted_id == Status::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
            ],

            [
                'format' => 'raw',
                'filter' => Html::a(
                    '<span class="glyphicon glyphicon-remove a-action"></span>',
                    ['index'],
                    ['title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    if ($model->deleted_id == Status::NOT_DELETED) {
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
        'options' => [
            'data' => [
                'paramsByDefault' => Json::encode($searchModel->getDefaultValidators())
            ]
        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>