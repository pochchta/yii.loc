<div id='grid_column_sort' class='clearfix'>
    <ul id='sortable1' class='connectedSortable'>
        <? foreach($columns['enabled'] as $key => $value) echo "<li id='$key' class='ui-state-default'>$value</li>" ?>
    </ul>
    <ul id='sortable2' class='connectedSortable'>
        <? foreach($columns['disabled'] as $key => $value) echo "<li id='$key' class='ui-state-highlight'>$value</li>" ?>
    </ul>
    <button id='save_grid_column_sort'>Сохранить</button>
</div>
