<?php

use app\models\Status;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Verification */
/* @var $form yii\widgets\ActiveForm */
?>

<p><?=
    'Относится к прибору: '
    . Html::a(
        $model->device->wordName->name . ', №' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : ''),
        ['device/view', 'id' => $model->device_id]
    )
    ?></p>

<div class="verification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'device_id')->dropDownList(
        [$model->device_id => $model->device->number]
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

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
