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
    let $gcsWindow = $('#grid_column_sort');
    showLoadingWindow($gcsWindow);

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
            hideLoadWindow($gcsWindow);
        }
    });
}

function loadGridColumnSort(e) {
    let $gcsWindow = $('#grid_column_sort');
    showLoadingWindow($gcsWindow);

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
            hideLoadWindow($gcsWindow);
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

/**
 * Создание и отображение окна "загрузка" поверх переданного элемента jquery
 * @param $selector jquery
 */
function showLoadingWindow($selector) {
    let loading_window_id = $selector.attr('id') + '_loading_window';
    let $window = $('#' + loading_window_id);
    if (! $window.length) {
        let offsetZIndex = 10;
        let newZIndex = + $selector.css('zIndex') + offsetZIndex;
        $window = $('<div>Загрузка</div>');
        $window.position($selector.position());
        $window.css('position', 'absolute');
        $window.css('zIndex', newZIndex);
        $window.css('background', '#eeeeeeb8');
        $window.attr('id', loading_window_id);

        let selectorWidth = $selector.width();
        $window.css('font-size', (selectorWidth / 15) + 'px');
        $window.css('padding', (selectorWidth / 15) + 'px');

    }

    $window.css('width', $selector.css('width'));
    $window.css('height', $selector.css('height'));
    $window.css('display', 'block');

    $window.insertAfter($selector);
}

/**
 * Сокрытие окна "загрузка" над переданным элементом
 * @param $selector jquery
 */
function hideLoadWindow($selector) {
    let loading_window_id = $selector.attr('id') + '_loading_window';
    let $window = $('#' + loading_window_id);
    if ($window.length) {
        $window.css('display', 'none');
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