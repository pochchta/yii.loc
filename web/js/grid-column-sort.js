function takeColumnsFromHtml() {
    let $elements = $('#grid_column_sort #sortable1 li');
    return Array.from($elements).map(item => item.innerText);
}

function takeRoleFromHtml() {
    let $element = $('#grid_column_sort select.form-control');
    return $element.val();
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
            role: takeRoleFromHtml(),
            col: JSON.stringify(takeColumnsFromHtml()),
        },
        success: function (msg) {
            document.dispatchEvent(new CustomEvent("gcs:success", {
                detail: { msg: msg }
            }));
            if (msg === true) {
                showMessage('Столбцы таблицы: сохранено');
            } else {
                showMessage('Столбцы таблицы: ошибка сохранения', 'danger');
            }
        },
        complete: function (jqXHR, textStatus) {
            if (textStatus !== 'success') {
                document.dispatchEvent(new CustomEvent("gcs:error", {
                    detail: { msg: jqXHR.responseText }
                }));
                showMessage('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
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
    $( "#hide_grid_column_sort" ).click(function(){
        $( "#grid_column_sort" ).hide();
    });
}