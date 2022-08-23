function takeColumnsFromHtml() {
    let $elements = $('#grid_column_sort #sortable1 li');
    return Array.from($elements).map(item => item.innerText);
}

/**
 * Сохранение выбранных и отсортированных столбцов
 */
function saveGridColumnSort(e) {
    $.ajax({
        method: "POST",
        url: e.data.writeUrl,
        data: {
            name: e.data.name,
            role: e.data.role,
            col: JSON.stringify(takeColumnsFromHtml()),
        },
        success: function (msg) {
            console.log('success');
            console.log(msg);

        },
        complete: function (jqXHR, textStatus) {
            console.log(textStatus);
            if (textStatus !== 'success') {
                console.log('ошибка')
            }
        }
    });
}

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