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
            document.dispatchEvent(new CustomEvent("gcs:save_success", {
                detail: { msg: msg }
            }));
            if (JSON.parse(msg) === true) {
                showMessage('Столбцы таблицы: сохранено');
            } else {
                showMessage('Столбцы таблицы: ошибка сохранения', 'danger');
            }
        },
        complete: function (jqXHR, textStatus) {
            if (textStatus !== 'success') {
                document.dispatchEvent(new CustomEvent("gcs:save_error", {
                    detail: { msg: jqXHR.responseText }
                }));
                showMessage('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
            }
        }
    });
}

function loadGridColumnSort(e) {
    $.ajax({
        method: "POST",
        url: e.data.readUrl,
        data: {
            name: e.data.name,
            role: takeRoleFromHtml(),
        },
        success: function (msg) {
            document.dispatchEvent(new CustomEvent("gcs:load_success", {
                detail: { msg: msg }
            }));
            updateColumns(JSON.parse(msg), e.data);
        },
        complete: function (jqXHR, textStatus) {
            if (textStatus !== 'success') {
                document.dispatchEvent(new CustomEvent("gcs:load_error", {
                    detail: { msg: jqXHR.responseText }
                }));
                showMessage('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
            }
        }
    });
}

function updateColumns(columnsAfter, params) {
    let $columns = $('#grid_column_sort ul li');
    let $sortable1 = $('#grid_column_sort #sortable1');
    let $sortable2 = $('#grid_column_sort #sortable2');

    let arrayBeforeUpdate = [];
    for (let column of $columns) {
        let $column = $(column);
        arrayBeforeUpdate.push($column.text());
    }

    $columns.remove();

    for (let name of arrayBeforeUpdate) {
        if (columnsAfter.includes(name) || params.required.includes(name)) {
            $sortable1.append('<li>' + name + '</li>')
        } else {
            $sortable2.append('<li>' + name + '</li>')
        }
    }
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