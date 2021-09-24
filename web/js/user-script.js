/**
 * Создает вкладку с заданным id, если ее еще нет
 * @param $tabs jquery объект куда будет добавлена вкладка
 * @param id id вкладки
 */
function createTab($tabs, id) {
    if (Boolean($('#tab' + id).length) === false) {
        let $tab = $('<div class="hide"></div>').attr('id', 'tab' + id);
        let $checkboxList = $('<div class="checkboxList"></div>').appendTo($tab);
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
            method: "GET", // метод HTTP, используемый для запроса
            url: "/device/filter", // строка, содержащая URL адрес, на который отправляется запрос
            data: { // данные, которые будут отправлены на сервер
                term_p1: id,
            },
            success: function (msg) {
                $checkboxList.text('');
                $checkboxList.addClass('success');
                let listFilterName = JSON.parse(msg);
                for (let key in listFilterName) {
                    if (listFilterName.hasOwnProperty(key)) {
                        let $newSpan = $span.clone();
                        $newSpan.attr('data-value', listFilterName[key].id);
                        $newSpan.text(listFilterName[key].value);
                        $newSpan.appendTo($checkboxList);
                    }
                }
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

window.onload = function() {
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
                $('.catalogTabs li>a.current').removeClass('current');
                $('.tabs_content').addClass('hide');
            })
            .on('mouseover', '.checkboxList span:not(.current)', function() {
                let $currentTabsContent = $(this).parent().parent().parent();
                if ($currentTabsContent.attr('id') === 'tabs_content3') {
                    return;
                }
                $(this).siblings().removeClass('current');
                $(this).addClass('current');
                let $blockArrow = $currentTabsContent.children('.block_arrow');
                $blockArrow
                    .removeClass('hide')
                    .offset({
                        'left': $(this).offset().left + $(this).outerWidth() - $blockArrow.outerWidth() - 1,
                        'top': $(this).offset().top + $(this).outerHeight() - 1,
                    });
                $currentTabsContent.children('.tabs_content').addClass('hide');
            })
            .on('mouseover', '.block_arrow', function() {
                let $currentTabsContent = $(this).parent();
                let $nextTabsContent = $(this).siblings('.tabs_content');
                let value = $('#' + $currentTabsContent.attr('id') + '>div>.checkboxList>span.current')[0].dataset.value;
                $nextTabsContent.children('div:not(".hide")').addClass('hide');
                createTab($nextTabsContent, value);
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