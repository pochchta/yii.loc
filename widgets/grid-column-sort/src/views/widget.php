<?php

use app\widgets\sort\GridColumnSort;
use yii\helpers\Html;
?>
<div id='grid_column_sort' class='clearfix'>
    <h3>Настройка столбцов таблицы</h3>
    <ul id='sortable1' class='connectedSortable'>
        <? foreach($columns['enabled'] as $value) echo "<li>$value</li>" ?>
    </ul>
    <ul id='sortable2' class='connectedSortable'>
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
            'class' => 'btn btn-warning',
        ]) ?>
    </div>
</div>