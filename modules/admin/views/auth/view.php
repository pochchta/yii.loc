<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\AuthItem */
/* @var $modelChildItem app\modules\admin\models\AuthItemChild */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Роли и разрешения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить элемент?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'type',
                'value' => $model->type == $model::$ROLE ? 'Роль' : 'Разрешение'
            ],
            'description:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'date'
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date'
            ],
//            'rule_name',
//            'data',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>
    <?php if ($model->type === $model::$ROLE): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'child',
                    'value' => function ($model) {
                        return Html::a($model->child, ['view', 'id' => $model->child]);
                    },
                    'format' => 'html'
                ],
                'itemChild.description',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' =>
                        [
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                   'delete-child',
                                    'parent' => $model->parent,
                                    'child' => $model->child,
                                ], [
                                    'title' => Yii::t('yii', 'Удалить'),
                                    'data' => [
                                        'confirm' => 'Вы действительно хотите удалить элемент?',
                                        'method' => 'post'
                                    ]
                                ]);
                            },
                        ]
                    ],
            ],
        ]); ?>

        <div class="one-button-form">
            <?php $form = ActiveForm::begin();
            $button = Html::submitButton('Добавить', ['class' => 'btn btn-success']);
            $span = "<span class='input-group-addon' id='basic-addon'>{$button}</span>";
            $formGroup = "<div class='input-group'>{input}{$span}</div>";
            ?>

            <?= $form->field($modelChildItem, 'parent', [
                "template" => "{input}"
            ])->hiddenInput(['value' => $model->name])->label() ?>

            <?= $form->field($modelChildItem, 'child', [
                "template" => "{label}\n{$formGroup}\n{error}"
            ])->dropDownList($model::getNamesAllPermits(), ['class' => 'form-control', 'aria-describedby' => 'basic-addon']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endif ?>

</div>