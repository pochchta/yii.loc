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
            document.dispatchEvent(new CustomEvent("gcs:success", {
                detail: { msg: msg }
            }));
        },
        complete: function (jqXHR, textStatus) {
            if (textStatus !== 'success') {
                document.dispatchEvent(new CustomEvent("gcs:error", {
                    detail: { msg: textStatus }
                }));
            }
        }
    });
}

function initGridColumnSort() {
    $( "#grid_column_sort ul" ).sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-state-highlight",
        cancel: ".ui-state-disabled",
    }).disableSelection();

    $( ".show_grid_column_sort" ).click(function(){
        $( "#grid_column_sort" ).show();
    });
    $( "#grid_column_sort .hide_grid_column_sort" ).click(function(){
        $( "#grid_column_sort" ).hide();
    });
}