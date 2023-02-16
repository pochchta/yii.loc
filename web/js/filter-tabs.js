/**
 * Создает вкладку с заданным id, если ее еще нет
 * @param $tabs jquery объект куда будет добавлена вкладка
 * @param id id вкладки
 * @param name название поля
 */
function createTab($tabs, id, name) {
    if (Boolean($('#tab' + id).length) === false) {
        let $tab = $('<div class="hide"></div>').attr('id', 'tab' + id).attr('data-name', name);
        $('<div class="checkboxList"></div>').appendTo($tab);
        $tab.appendTo($tabs);
    }
}

/**
 * Загрузка и вставка пунктов меню во вкладку, если они еще не были загружены
 * tab > checkboxList.download
 * @param id - id вкладки
 */
function loadDataToTab(id) {
    let $tab = $('#tab' + id);
    let $checkboxList = $tab.children().first();
    if ($checkboxList.hasClass('download') === false && $checkboxList.hasClass('success') === false) {
        gettingWordVersion().done(function (version) {
            $checkboxList.addClass('download');
            $checkboxList.text('Загрузка');
            let $span = $('<span class="checkbox filter-checkbox"></span>');
            $.ajax({
                cache: true,
                method: "GET",
                url: "/api/word/get-children",
                data: {
                    parent_id: id,
                    version: version,
                },
                success: function (msg) {
                    $checkboxList.text('');
                    $checkboxList.addClass('success');
                    let listFilterName = (msg);
                    for (let key in listFilterName) {
                        if (listFilterName.hasOwnProperty(key)) {
                            let $newSpan = $span.clone();
                            $newSpan.attr('data-value', listFilterName[key].id);
                            $newSpan.text(listFilterName[key].name);
                            $newSpan.appendTo($checkboxList);
                        }
                    }
                    setParamsToCheckbox($tab.attr('data-name'));
                },
                complete: function () {
                    $checkboxList.removeClass('download');
                    if ($checkboxList.hasClass('success') === false) {
                        $checkboxList.text('Ошибка');
                    }
                }
            });
        })
    }
}

/**
 * Обновление pjax с учетом формы фильтрации
 * @param id id формы
 */
function sendFiltersForm(id) {
    let $form = $(id);
    let msg = $form.serialize();
    let url = $(location).attr('pathname');
    $.pjax.reload({container: "#my-pjax-container", url: url + '?' + msg, 'timeout': yiiParams['pjaxTimeout']});
}

/**
 * Установка значений в фильтры формы filters_form
 */
function setParamsToFiltersForm() {
    $('#filters-form input').val('');       // очистка всех установленных значений

    let params = new URLSearchParams($(location).attr('search'));
    let entries = params.entries();
    for(let entry of entries) {
        const [name, value] = entry;
        $('#filters-form input[name=' + name + ']').val(value);
    }
}

/**
 * Установка .checkboxList span.checked
 * Запускается после setParamsToFiltersForm, т.к. используются значения из input
 * @param tabName - tab data-name
 */
function setParamsToCheckbox(tabName = '') {
    if (tabName.length > 0) {  // задан только один тип значения
        let $checkboxList = $('#filters-form .tabs_content>div[data-name="' + tabName + '"]>.checkboxList');
        $checkboxList.children('span.checked').removeClass('checked');

        let $input = $('#filters-form input.hide[name=' + tabName + '_id]');
        let name = $input.attr('name');
        name = name.substr(0, name.length - 3);     // обрезка '_id', т.к. input.hide

        let value = $input.val();
        let $span = $('#filters-form .tabs_content>div[data-name="' + name + '"] span[data-value="' + value + '"]');
        $span.addClass('checked');
    } else {
        $('#filters-form span.checked').removeClass('checked');     // очистка всех выбранных span

        let $inputs = $('#filters-form input.hide');

        for(let input of $inputs) {
            let $input = ($(input));
            let name = $input.attr('name');
            name = name.substr(0, name.length - 3);     // обрезка '_id', т.к. input.hide
            let value = $input.val();
            let $span = $('#filters-form .tabs_content>div[data-name="' + name + '"] span[data-value="' + value + '"]');
            $span.addClass('checked');
        }
    }
}

/**
 * Установка списка установленных фильтров из span.checked или input или global filterParams
 */
function setParamsToFiltersItemList() {
    // очистка списка фильтров "Выводятся только: 1: 1,2; 2: 1"
    let $tabFiltersParams = $('.tabsFilterParams');
    let $list = $tabFiltersParams.find('#filters-active');
    $list.text('');
    $list.append('<span class="showOnly">Выводятся только:</span>');

    // создается заготовка под первую группу списка фильтров
    let $name = $('<span class="first"></span>');
    let $value = $('<span class="second"><a class="reset-filter" title="Отменить фильтр"></a></span>');
    let $showGroup = $('<span class="showGroup"></span>');
    $showGroup.append($name).append($value);

    // обработка inputs
    let $inputs = $('#filters-form input[type="text"], #filters-form input:not(.hide)[type="date"]');      // поля для ручного ввода
    for (let input of $inputs) {
        let $input = $(input);
        if ($input.val() === '') {
            continue;
        }

        // input date
        let addLabel = '';
        if ($input.attr('type') === 'date') {
            let name = $input.attr('name');
            let arr = {
                '_start': '(начало)',
                '_end': '(конец)'
            }
            for (let key in arr) {
                let index = name.indexOf(key);
                if (index !== -1 && index + key.length === name.length) {   // суффикс '_start' (_end) есть в конце
                    addLabel = arr[key];
                    break;
                }
            }
        }

        let $tab = $input.parent();
        if ($tab.hasClass('checkboxList')) {
            $tab = $tab.parent();
        }
        let tabName = $tab.attr('data-name');
        let tabLabel = $('#tabs a[data-name=' + tabName + ']').text() + addLabel;   // название вкладки на русском языке + суффикс (начало или конец)

        // название фильтра
        let $newShowGroup = $showGroup.clone();
        let $newName = $newShowGroup.children('.first');
        $newName.text(tabLabel + ': ');

        // значение фильтра
        let $newValue = $newShowGroup.children('.second');
        let $newValueChild = $newValue.children().first();
        let value = $input.val();                               // value записано в input вручную
        if ($input.hasClass('hide')) {
            let $span = $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span[data-value="' + value + '"]');
            if ($span.length > 0) {
                value = $span.text();                           // value выставляем из span.checked
            } else if(filterParams.hasOwnProperty(value) && filterParams[value].name === tabName + '_id') {
                value = filterParams[value].label;              // value из глобальной переменной filterParams (список примененых фильтров)
            }
        }
        $newValueChild.text(value);
        $newValueChild.attr('data-name', $input.attr('name'));

        if ($list.children('.showGroup').length > 0) {
            $list.append(', ')
        }
        $list.append($newShowGroup);
    }

    if ($list.children('.showGroup').length > 0) {
        $tabFiltersParams.removeClass('hide')
    } else {
        $tabFiltersParams.addClass('hide')
    }
}

/**
 * Сброс фильтров
 * @param name имя конкретного фильтра
 */
function resetFilters(name = '') {
    let url = $(location).attr('pathname');
    if (name.length > 0) {
        let $form = $('#filters-form');
        let $input = $form.find('input[name='+ name + ']');
        $input.val('');
        let msg = $form.serialize();
        $.pjax.reload({container: "#my-pjax-container", url: url + '?' + msg, 'timeout': yiiParams['pjaxTimeout']});
    } else {
        $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': yiiParams['pjaxTimeout']});
    }
}

$(window).on('load', function() {
    gettingYiiParams().done(function () {
        setParamsToFiltersForm();
        setParamsToCheckbox();
        setParamsToFiltersItemList();

        (function($) {
            $('.print_button')
                .on('click', function() {
                    let url = $(location).attr('origin') + $(this).attr('data-url') + $(location).attr('search');
                    $(this).attr('href', url);
                })
            $('#my-pjax-container')
                .on('click', '.reset_sort', function() {
                    resetFilters('sort');
                })
        })(jQuery);

        (function($) {
            $(document)
                .on('pjax:send', function() {
                    $('#pjax-loading').removeClass('hide');
                })
                .on('pjax:complete', function() {
                    setParamsToFiltersForm();
                    setParamsToCheckbox();
                    setParamsToFiltersItemList();
                    $('#pjax-loading').addClass('hide')
                })
        })(jQuery);

        (function($) {
            $('.tabsFilterParams')
                .on('click', '.reset-filter', function() {
                    resetFilters($(this).attr('data-name'))
                })
                .on('click', '#filters-reset', function() {
                    resetFilters();
                })
        })(jQuery);

        (function($) {
            $('.catalogTabs')
                .on('mouseover', 'li>a:not(.current)', function() {
                    $('.catalogTabs li>a.current').removeClass('current');
                    $(this).addClass('current');
                    $('#tabs_content1>div:not(".hide")').addClass('hide');
                    $('#tabs_content1>#' + ($(this).attr('data-value'))).removeClass('hide');
                    $('#tabs_content1').removeClass('hide');
                })
                .on('mouseleave', function() {
                    // $('.catalogTabs li>a.current').removeClass('current');
                    // $('.tabs_content').addClass('hide');
                })
                .on('mouseover', '.checkboxList span:not(.current)', function() {
                    let $currentTabsContent = $(this).parent().parent().parent();
                    if ($currentTabsContent.attr('id') === 'tabs_content3') {
                        return;
                    }
                    $(this).siblings().removeClass('current');
                    $(this).addClass('current');
                    if ($(this).attr('data-child') !== '0') {
                        let $blockArrow = $currentTabsContent.children('.block_arrow');
                        $blockArrow
                            .removeClass('hide')
                            .offset({
                                'left': $(this).offset().left + $(this).outerWidth() - $blockArrow.outerWidth() - 1,
                                'top': $(this).offset().top + $(this).outerHeight() - 1,
                            });
                    }
                    $currentTabsContent.children('.tabs_content').addClass('hide');
                })
                .on('mouseover', '.block_arrow', function() {
                    let $currentTabsContent = $(this).parent();
                    let $nextTabsContent = $(this).siblings('.tabs_content');
                    let $currentSpan = $('#' + $currentTabsContent.attr('id') + '>div>.checkboxList>span.current');
                    let $currentTab = $currentSpan.parent().parent();
                    let value = $currentSpan.attr('data-value');
                    let name = $currentTab.attr('data-name');
                    $nextTabsContent.children('div:not(".hide")').addClass('hide');
                    createTab($nextTabsContent, value, name);
                    loadDataToTab(value);
                    $nextTabsContent.children('#tab' + (value)).removeClass('hide');
                    $nextTabsContent
                        .removeClass('hide')
                        .offset({
                            'left': $currentTabsContent.offset().left,
                            'top': $(this).offset().top + $(this).outerHeight(),
                        })
                        .width($currentTabsContent.width() * 1.01);
                })
                .on('mouseleave', '.tabs_content', function() {
                    $(this).children('.block_arrow').addClass('hide');
                    $('#' + $(this).attr('id') + '>div>.checkboxList>span.current').removeClass('current');
                    $(this).children('.tabs_content').addClass('hide');
                })

                .on('click', '.filter_button', function() {
                    sendFiltersForm('#filters-form')
                })
                .on('click', '.checkboxList>span', function() {
                    let name = $(this).parent().parent().attr('data-name');
                    let value = $(this).attr('data-value');
                    $('#filters-form input[name=' + name + '_id]').val(value);
                    sendFiltersForm('#filters-form')
                })
        })(jQuery);

        document.addEventListener("gcs:save_success", function() {
            sendFiltersForm('#filters-form');
        });
    })
})