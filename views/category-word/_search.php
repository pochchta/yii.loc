<?php

use app\models\CategoryWord;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CategoryWordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-word-search">

    <?php $form = ActiveForm::begin([
        'id' => 'form1',
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?= $form->field($model, 'firstCategory')->dropDownList(
        [CategoryWord::ALL => 'все', '0' => 'нет'] + CategoryWord::getAllNames(0),
        [
            'onchange'=>'$.pjax.reload({
                container: "#my-pjax-container", 
                url: "' . Url::to('index') . '",
                type: "GET",
		        data: $("#form1").serialize(),
                timeout: ' . Yii::$app->params['pjaxTimeout'] . ',
            });',
        ]
    ) ?>

    <?php ActiveForm::end(); ?>

</div>
