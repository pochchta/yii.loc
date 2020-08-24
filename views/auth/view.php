<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
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

                'child',
                'item.description',
    /*            [
                    'attribute' => 'parent',
                    'value' => function ($data) {
                        return Html::a('удалить', ['unlink', 'parent' => $data->parent, 'child' => $data->child], ['confirm' => 'Вы уверены?']);
                    },
                    'format' => 'html',
                    'header' => 'Действия'
                ],*/
                ['class' => 'yii\grid\ActionColumn', 'controller' => 'auth-item-child', 'template' => '{delete}'],
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
            ])->dropDownList($model->getAllPermits(), ['class' => 'form-control', 'aria-describedby' => 'basic-addon']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endif ?>

</div>
