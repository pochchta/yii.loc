<?php

use app\widgets\sort\GridColumnSort;
use yii\helpers\Html;
?>
<div id='grid_column_sort' class='clearfix'>
    <h3>Настройка столбцов таблицы</h3>
    <ul id='sortable1' class='connectedSortable'>
        <? foreach($columns['enabled'] as $key => $value) echo "<li id='$key'>$value</li>" ?>
    </ul>
    <ul id='sortable2' class='connectedSortable'>
        <? foreach($columns['disabled'] as $key => $value) echo "<li id='$key'>$value</li>" ?>
    </ul>

    <div class="control">
        <?= Html::dropDownList(
            'profileView',
            Yii::$app->user->identity->getProfileView(),
            GridColumnSort::getListProfileView(),
            [
                'class' => 'form-control'
            ]
        ) ?>
        <?= Html::a('Сохранить', null, [
            'id' => 'save_grid_column_sort',
            'class' => 'btn btn-success',
        ]) ?>
        <?= Html::a('Закрыть', null, [
            'id' => 'hide_grid_column_sort',
            'class' => 'btn btn-warning',
        ]) ?>
    </div>
</div>