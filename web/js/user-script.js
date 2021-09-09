window.onload = function() {
    (function($) {
        $(function() {

/*            $('.tabs_title ul').on('click', 'a:not(.current)', function() {
                $('.rfalse')
                    .removeClass('current');
                $(this)
                    .addClass('current').siblings()
                    .closest('div.tabs').find('div.tabs__content').removeClass('current').eq($(this).index()).addClass('current');
            });*/
            $('.catalogTabs')
                .on('mouseover', 'li>a:not(.current)', function() {
                    $('.catalogTabs li>a.current').removeClass('current');
                    $(this).addClass('current');
                    $('#tabs_content1>div:not(".hide")').addClass('hide');
                    $('#tabs_content1>' + ($(this).attr('href'))).removeClass('hide');
                    $('#tabs_content1').removeClass('hide');
                })
                .on('mouseleave', function() {
                    $('.catalogTabs li>a.current').removeClass('current');
                    $('#tabs_content1').addClass('hide');
                    $('#tabs_content2').addClass('hide');
                })
                .on('mouseover', '#tabs_content1 .checkboxList>span:not(.current)', function() {
                    $('#tabs_content1 .checkboxList>span.current').removeClass('current');
                    $(this).addClass('current');
                    $('#tabs_content2>div:not(".hide")').addClass('hide');
                    $('#tabs_content2>#tab' + ($(this).children('input')[0].value)).removeClass('hide');
                    $('#tabs_content2')
                        .removeClass('hide')
                        .offset({'top': $(this).offset().top + $(this).height()})
                        .width($('#tabs_content1').width() * 1.02);
                })
                .on('mouseleave', '#tabs_content1', function() {
                    $('#tabs_content2').addClass('hide');
                })
        });
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