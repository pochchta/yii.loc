<?php

use app\models\CategoryWord;
use app\models\Word;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $arrSecondCategory array */
/* @var $arrThirdCategory array */

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
                    return CategoryWord::getParentN($model);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'firstCategory',
                    [CategoryWord::ALL => 'все', '0' => 'нет'] + CategoryWord::getAllNames(0)
                )
            ],
            [
                'attribute' => 'secondCategory',
                'value' => function ($model) {
                    return CategoryWord::getParentN($model, 1);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'secondCategory',
                    [CategoryWord::ALL => 'все'] + $arrSecondCategory
                )
            ],
            [
                'attribute' => 'thirdCategory',
                'value' => function ($model) {
                    return CategoryWord::getParentN($model, 2);
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'thirdCategory',
                    [CategoryWord::ALL => 'все'] + $arrThirdCategory
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
