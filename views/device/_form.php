<?php

use app\models\DeviceSearch;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Device */
/* @var $searchModel app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, $attribute ='name')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, $attribute ='type')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, $attribute ='department')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, $attribute ='position')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, $attribute ='scale')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, $attribute ='accuracy')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, DeviceSearch::getAutoCompleteOptions($attribute)
    ); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
