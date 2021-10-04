const PJAX_TIMEOUT = 5000;

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
        $checkboxList.addClass('download');
        $checkboxList.text('Загрузка');
        let $span = $('<span class="checkbox filter-checkbox"></span>');
        $.ajax({
            method: "GET",
            url: "/device/filter",
            data: {
                parent_id: id,
            },
            success: function (msg) {
                $checkboxList.text('');
                $checkboxList.addClass('success');
                let listFilterName = JSON.parse(msg);
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
    $.pjax.reload({container: "#my-pjax-container", url: url + '?' + msg, 'timeout': PJAX_TIMEOUT});
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

        let $input = $('#filters-form input.hide[name=' + tabName + ']');
        let name = $input.attr('name');
        let value = $input.val();
        let $span = $('#filters-form .tabs_content>div[data-name="' + name + '"] span[data-value="' + value + '"]');
        $span.addClass('checked');
    } else {
        $('#filters-form span.checked').removeClass('checked');     // очистка всех выбранных span

        let $inputs = $('#filters-form input.hide');

        for(let input of $inputs) {
            let $input = ($(input));
            let name = $input.attr('name');
            let value = $input.val();
            let $span = $('#filters-form .tabs_content>div[data-name="' + name + '"] span[data-value="' + value + '"]');
            $span.addClass('checked');
        }
    }
}

/**
 * Установка списка фильтров из span.checked и input
 */
function setParamsToFiltersItemList() {
    let $tabFiltersParams = $('.tabsFilterParams');
    let $list = $tabFiltersParams.find('#filters-active');
    $list.text('');  // очистка списка фильтров
    $list.append('<span class="showOnly">Выводятся только:</span>');

    let $name = $('<span class="first"></span>');
    let $value = $('<span class="second"><a class="reset-filter" title="Отменить фильтр"></a></span>');
    let $showGroup = $('<span class="showGroup"></span>');
    $showGroup.append($name).append($value);

    let $checkedSpans = $('#filters-form span.checked');    // выбранные пункты меню
    for (let span of $checkedSpans) {
        let $span = $(span);
        if ($span.attr('data-value') === '') {              // значение по умолчанию не выводится
            continue;
        }
        let $tab = $span.parent().parent();
        let tabName = $tab.attr('data-name');
        let tabLabel = $('#tabs a[data-name=' + tabName + ']').text();   // название вкладки на русском языке

        let $newShowGroup = $showGroup.clone();
        let $newName = $newShowGroup.children('.first');
        $newName.text(tabLabel + ': ');

        let $newValue = $newShowGroup.children('.second');
        let $newValueChild = $newValue.children().first();
        $newValueChild.text($span.text().toLowerCase());
        $newValueChild.attr('data-name', tabName);

        if ($list.children('.showGroup').length > 0) {
            $list.append(', ')
        }
        $list.append($newShowGroup);
    }

    let $inputs = $('#filters-form input:not(.hide)[type="text"], #filters-form input:not(.hide)[type="date"]');      // поля для ручного ввода
    for (let input of $inputs) {
        let $input = $(input);
        if ($input.val() === '') {
            continue;
        }

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
        let tabName = $tab.attr('data-name');
        let tabLabel = $('#tabs a[data-name=' + tabName + ']').text() + addLabel;   // название вкладки на русском языке + суффикс (начало или конец)

        let $newShowGroup = $showGroup.clone();
        let $newName = $newShowGroup.children('.first');
        $newName.text(tabLabel + ': ');

        let $newValue = $newShowGroup.children('.second');
        let $newValueChild = $newValue.children().first();
        $newValueChild.text($input.val());
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
        $.pjax.reload({container: "#my-pjax-container", url: url + '?' + msg, 'timeout': PJAX_TIMEOUT});
    } else {
        $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': PJAX_TIMEOUT});
    }
}

/**
 * Замена url кнопки .print_button
 */
function setUrlForPrint() {
    let $button = $('.print_button');
    let url = $(location).attr('origin') + $button.attr('data-url') + $(location).attr('search');
    $button.attr('href', url);
}

window.onload = function() {
    setParamsToFiltersForm();
    setParamsToCheckbox();
    setUrlForPrint();
    setParamsToFiltersItemList();

    (function($) {
        $(document)
        .on('pjax:send', function() {
            $('#pjax-loading').removeClass('hide');
        })
        .on('pjax:complete', function() {
            setParamsToFiltersForm();
            setParamsToCheckbox();
            setUrlForPrint();
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
                resetFilters()
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
                $('#filters-form input[name=' + name + ']').val(value);
                sendFiltersForm('#filters-form')
            })
    })(jQuery);

    if ($("div").is("#grid_id")) {
        const TIME_FOR_FILTER = 3000;

        let filter_selector = "#grid_id-filters input, #grid_id-filters select";
        let filter_text_and_select = "#grid_id-filters :text, #grid_id-filters select";
        let filter_date = "#grid_id-filters input[type=date]";
        let timerFilter = new timerForFilter(TIME_FOR_FILTER, applyFilter);
        let keyCode = 0;
        let enterPressed = false;
        $(document)
            .off("change.yiiGridView keydown.yiiGridView", filter_selector)
            .on("pjax:complete", function() {
                $(document)
                    .off("change.yiiGridView keydown.yiiGridView", filter_selector);
                keyCode = 0;
                enterPressed = false;
            })
            .on("change keydown", filter_text_and_select, function(event, valueChanged) {
                if (event.type === "keydown" || valueChanged === true) {
                    keyCode = event.keyCode;
                    if (keyCode !== 13 && valueChanged !== true) {
                        return;
                    } else {
                        enterPressed = true;
                    }
                } else {
                    if (enterPressed) {
                        enterPressed = false;
                        return;
                    }
                }
                applyFilter();
            })
            .on("change keydown", filter_date, function(event) {
                if (event.type === "keydown") {
                    keyCode = event.keyCode;
                    if (keyCode === 13) {
                        if (timerFilter.stop(true) === false) {
                            applyFilter();
                        }
                    }
                } else {
                    if (keyCode === 0 || keyCode === 13) {
                        applyFilter();
                    } else {
                        timerFilter.stop();
                        if (this.value !== "") {
                            timerFilter.start();
                        }
                    }
                }
            })
    }
}

class timerForFilter
{
    time = 1000;
    func;
    timerId;
    flagComplete = true;

    constructor(time, func)
    {
        this.time = time;
        this.func = func;
    }

    // return bool был ли таймер запущен
    start()
    {
        if (this.flagComplete) {
            this.flagComplete = false;
            this.timerId = setTimeout(this.process.bind(this), this.time);
            return true;
        }
        return false;
    }

    // return bool был ли таймер остановлен
    stop(forceCall = false)
    {
        if (this.flagComplete === false) {
            clearTimeout(this.timerId);
            if (forceCall === true) {
                this.func();
            }
            this.flagComplete = true;
            return true;
        }
        return false;
    }

    process()
    {
        this.func();
        this.flagComplete = true;
    }
}

function applyFilter()
{
    $("#grid_id").yiiGridView("applyFilter");
}

function selectAutoComplete(event, ui, attribute) {
    if (event.keyCode === 13) {     // значение из autoComplete будет вставлено в input и отправлено само
        return;
    }
    let str = $("#grid_id").yiiGridView("data").settings.filterUrl;
    let searchParams = new URLSearchParams(str.substring(str.indexOf("?") + 1));
    let oldValueFilter = searchParams.get(attribute);
    let filterSelector = $('#' + attribute);
    if (oldValueFilter !== ui.item.label) {
        let valueChanged = false;
        if (oldValueFilter !== filterSelector.val()) {
            valueChanged = true;    // значение изменилось, значит будет сгенерировано еще одно событие "change"
        }                           // флаг предотвратит повторную отправку фильтра
        filterSelector.val(ui.item.label).trigger('change', valueChanged);
    }
}