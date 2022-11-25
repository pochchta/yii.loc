<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\models\profile\ChangeViewForm */

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Профиль</h1>

<div class="row">

    <div class="col-xs-4">
        <?= $this->render('menu') ?>
    </div>

    <div class="col-xs-8">
        <h4>Смена профиля для отображения и сортировки колонок таблиц</h4>

        <div class="user-form">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'profileView')->dropDownList(
                $model::getListProfileView()
            ) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>