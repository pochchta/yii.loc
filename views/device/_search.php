<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DeviceSearch */
/* @var $form yii\widgets\ActiveForm */

$arrDepartment = $model->arrDepartment;
$arrScale = $model->arrScale;

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

    <?php ActiveForm::end(); ?>

</div>
