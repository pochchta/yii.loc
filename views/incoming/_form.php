<?php

use app\models\Incoming;
use app\models\Status;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Incoming */
/* @var $form yii\widgets\ActiveForm */
?>

<p><?=
    'Относится к прибору: '
    . Html::a(
        $model->device->name . ', №' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : ''),
        ['device/view', 'id' => $model->device_id]
    )
    ?></p>

<div class="incoming-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'device_id')->dropDownList(
        [$model->device_id => $model->device->number]
    ) ?>

    <?= $form->field($model, 'status')->dropDownList([
        Incoming::INCOMING => 'Принят',
        Incoming::READY => 'Готов',
        Incoming::OUTGOING => 'Выдан',
    ]) ?>

    <?= $form->field($model, 'payment')->dropDownList([
        Incoming::NOT_PAID => 'Не оплачен',
        Incoming::PAID => 'Оплачен',
    ]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
