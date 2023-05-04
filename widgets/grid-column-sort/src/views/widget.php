<?php

use app\widgets\gcs\GridColumnSort;
use yii\helpers\Html;
?>
<div id='grid_column_sort' class='connected-sortable-columns clearfix' hidden>
    <h3>Настройка столбцов таблицы</h3>
    <ul class='connected-sortable sortable1'>
        <? foreach($columns['enabled'] as $value) echo "<li>$value</li>" ?>
    </ul>
    <ul class='connected-sortable sortable2'>
        <? foreach($columns['disabled'] as $value) echo "<li>$value</li>" ?>
    </ul>

    <div class="control">
        <?= Html::dropDownList(
            'profileView',
            Yii::$app->user->identity->getProfileView(),
            GridColumnSort::getListProfileView(),
            [
                'id' => 'load_grid_column_sort',
                'class' => 'form-control'
            ]
        ) ?>
        <?= Html::a('Сохранить', null, [
            'id' => 'save_grid_column_sort',
            'class' => 'btn btn-success',
        ]) ?>
        <?= Html::a('Закрыть', null, [
            'id' => 'hide_grid_column_sort',
            'class' => 'btn btn-warning toggle-connected-sortable-columns',
        ]) ?>
    </div>
</div>