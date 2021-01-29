<?php

use app\models\Word;
use app\models\Status;
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

            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->name, ['view', 'id' => $model->id]);
                },
            ],
            'value',
            'description:ntext',
            [
                'attribute' => 'firstCategory',
                'format' => 'html',
                'value' => function ($model) {
                    $arr = Word::getParentName($model);
                    return $arr['id'] > 0 ? Html::a($arr['name'], ['view', 'id' => $arr['id']]) : $arr['name'];
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'firstCategory',
                    [Status::ALL => 'все', '0' => 'нет'] + Word::LABEL_FIELD_WORD
                )
            ],
            [
                'attribute' => 'secondCategory',
                'format' => 'html',
                'value' => function ($model) {
                    $arr = Word::getParentName($model, 1);
                    return $arr['id'] > 0 ? Html::a($arr['name'], ['view', 'id' => $arr['id']]) : $arr['name'];
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'secondCategory',
                    [Status::ALL => 'все'] + $arrSecondCategory
                )
            ],
            [
                'attribute' => 'thirdCategory',
                'format' => 'html',
                'value' => function ($model) {
                    $arr = Word::getParentName($model, 2);
                    return $arr['id'] > 0 ? Html::a($arr['name'], ['view', 'id' => $arr['id']]) : $arr['name'];
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'thirdCategory',
                    [Status::ALL => 'все'] + $arrThirdCategory
                )
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
                    if ($model->deleted == Status::NOT_DELETED) {
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
