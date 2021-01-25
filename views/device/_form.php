<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Device */
/* @var $searchModel app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */

function getAutoCompleteOptions($attribute) {
    return [
        'clientOptions' => [
            'source' => new JsExpression("function(request, response) {
                $.getJSON('" . Url::to('ajax-one') . "', {
                term: request.term,
            }, response);
        }"),
            'select' => new JsExpression("function( event, ui ) {
                $('#device-{$attribute}_id').val(ui.item.id);
            }"),
            'minLength' => 3,
            'delay' => 300
        ],
        'options' => [
            'class' => 'form-control',
        ]
    ];
}
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, $attribute ='name')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'name_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, $attribute ='type')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'type_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, $attribute ='department')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'department_id')/*->hiddenInput()*/->label(false) ?>

    <?= $form->field($model, $attribute ='position')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'position_id')/*->hiddenInput()*/->label(false) ?>

    <?= $form->field($model, $attribute ='scale')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'scale_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, $attribute ='accuracy')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, getAutoCompleteOptions($attribute)
    ); ?>
    <?= $form->field($model, 'accuracy_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
