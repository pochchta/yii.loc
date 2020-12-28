<?php

use app\models\CategoryWord;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategoryWordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $arrSecondCategory array */

$this->title = 'Категории словаря';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-word-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>
<!--    --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>
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
                'attribute' => 'firstCategory',
                'format' => 'html',
                'value' => function ($model) {
                    return CategoryWord::getParentName($model);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'firstCategory',
                    [CategoryWord::ALL => 'все', '0' => 'нет'] + CategoryWord::LABEL_FIELD_WORD
                )
            ],
            [
                'attribute' => 'secondCategory',
                'format' => 'html',
                'value' => function ($model) {
                    return CategoryWord::getParentName($model, 1);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'secondCategory',
                    [CategoryWord::ALL => 'все'] + $arrSecondCategory
                )
            ],
            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == CategoryWord::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    CategoryWord::NOT_DELETED => 'нет',
                    CategoryWord::DELETED => 'да',
                    CategoryWord::ALL => 'все'
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
                    if ($model->deleted == $model::NOT_DELETED) {
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
