<?php

use app\assets\FormAsset;
use app\models\Status;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Verification */
/* @var $form yii\widgets\ActiveForm */

FormAsset::register($this);
?>

<p><?=
    'Относится к прибору: '
    . Html::a(
        $model->device->wordName->name . ', №' . $model->device->number . ($model->device->deleted_id == Status::DELETED ? ' (удален)' : ''),
        ['device/view', 'id' => $model->device_id]
    )
    ?></p>

<div class="verification-form">

    <?php $form = ActiveForm::begin([
        'id' => 'active-form',
    ]); ?>

    <?= $form->field($model, 'device_id')->dropDownList(
        [$model->device_id => $model->device->number]
    ) ?>

    <?= $form->field($model, $attribute ='type')->textInput(['maxlength' => true])->widget(
        AutoComplete::class, [
            'options' => [
                'class' => 'form-control',
                'data' => ['parent' => 'verification']
            ]
        ]
    ); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'last_date')->input('date', ['value' =>
        (new DateTime())->setTimestamp($model->last_date)->format('Y-m-d')
    ]) ?>

    <?= $form->field($model, 'period')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
