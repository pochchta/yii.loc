<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <h1>Профиль</h1>
    <p>
        Информация о пользователе <?= Html::encode(\yii::$app->user->identity->username) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'item_name',
                'label' => 'Роль',
                'value' => function ($data) {
                    return Html::a(
                        $data->item_name,
                        ['/admin/auth/view', 'id' => $data->item_name],
                        ['title' => $data->item->description]
                    );
                },
                'format' => 'html'
            ],
            'item.description',
            [
                'label' => 'Разрешения',
                'value' => function ($data) {
                    $ret = '';
                    $lineBreak = Yii::$app->formatter->asNtext(",\n");
                    foreach ($data->permits as $item) {
                        $ret .= Html::a(
                                $item->child,
                                ['/admin/auth/view', 'id' => $item->child],
                                ['title' => $item->itemChild->description]
                            ).$lineBreak;
                    }
                    $ret = rtrim($ret, $lineBreak);
                    return $ret;
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Назначено',
                'format' => 'date'
            ],
        ],
    ]); ?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'oldPass')
            ->label('Старый пароль')
            ->passwordInput(['maxlength' => true])
        ?>

        <?= $form->field($model, 'newPass')
            ->label('Новый пароль')
            ->passwordInput(['maxlength' => true])
        ?>

        <?= $form->field($model, 'newPassRepeat')
            ->label('Повтор нового пароля')
            ->passwordInput(['maxlength' => true])
        ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>