<?php

use app\models\Word;
use app\models\Status;
use app\models\WordSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Словарь';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="word-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>

    <?= GridView::widget([
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => $attribute = 'name',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->name, ['view', 'id' => $model->id]);
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + WordSearch::getAutoCompleteOptions($attribute))
            ],
            [
                'attribute' => $attribute = 'value',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + WordSearch::getAutoCompleteOptions($attribute))
            ],
//            'description:ntext',
            [
                'attribute' => 'parent_id',
                'format' => 'html',
                'value' => function ($model) {
                    $parent = Word::getParentByLevel($model, 0);
                    return $parent->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    $attribute = 'category1',
                    [Status::ALL => 'все'] + array_combine(array_keys(Word::FIELD_WORD), Word::LABEL_FIELD_WORD)
                )
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
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + WordSearch::getAutoCompleteOptions($attribute))
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
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + WordSearch::getAutoCompleteOptions($attribute))
            ],
/*            [
                'attribute' => $attribute = 'category4',
                'format' => 'html',
                'value' => function ($model) {
                    if ($parent = Word::getParentByLevel($model, 3)) {
                        return Html::a($parent->name, ['view', 'id' => $parent->id]);
                    }
                    return NULL;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + WordSearch::getAutoCompleteOptions($attribute))
            ],*/
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
