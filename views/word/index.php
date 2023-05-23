<?php

use app\assets\GridAsset;
use app\models\Word;
use app\models\Status;
use app\widgets\csc\CatalogTabsSort;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Словарь';
$this->params['breadcrumbs'][] = $this->title;

GridAsset::register($this);

$catalogTabsSort = new CatalogTabsSort($menu, [
    'required' => Yii::$app->user->can('ChangingCatalogTabsSort') ? ['Настройки'] : [],
    'token' => Yii::$app->user->identity->getAuthKey(),
    'write_url' => '/api/csc/write-column',
    'read_url' => '/api/csc/read-column',
    'class' => '\app\models\word',
    'role' => Yii::$app->user->identity->getProfileView(),
]);
?>
<div class="word-index page-index" id="page-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('/catalog-tabs/grid', compact(
        'catalogTabsSort'
    )); ?>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>

    <?= GridView::widget([
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => $attribute = 'name',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->name, ['view', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => $attribute = 'value',
            ],
//            'description:ntext',
            [
                'attribute' => 'parent_id',
                'format' => 'html',
                'value' => function ($model) {
                    $parent = Word::getParentByLevel($model, 0);
                    return $parent->name;
                },
            ],
            [
                'attribute' => $attribute = 'category2',
                'format' => 'html',
                'value' => function ($model) {
                    if ($parent = Word::getParentByLevel($model, 1)) {
                        return Html::a($parent->name, ['view', 'id' => $parent->id]);
                    }
                    return NULL;
                },
            ],
            [
                'attribute' => $attribute = 'category3',
                'format' => 'html',
                'value' => function ($model) {
                    if ($parent = Word::getParentByLevel($model, 2)) {
                        return Html::a($parent->name, ['view', 'id' => $parent->id]);
                    }
                    return NULL;
                },
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
                'header' =>
                    Html::a(
                        '<span class="glyphicon glyphicon-remove reset_sort a-action"></span>',
                        null,
                        ['title' => 'Сбросить сортировку']
                    )
/*                    . Html::a(
                        '<span class="glyphicon glyphicon-cog show_grid_column_sort a-action" data-toggle-id="grid_column_sort"></span>',
                        null,
                        ['title' => 'Настроить столбцы']
                    )*/,
                'contentOptions' => ['class' => 'nowrap'],
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
                                'pjax' => 0,
                                'method' => 'post',
                                'confirm' => $deleteMessage
                            ]]
                        );
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>

</div>
