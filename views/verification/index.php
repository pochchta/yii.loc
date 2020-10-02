<?php

use app\models\Verification;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поверки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'device_id',
            [
                'attribute' => 'device_id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(
                        $model->device->number,
                        ['device/view', 'id' => $model->device_id],
                        ['title' => $model->device->name]
                    );
                },
            ],
            'name',
            'type',
            //            'description',
            'last_date:date',
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
                'attribute' => 'deleted',
                'value' => function ($model) {
                    return ($model->deleted == Verification::NOT_DELETED) ? 'нет' : 'да';
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
                    ['index'],
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
