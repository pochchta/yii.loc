<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */

$arrDepartment = $model->arrDepartment;
$arrScale = $model->arrScale;
$arrName = $model->arrName;
$arrType = $model->arrType;
$arrPosition = $model->arrPosition;
$arrAccuracy = $model->arrAccuracy;

$jsOnChange = ['onchange'=>'pjaxPost("' . Url::to(['', 'id' => $model->id]) . '","' . Yii::$app->params['pjaxTimeout'] . '")'];
?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstName')->dropDownList(
            $arrName['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondName')->dropDownList(
            $arrName['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdName')->dropDownList(
            $arrName['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstType')->dropDownList(
            $arrType['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondType')->dropDownList(
            $arrType['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdType')->dropDownList(
            $arrType['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstDepartment')->dropDownList(
            $arrDepartment['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondDepartment')->dropDownList(
            $arrDepartment['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdDepartment')->dropDownList(
            $arrDepartment['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstPosition')->dropDownList(
            $arrPosition['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondPosition')->dropDownList(
            $arrPosition['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdPosition')->dropDownList(
            $arrPosition['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstScale')->dropDownList(
            $arrScale['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondScale')->dropDownList(
            $arrScale['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdScale')->dropDownList(
            $arrScale['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'firstAccuracy')->dropDownList(
            $arrAccuracy['arrFirstCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'secondAccuracy')->dropDownList(
            $arrAccuracy['arrSecondCategory'], $jsOnChange
        ) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'thirdAccuracy')->dropDownList(
            $arrAccuracy['arrThirdCategory'], $jsOnChange
        ) ?>
    </div>
</div>