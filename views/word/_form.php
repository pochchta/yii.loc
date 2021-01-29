<?php

use app\models\Status;
use app\models\Word;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Word */
/* @var $form yii\widgets\ActiveForm */
/* @var $arrSecondCategory array */
/* @var $arrThirdCategory array */
?>

<div class="word-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form1',
    ]); ?>

    <?= $form->field($model, 'categoryName')->dropDownList(
        [Status::NOT_CATEGORY => 'нет'] + array_combine(array_keys(Word::FIELD_WORD), Word::LABEL_FIELD_WORD)
    ) ?>

    <?= $form->field($model, 'parentName')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                $.getJSON('" . Url::to('ajax-one') . "', {
                term: request.term,
                parent: $('#word-categoryname').val(),
            }, response);
        }"),
                'minLength' => 3,
                'delay' => 300
            ],
            'options' => [
                'class' => 'form-control',
            ]
        ]
    ); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'saveButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
