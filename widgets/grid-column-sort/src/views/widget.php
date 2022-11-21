<?php use yii\helpers\Html; ?>
<div id='grid_column_sort' class='clearfix'>
    <ul id='sortable1' class='connectedSortable'>
        <? foreach($columns['enabled'] as $key => $value) echo "<li id='$key'>$value</li>" ?>
    </ul>
    <ul id='sortable2' class='connectedSortable'>
        <? foreach($columns['disabled'] as $key => $value) echo "<li id='$key'>$value</li>" ?>
    </ul>

    <div class="control">
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