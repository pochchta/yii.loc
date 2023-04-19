/**
 * Обработка меню при использовании с формами
 */
$(window).on('load', function() {
    gettingYiiParams().done(function () {
        window.filterTabsData = new dataObj();
        // setParamsToFiltersForm();
        // setParamsToCheckbox();
        // window.filterTabsData.deferred.done(function () {
        //     setParamsToFiltersItemList();
        // });
        initCatalogTabs();
        initHandlers();
    })
})

/**
 * Установка выбранных пунктов меню .checkboxList span.checked
 * @param tabName - tab data-name
 */
function setParamsToCheckbox(tabName = '') {
    if (tabName.length > 0) {  // задан только один тип значения
        let $checkboxList = $('#filters-form .tabs_content>div[data-name="' + tabName + '"]>.checkboxList');
        $checkboxList.children('span.checked').removeClass('checked');

        let value = $('#active-form input[name=' + tabName + ']').val();

        $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span:contains("' + value + '")')
            .filter(function() {
                return $(this).text() === value;
            })
            .first()
            .addClass('checked');

    } else {
        $('#filters-form span.checked').removeClass('checked');     // очистка всех выбранных span

        let tabsData = window.filterTabsData.getObject();
        for (let tabName in tabsData) {
            let value = $('#active-form input[name=' + tabName + ']').val();

            $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span:contains("' + value + '")')
                .filter(function() {
                    return $(this).text() === value;
                })
                .first()
                .addClass('checked');
        }
    }
}

/**
 * Назначение обработчиков событий filter-tabs
 */
function initHandlers() {
    $('.catalogTabs')
        .on('click', '.checkboxList>span', function() {
            let name = $(this).parent().parent().attr('data-name');
            let value = $(this).text();
            $('#active-form input[name=' + name + ']').val(value);
            setParamsToCheckbox(name);
        })

    $('#active-form input')
        .on('input', function (e) {
            let tabName = $(e.target).attr('name');
            if (window.filterTabsData.getObject().hasOwnProperty(tabName)) {
                setParamsToCheckbox(tabName);
            }
        })
}

function onSelectAutoComplete(e, ui) {
    let $input = $(e.target);
    $input.val(ui.item.label);
    let tabName = $input.attr('name');
    if (window.filterTabsData.getObject().hasOwnProperty(tabName)) {
        setParamsToCheckbox(tabName);
    }
}