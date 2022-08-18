
$( document ).ready(function(){
    $(function() {
        $( "#grid_column_sort ul" ).sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight"
        }).disableSelection();
    });

    $(function() {
        $( ".show_grid_column_sort" ).click(function(){
            $( "#grid_column_sort" ).show();
        });
        $( "#grid_column_sort .hide_grid_column_sort" ).click(function(){
            $( "#grid_column_sort" ).hide();
        });
    });

});