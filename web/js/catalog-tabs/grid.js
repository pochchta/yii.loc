/**
 * Обработка меню при использовании с gridView
 */
$(window).on('load', function() {
    gettingYiiParams().done(function () {
        window.filterTabsData = new dataObj();
        window.filterTabsData.update();
        setParamsToFiltersForm();
        setParamsToCheckbox();
        window.filterTabsData.deferred.done(function () {
            setParamsToFiltersItemList();
        });
        initCatalogTabs();
        initHandlers();
    })
})



/**
 * Установка значений в inputs формы filters_form
 */
function setParamsToFiltersForm() {
    $('#filters-form input').val('');       // очистка всех установленных значений

    for (let tab of window.filterTabsData.getArray()) {
        for(let name in tab) {
            if (name === 'label' || name === 'tabName') continue;
            if (tab.hasOwnProperty(name)) {
                $('#filters-form input[name=' + name + ']').val(tab[name]);
            }
        }
    }
}

/**
 * Установка выбранных пунктов меню .checkboxList span.checked
 * @param tabName - tab data-name
 */
function setParamsToCheckbox(tabName = '') {

    if (tabName.length > 0) {  // задан только один тип значения
        let $checkboxList = $('#filters-form .tabs_content>div[data-name="' + tabName + '"]>.checkboxList');
        $checkboxList.children('span.checked').removeClass('checked');

        let value = window.filterTabsData.getValueByTabName(tabName);

        let $span = $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span[data-value="' + value + '"]');
        $span.addClass('checked');
    } else {
        $('#filters-form span.checked').removeClass('checked');     // очистка всех выбранных span

        let tabsData = window.filterTabsData.getObject();
        for (let tabName in tabsData) {
            let value = window.filterTabsData.getValueByTabName(tabName);

            let $span = $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span[data-value="' + value + '"]');
            $span.addClass('checked');
        }
    }
}

/**
 * Установка списка установленных фильтров
 */
function setParamsToFiltersItemList() {
    // очистка списка примененных фильтров "Выводятся только: 1: 1,2; 2: 1"
    let $tabFiltersParams = $('.tabsFilterParams');
    let $list = $tabFiltersParams.find('#filters-active');
    $list.text('');
    $list.append('<span class="showOnly">Выводятся только:</span>');

    // создается заготовка под первую группу списка фильтров
    let $showGroup = $('<span class="showGroup"></span>');
    let $name = $('<span class="first"></span>');
    $showGroup.append($name);
    let $value = $('<span class="second"></span>');
    let $valueChild = $('<a class="reset-filter" title="Отменить фильтр"></a>');

    let tabsData = window.filterTabsData.getObject();
    for (let tabName in tabsData) {
        if (window.filterTabsData.checkIfNameNeedsToAdd(tabName)) {    // есть что выводить

            // название фильтра
            let $newShowGroup = $showGroup.clone();
            let $newName = $newShowGroup.children('.first');
            $newName.text(tabsData[tabName]['label'] + ': ');

            let textArray = {
                'value': '🔎 ',
                '_id': '👉 ',
                '_start': '► ',
                '_end': '◄ '
            };

            for (let key in textArray) {
                if (tabsData[tabName].hasOwnProperty(key)) {
                    let $newValue = $value.clone();
                    let $newValueChild = $valueChild.clone();

                    $newValue.text(textArray[key]);         // например '🔎 '

                    let text = window.filterTabsData.getLabelByTabName(tabName, key);
                    $newValueChild.text(text);              // например ПКЦ

                    let dataName = tabName + key;
                    if (key === 'value') {
                        dataName = tabName;
                    }
                    $newValueChild.attr('data-name', dataName);     // название поля фильтра для сброса

                    $newValue.append($newValueChild);
                    if ($newShowGroup.children('.second').length > 0) {
                        $newShowGroup.append(', ')
                    }
                    $newShowGroup.append($newValue);
                }
            }

            // добавляем фильтр, если он не пустой
            if ($list.children('.showGroup').length > 0) {
                $list.append(', ')
            }
            $list.append($newShowGroup);
        }
    }

    // скрытие всего блока если не нужен
    if ($list.children('.showGroup').length > 0) {
        $tabFiltersParams.removeClass('hide')
    } else {
        $tabFiltersParams.addClass('hide')
    }
}

/**
 * Обновление pjax с учетом формы фильтрации
 * @param id id формы
 */
function sendFiltersForm(id) {
    let $form = $(id);
    let url = (new locSearch($form.serialize()))
        .deleteEmptyValues()
        .concat((new locSearch())
            .deleteEmptyValues()
            .deleteKey('sort', false)
            .getSearch()
        )
        .getUrl();
    $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': window.yiiParams['pjaxTimeout']});
}

/**
 * Сброс фильтров
 * @param name имя фильтра, который будет сброшен
 * @param deleteOne true сбросить один, а остальные оставить; false - наоборот
 */
function resetFilters(name = '', deleteOne = true) {
    let url = (new locSearch())
        .deleteEmptyValues()
        .deleteKey(name, deleteOne)
        .getUrl()
    $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': window.yiiParams['pjaxTimeout']});
}

/**
 * Назначение обработчиков событий filter-tabs
 */
function initHandlers() {
    // sendFiltersForm - pjax отправка формы

    $('.catalogTabs')
        .on('keypress',function(e) {
            if(e.which === 13) {
                sendFiltersForm('#filters-form')
            }
        })
        .on('click', '.filter_button', function() {
            sendFiltersForm('#filters-form')
        })
        .on('click', '.checkboxList>span', function() {
            let name = $(this).parent().parent().attr('data-name');
            name = window.filterTabsData.getFieldNameByTabName(name);
            let value = $(this).attr('data-value');
            $('#filters-form input[name=' + name + ']').val(value);
            sendFiltersForm('#filters-form')
        })

    $(document)
        .on("gcs:save_success", function () {
            sendFiltersForm('#filters-form');
        })

    // resetFilters - сброс одного или нескольких фильтров
    $('.catalogTabs .tabsFilterParams')
        .on('click', '.reset-filter', function() {
            resetFilters($(this).attr('data-name'))
        })
        .on('click', '#filters-reset', function() {
            resetFilters('sort', false);
        })
    $('#my-pjax-container')
        .on('click', '.reset_sort', function() {
            resetFilters('sort');
        })

    // восстановление значений в filter-tabs
    $(document)
        .on('pjax:complete', function() {
            window.filterTabsData.update();
            setParamsToFiltersForm();
            setParamsToCheckbox();
            window.filterTabsData.deferred.done(function () {
                setParamsToFiltersItemList();
            });
        })
}

function onSelectAutoComplete(e, ui) {
}