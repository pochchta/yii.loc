<?php

use app\assets\GridAsset;
use app\models\Status;
use app\widgets\sort\GridColumnSort;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Device */
/* @var $params array */
/* @var $menu app\models\CatalogTabs */

$this->title = 'Приборы';
$this->params['breadcrumbs'][] = $this->title;

GridAsset::register($this);
?>

<div class="device-index page-index" id="page-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Печать списка', ['print-list'], [
            'class' => 'btn btn-warning print_button',
            'data' => ['pjax' => 0, 'url' => '/device/print-list']
        ]) ?>
        <?= Html::a('Создать новую запись', ['create'], [
            'class' => 'btn btn-success',
            'data' => ['pjax' => 0]
        ]) ?>
    </p>

    <?= $this->render('/catalog-tabs/grid', compact(
        'menu'
    )); ?>


    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout'],
    ]);

    $gridViewData = [
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            '№' => ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => ($attribute = 'kind') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                }
            ],
            [
                'attribute' => ($attribute = 'group'),
                'value' => function ($model) use ($attribute) {
                    return $model->wordName->parent->parent->name;
                }
            ],
            [
                'attribute' => ($attribute = 'type'),
                'value' => function ($model) use ($attribute) {
                    return $model->wordName->parent->name;
                }
            ],
            [
                'attribute' => ($attribute = 'name') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                }
            ],
            [
                'attribute' => ($attribute = 'state') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                }
            ],
            [
                'attribute' => ($attribute = 'department') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                }
            ],
            [
                'attribute' => $attribute = 'position',
                'value' => function ($model) {
                    return $model->position;
                }
            ],
            [
                'attribute' => ($attribute = 'crew') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                }
            ],
            [
                'attribute' => $attribute = 'number',
                'value' => function ($model) {
                    return $model->number;
                }
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
                }
            ],
            'Кнопки' => [
                'header' =>
                    Html::a(
                        '<span class="glyphicon glyphicon-remove reset_sort a-action"></span>',
                        null,
                        ['title' => 'Сбросить сортировку']
                    )
                    . Html::a(
                        '<span class="glyphicon glyphicon-cog show_grid_column_sort a-action" data-toggle-id="grid_column_sort"></span>',
                        null,
                        ['title' => 'Настроить столбцы']
                    ),
                'contentOptions' => ['class' => 'nowrap'],
                'format' => 'raw',
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
    ];

    $gridColumnSort = new GridColumnSort($gridViewData, [
        'required' => ['Кнопки'],
        'token' => Yii::$app->user->identity->getAuthKey(),
        'writeUrl' => '/api/gcs/write-column',
        'readUrl' => '/api/gcs/read-column',
        'class' => '\app\models\device',
        'role' => Yii::$app->user->identity->getProfileView(),
    ]);
    ?>

    <?= $gridColumnSort->runWidget() ?>

    <?= GridView::widget($gridColumnSort->getGridViewData()); ?>

    <?php Pjax::end() ?>
</div>