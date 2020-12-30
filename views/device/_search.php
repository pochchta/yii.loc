<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $arrDepartments array */
/* @var $arrScales array */

$jsOnChange = [
    'onchange'=>'$.pjax.reload({
        container: "#my-pjax-container", 
        url: "' . Url::to(['', 'id' => $model->id]) . '",
        type: "GET",
        data: $("#form1").serialize(),
        timeout: ' . Yii::$app->params['pjaxTimeout'] . ',
    });',
]
?>

<div class="device-search">
    <?php $form = ActiveForm::begin([
        'id' => 'form1',
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true]
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'firstDepartment')->dropDownList(
                $arrDepartments['arrFirstCategory'], $jsOnChange
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'secondDepartment')->dropDownList(
                $arrDepartments['arrSecondCategory'], $jsOnChange
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'thirdDepartment')->dropDownList(
                $arrDepartments['arrThirdCategory'], $jsOnChange
            ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'firstScale')->dropDownList(
                $arrScales['arrFirstCategory'], $jsOnChange
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'secondScale')->dropDownList(
                $arrScales['arrSecondCategory'], $jsOnChange
            ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'thirdScale')->dropDownList(
                $arrScales['arrThirdCategory'], $jsOnChange
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
