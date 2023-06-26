class csc {
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
                role: csc.takeRoleFromHtml($rootElement),
                col: JSON.stringify(csc.takeColumnsFromHtml($rootElement)),
            },
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + e.data.token);
            },
            success: function (msg) {
                if (msg === true) {
                    flash.add('Столбцы таблицы: сохранено');
                } else {
                    flash.add('Столбцы таблицы: ошибка сохранения', 'danger');
                }
                document.dispatchEvent(new CustomEvent("csc:save_success", {
                    detail: {
                        msg: msg,
                        widget_name: e.data.widget_name,
                    }
                }));
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("csc:save_error", {
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
                role: csc.takeRoleFromHtml($rootElement),
            },
            success: function (msg) {
                document.dispatchEvent(new CustomEvent("csc:load_success", {
                    detail: {
                        msg: msg,
                        widget_name: e.data.widget_name,
                    }
                }));
                csc.updateColumns(JSON.parse(msg), e);
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    document.dispatchEvent(new CustomEvent("csc:load_error", {
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

    static initControl($element) {
        $element
            .on('click', '.toggle-connected-sortable-columns' , function() {
                $element.toggle();
            })
    }

    static initSortable($element) {
        $element.find('ul').sortable({
            connectWith: ".connected-sortable",
            placeholder: "ui-state-highlight",
            cancel: ".ui-state-disabled",
        }).disableSelection();
    }

    static initObserver($element) {
        let observer = new ResizeObserver(function (elements) {
            document.dispatchEvent(new CustomEvent("csc:resize", {
                detail: {
                    elements: elements,
                }
            }));
        });
        observer.observe($element[0]);
    }

    static init() {
        let $elements = $('.connected-sortable-columns:not([data-init="true"])');
        for (let element of $elements) {
            let $element = $(element);
            csc.initControl($element);
            csc.initSortable($element);
            csc.initObserver($element);
            $element.attr('data-init', true);
        }
    }
}

$(window).on('load', function() {
    csc.init();
})
$(document).on('pjax:complete', function() {
    csc.init();
})