<?php

use app\assets\FormAsset;
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

FormAsset::register($this);
?>

<?= $this->render('catalog-tabs/form', compact(
    'menu'
)); ?>

<div class="word-form">

    <?php $form = ActiveForm::begin([
        'id' => 'active-form',
    ]); ?>

    <?= $form->field($model, 'category_name')->dropDownList(
        array_combine(array_keys(Word::FIELD_WORD), Word::LABEL_FIELD_WORD)
    ) ?>
    <?= $form->field($model, 'parent')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('/word/list-auto-complete') . "', {
                        term: request.term,
                        term_p1: $('#category_name').val(),
                    }, response);
                }"),
                'minLength' => Yii::$app->params['minSymbolsAutoComplete'],
                'delay' => Yii::$app->params['delayAutoComplete']
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
