<?php


namespace app\widgets;


use yii\bootstrap\Widget;

class GridColumnSort extends Widget
{
    public function run()
    {
/*        echo "<ul id='sortable'>";
        foreach (range(1,20) as $item) {
            echo "<li class='ui-state-default'>Item №$item</li>";
        }
        echo "</ul>";*/
        echo "
            <div id='grid_column_sort' class='clearfix'>
                <ul id='sortable1' class='connectedSortable'>
                <li class='ui-state-default'>Item 1</li>
                <li class='ui-state-default'>Item 2</li>
                <li class='ui-state-default'>Item 3</li>
                <li class='ui-state-default'>Item 4</li>
                <li class='ui-state-default'>Item 5</li>
            </ul>
             
            <ul id='sortable2' class='connectedSortable'>
                <li class='ui-state-highlight'>Item 1</li>
                <li class='ui-state-highlight'>Item 2</li>
                <li class='ui-state-highlight'>Item 3</li>
                <li class='ui-state-highlight'>Item 4</li>
                <li class='ui-state-highlight'>Item 5</li>
            </ul>
            
            <button class='hide_grid_column_sort' id='save'>save</button>
            
            </div>
        ";
    }
}