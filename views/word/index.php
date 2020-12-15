<?php

use app\models\Word;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $arrSecondCategory array */

$this->title = 'Словарь';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="word-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin([
        'timeout' => 5000,
    ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'value',
            'description:ntext',
//            'created_at:date',
//            'updated_at:date',
            //'created_by',
            //'updated_by',
            //'deleted',
            [
                'format' => 'html',
                'attribute' => 'parent_type',
                'value' => function ($model) {
//                    return "<span title='" . Word::LABELS_TYPE[$model->parent_type] . "'>$model->parent_type</span>";
                    return Word::LABELS_TYPE[$model->parent_type];
                },
                'filter' => Html::activeDropDownList($searchModel, 'parent_type', [Word::ALL => 'все'] + Word::LABELS_TYPE)
            ],
            [
                'format' => 'html',
                'label' => 'Раздел',
                'value' => function ($model) {
                    if ($model->parent->parent_id != 0) {
                        return $model->parent->parent->name;
                    }
                    return $model->parent->name;
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'firstCategory',
                        [Word::ALL => 'все'] + Word::getAllNames(Word::CATEGORY_OF_ALL, 0)
                    )
            ],
            [
                'format' => 'html',
                'label' => 'Категория',
                'value' => function ($model) {
                    if ($model->parent->parent_id != 0) {
                        return $model->parent->name;
                    }
                    return NULL;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'secondCategory',
                    [Word::ALL => 'все'] + $arrSecondCategory
                )
            ],

            [
                'format' => 'raw',
                'filter' => Html::a(
                    '<span class="glyphicon glyphicon-remove a-action"></span>',
                    ['index'],
                    ['title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    if ($model->deleted == Word::NOT_DELETED) {
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
    <?php Pjax::end() ?>

</div>
