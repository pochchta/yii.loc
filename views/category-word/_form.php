<?php

use app\models\CategoryWord;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\CategoryWord */
/* @var $form yii\widgets\ActiveForm */
/* @var $arrSecondCategory array */

?>

<div class="category-word-form">

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
//        'timeout' => Yii::$app->params['pjaxTimeout']
    ]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'form1',
//        'options' => ['data-pjax' => true]
    ]); ?>

    <?= $form->field($model, 'firstCategory')->dropDownList(
        [0 => 'нет'] + CategoryWord::LABEL_FIELD_WORD,
        [
            'onchange'=>'$.pjax.reload({
                container: "#my-pjax-container", 
                url: "' . Url::to(['', 'id' => $model->id]) . '",
                type: "POST",
		        data: $("#form1").serialize(),
                timeout: ' . Yii::$app->params['pjaxTimeout'] . ',
            });',
        ]
    ) ?>

    <?= $form->field($model, 'secondCategory')->dropDownList(
        [0 => 'нет'] + $arrSecondCategory
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'name' => 'saveButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

</div>
