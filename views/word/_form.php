<?php

use app\models\Status;
use app\models\Word;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Word */
/* @var $form yii\widgets\ActiveForm */
/* @var $arrSecondCategory array */
/* @var $arrThirdCategory array */

/**
 * @param $model
 * @return string[]
 */
function getPjaxJsOnChange($model)
{
    return [
        'onchange' => '$.pjax.reload({
            container: "#my-pjax-container", 
            url: "' . Url::to(['', 'id' => $model->id]) . '",
            type: "POST",
            data: $("#form1").serialize(),
            timeout: ' . Yii::$app->params['pjaxTimeout'] . ',
        });',
    ];
}
?>

<div class="word-form">

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
//        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'form1',
//        'options' => ['data-pjax' => true]
    ]); ?>

    <?= $form->field($model, 'firstCategory')->dropDownList(
        [Status::NOT_CATEGORY => 'нет'] + Word::LABEL_FIELD_WORD, getPjaxJsOnChange($model)
    ) ?>

    <?= $form->field($model, 'secondCategory')->dropDownList(
        [Status::NOT_CATEGORY => 'нет'] + $arrSecondCategory, getPjaxJsOnChange($model)
    ) ?>

    <?= $form->field($model, 'thirdCategory')->dropDownList(
        [Status::NOT_CATEGORY => 'нет'] + $arrThirdCategory
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'name' => 'saveButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

</div>
