<?php

use app\assets\FormAsset;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Device */
/* @var $searchModel app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */

FormAsset::register($this);
?>

<?= $this->render('catalog-tabs/form', compact(
    'menu'
)); ?>

<div class="device-form">

    <?php $form = ActiveForm::begin([
        'id' => 'active-form',
    ]); ?>

    <?= $form->field($model, $attribute ='kind')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'device']
            ]
        ]
    ); ?>

    <?= $form->field($model, $attribute ='name')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'device']
            ]
        ]
    ); ?>

    <?= $form->field($model, $attribute ='state')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'device']
            ]
        ]
    ); ?>

    <?= $form->field($model, $attribute ='department')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'device']
            ]
        ]
    ); ?>

    <?= $form->field($model, $attribute ='position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, $attribute ='crew')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'device']
            ]
        ]
    ); ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
