class gcs {
    static takeColumnsFromHtml() {
        let $elements = $('#grid_column_sort #sortable1 li');
        return Array.from($elements).map(item => item.innerText);
    }

    static takeRoleFromHtml() {
        let $element = $('#grid_column_sort select.form-control');
        return $element.val();
    }

    /**
     * Сохранение выбранных и отсортированных столбцов
     */
    static save(e) {
        let $gcsWindow = $('#grid_column_sort');
        loadingWindow.show($gcsWindow);

        $.ajax({
            method: "POST",
            url: e.data.writeUrl,
            data: {
                name: e.data.name,
                role: gcs.takeRoleFromHtml(),
                col: JSON.stringify(gcs.takeColumnsFromHtml()),
            },
            success: function (msg) {
                document.dispatchEvent(new CustomEvent("gcs:save_success", {
                    detail: { msg: msg }
                }));
                if (JSON.parse(msg) === true) {
                    flash.add('Столбцы таблицы: сохранено');
                } else {
                    flash.add('Столбцы таблицы: ошибка сохранения', 'danger');
                }
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("gcs:save_error", {
                        detail: { msg: jqXHR.responseText }
                    }));
                    flash.add('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
                }
                loadingWindow.hide($gcsWindow);
            }
        });
    }

    /**
     * Загрузка стобцов по профилю
     */
    static load(e) {
        let $gcsWindow = $('#grid_column_sort');
        loadingWindow.show($gcsWindow);

        $.ajax({
            method: "POST",
            url: e.data.readUrl,
            data: {
                name: e.data.name,
                role: gcs.takeRoleFromHtml(),
            },
            success: function (msg) {
                document.dispatchEvent(new CustomEvent("gcs:load_success", {
                    detail: { msg: msg }
                }));
                gcs.updateColumns(JSON.parse(msg), e.data);
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("gcs:load_error", {
                        detail: { msg: jqXHR.responseText }
                    }));
                    flash.add('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
                }
                loadingWindow.hide($gcsWindow);
            }
        });
    }

    static updateColumns(columnsAfter, params) {
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

    static enableSortable() {
        $( "#grid_column_sort ul").sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight",
            cancel: ".ui-state-disabled",
        }).disableSelection();
    }

    static init() {
        $(document)
            .on('mouseover', function(event) {
                if (event.target.id === 'grid_column_sort') {
                    let $grid = $("#grid_column_sort");
                    if ($grid.data('init') !== true) {
                        gcs.enableSortable();
                        $grid.data({init: true});
                    }
                }
            })
            .on('click', function(event) {
                if (event.target.id === 'hide_grid_column_sort') {
                    let elem = document.getElementById('grid_column_sort');
                    elem.hidden = !elem.hidden;
                }
            })
    }
}

$(window).on('load', function() {
    gcs.init();
})