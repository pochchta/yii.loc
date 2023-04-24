<?php

use app\assets\FormAsset;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Word */
/* @var $form yii\widgets\ActiveForm */
/* @var $arrSecondCategory array */
/* @var $arrThirdCategory array */

FormAsset::register($this);
?>

<?= $this->render('/catalog-tabs/form', compact(
    'menu'
)); ?>

<div class="word-form">

    <?php $form = ActiveForm::begin([
        'id' => 'active-form',
    ]); ?>

    <?= $form->field($model, 'parent_name')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'word']
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
