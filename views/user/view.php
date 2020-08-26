<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelAssign app\models\AuthAssignment */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить элемент?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'item_name',
                'label' => 'Роль',
                'value' => function ($data) {
                    return Html::a($data->item_name, ['/auth/view', 'id' => $data->item_name]);
                },
                'format' => 'html'
            ],
            [
                'label' => 'Разрешения',
                'value' => function ($data) {
                    $ret = '';
                    foreach ($data->permits as $item) {
                        $ret .= Html::a($item->child, ['/auth/view', 'id' => $item->child]);
                        $ret .= ', ';
                    }
                    $ret = rtrim($ret, ', ');
                    return $ret;
                },
                'format' => 'html',
            ],
             'item.description',
            [
                'attribute' => 'created_at',
                'format' => 'date'
            ],

            ['class' => 'yii\grid\ActionColumn', 'controller' => 'auth-assignment', 'template' => '{delete}'],
        ],
    ]); ?>

    <div class="one-button-form">
        <?php $form = ActiveForm::begin();
            $button = Html::submitButton('Добавить', ['class' => 'btn btn-success']);
            $span = "<span class='input-group-addon' id='basic-addon'>{$button}</span>";
            $formGroup = "<div class='input-group'>{input}{$span}</div>";

//            $allRoles = Yii::$app->authManager->getRoles();
            $arrayRoles = array();
            foreach ($dataProvider->models as $item) {
                $arrayRoles[$item->item_name] = $item->item_name;
            }
        ?>

        <?= $form->field($modelAssign, 'user_id', [
            "template" => "{input}"
        ])->hiddenInput(['value' => $model->id])->label(false) ?>

        <?= $form->field($modelAssign, 'item_name', [
            "template" => "{label}\n{$formGroup}\n{error}"
        ])->dropDownList($arrayRoles, ['value' => 'guest', 'class' => 'form-control', 'aria-describedby' => 'basic-addon'])->label('Добавить роль пользователю') ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>