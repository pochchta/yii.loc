<?php

use app\models\Word;
use app\models\Status;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="word-search">

    <?php $form = ActiveForm::begin([
        'id' => 'form1',
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?= $form->field($model, 'firstCategory')->dropDownList(
        [Status::ALL => 'все', '0' => 'нет'] + Word::getAllNames(0),
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
