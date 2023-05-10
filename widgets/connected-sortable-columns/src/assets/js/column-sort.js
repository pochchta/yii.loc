class gcs {
    static takeColumnsFromHtml($rootElement) {
        let $elements = $rootElement.find('.sortable1 li');
        return Array.from($elements).map(item => item.innerText);
    }

    static takeRoleFromHtml($rootElement) {
        let $element = $rootElement.find('select.form-control');
        return $element.val();
    }

    /**
     * Сохранение выбранных и отсортированных столбцов
     */
    static save(e) {
        let $rootElement = $(e.target).closest('.connected-sortable-columns');
        loadingWindow.show($rootElement);

        $.ajax({
            method: "POST",
            url: e.data.write_url,
            data: {
                widget_name: e.data.widget_name,
                name: e.data.name,
                role: gcs.takeRoleFromHtml($rootElement),
                col: JSON.stringify(gcs.takeColumnsFromHtml($rootElement)),
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + e.data.token);
            },
            success: function (msg) {
                document.dispatchEvent(new CustomEvent("gcs:save_success", {
                    detail: {
                        msg: msg,
                        widget_name: e.data.widget_name,
                    }
                }));
                if (msg === true) {
                    flash.add('Столбцы таблицы: сохранено');
                } else {
                    flash.add('Столбцы таблицы: ошибка сохранения', 'danger');
                }
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("gcs:save_error", {
                        detail: {
                            msg: jqXHR.responseText,
                            widget_name: e.data.widget_name,
                        }
                    }));
                    flash.add('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
                }
                loadingWindow.hide($rootElement);
            }
        });
    }

    /**
     * Загрузка стобцов по профилю
     */
    static load(e) {
        let $rootElement = $(e.target).closest('.connected-sortable-columns');
        loadingWindow.show($rootElement);

        $.ajax({
            method: "POST",
            url: e.data.read_url,
            data: {
                widget_name: e.data.widget_name,
                name: e.data.name,
                role: gcs.takeRoleFromHtml($rootElement),
            },
            success: function (msg) {
                document.dispatchEvent(new CustomEvent("gcs:load_success", {
                    detail: {
                        msg: msg,
                        widget_name: e.data.widget_name,
                    }
                }));
                gcs.updateColumns(msg, e);
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("gcs:load_error", {
                        detail: {
                            msg: jqXHR.responseText,
                            widget_name: e.data.widget_name,
                        }
                    }));
                    flash.add('Столбцы таблицы: ' + jqXHR.responseText, 'danger');
                }
                loadingWindow.hide($rootElement);
            }
        });
    }

    static updateColumns(columnsAfter, e) {
        let $rootElement = $(e.target).closest('.connected-sortable-columns');

        let $columns = $rootElement.find('ul li');
        let $sortable1 = $rootElement.find('.sortable1');
        let $sortable2 = $rootElement.find('.sortable2');

        let arrayBeforeUpdate = [];
        for (let column of $columns) {
            let $column = $(column);
            arrayBeforeUpdate.push($column.text());
        }

        $columns.remove();

        for (let name of columnsAfter) {
            $sortable1.append('<li>' + name + '</li>')
        }
        const arrayDiff = arrayBeforeUpdate.filter(x => !columnsAfter.includes(x)).sort();
        for (let name of arrayDiff) {
            $sortable2.append('<li>' + name + '</li>')
        }
    }

    static initControl() {
        $(document)
            .on('click', function(e) {
                let $button = $(e.target);
                if ($button.hasClass('toggle-connected-sortable-columns')) {
                    $button.closest('.connected-sortable-columns').toggle();
                }
            })
    }

    static initSortable() {
        let $elements = $('.connected-sortable-columns:not([data-init="true"])');
        for (let element of $elements) {
            let $element = $(element);
            $element.find('ul').sortable({
                connectWith: ".connected-sortable",
                placeholder: "ui-state-highlight",
                cancel: ".ui-state-disabled",
            }).disableSelection();
            $element.attr('data-init', true);
        }
    }
}

$(window).on('load', function() {
    gcs.initControl();
    gcs.initSortable();
})
$(document).on('pjax:complete', function() {
    gcs.initSortable();
})