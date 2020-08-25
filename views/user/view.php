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
                'label' => 'Роль'
            ],
            [
                'value' => function ($data) {
                    $ret = '';
                    $arrPerms = Yii::$app->authManager->getPermissionsByRole($data->item_name);
                    foreach ($arrPerms as $item) {
                        $ret .= ' '.$item->name.',';
                    }
                    $ret = rtrim($ret, ',');
                    return $ret;
                },
                'format' => 'ntext',
                'label' => 'Разрешения'
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

            $allRoles = Yii::$app->authManager->getRoles();
            $arrayRoles = array();
            foreach ($allRoles as $item) {
                $arrayRoles[$item->name] = $item->name;
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