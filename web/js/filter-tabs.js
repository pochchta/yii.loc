$(window).on('load', function() {
    gettingYiiParams().done(function () {
        window.filterTabsData = new dataObj();
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
 * Класс для хранения данных фильтра
 */
class dataObj {
    static suffixes = ['_id', '_start', '_end'];
    static url = '/api/word/get-name';
    data = {};
    deferred = new $.Deferred().resolve();

    constructor() {
        this.create();
        this.update();
    }

    update() {
        this.updateValues();
        this.updateNamesById();
    }

    create() {
        let $titles = $('#filters-form .tabs_title ul a');
        for (let title of $titles) {
            let $title = $(title);
            let name = $title.attr('data-name');
            let label = $title.text();
            this.data[name] = {'label': label};
        }
    }

    updateValues(tabName = '') {
        let arrSearch = (new locSearch())
            .deleteEmptyValues()
            .deleteKey('sort')
            .getArray()

        for (let oneSearch of arrSearch) {
            const [name, value] = oneSearch;

            if (tabName.length > 0 && tabName !== name) continue;

            let isSuffix = false;
            for (let suffix of dataObj.suffixes) {
                if (name.indexOf(suffix) === name.length - suffix.length) {
                    if (suffix === '_id') {
                        if (this.data[name.substr(0, name.length - suffix.length)][suffix] !== value) {
                            delete(this.data[name.substr(0, name.length - suffix.length)]['nameById'])
                        }
                    }
                    this.data[name.substr(0, name.length - suffix.length)][suffix] = value;
                    isSuffix = true;
                    break;
                }
            }
            if (isSuffix) continue;

            this.data[name]['value'] = decodeURI(value);
        }
    }

    updateNamesById() {
        const tabsData = this.getObject();
        $.each(this.data, function (tabName, tab) {
            if (tab.hasOwnProperty('_id') && tab.hasOwnProperty('nameById') === false) {
                let id = tab['_id'];

                let $span = $('#filters-form .tabs_content span[data-value="' + id + '"]');
                if ($span.length) {
                    this.data[tabName]['nameById'] = $span.text();
                } else {
                    this.deferred = this.deferred.then(function () {
                        return $.get(dataObj.url, {'id': id})
                            .done(function(data) {
                                tabsData[tabName]['nameById'] = data['name'];
                            })
                            .fail(function() {
                                console.error('dataObj: ' + dataObj.url + ' : fail' )
                            });
                    })
                }

            }
        }.bind(this))
    }

    /**
     * {'tabName': {'label': 'Название', 'value': 'фильтр_по_тексту', '_id': 'фильтр_по_id'}}
     */
    getObject() {
        return this.data;
    }

    /**
     * [{'tabName': 'tabName', 'label': 'Название', 'tabName': 'фильтр_по_тексту', 'tabName_id': 'фильтр_по_id'}]
     */
    getArray() {
        let array = [];
        $.each(this.data, function (tabName, tab) {
            let newTab = {};
            newTab['tabName'] = tabName;

            $.each(tab, function (field, value) {
                if (dataObj.suffixes.includes(field)) {
                    newTab[tabName + field] = value;
                } else if(field === 'label') {
                    newTab['label'] = tab['label'];
                } else if (field === 'value') {
                    newTab[tabName] = tab['value'];
                }
            });

            array.push(newTab);
        });

        return array;
    }
}

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
 * Установка значений в фильтры формы filters_form
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
 * Установка .checkboxList span.checked
 * @param tabName - tab data-name
 */
function setParamsToCheckbox(tabName = '') {
    let tabsData = window.filterTabsData.getObject();

    if (tabName.length > 0) {  // задан только один тип значения
        let $checkboxList = $('#filters-form .tabs_content>div[data-name="' + tabName + '"]>.checkboxList');
        $checkboxList.children('span.checked').removeClass('checked');

        let value = tabsData[tabName]['_id'];

        let $span = $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span[data-value="' + value + '"]');
        $span.addClass('checked');
    } else {
        $('#filters-form span.checked').removeClass('checked');     // очистка всех выбранных span

        for (let tabName in tabsData) {
            let value = tabsData[tabName]['_id'];

            let $span = $('#filters-form .tabs_content>div[data-name="' + tabName + '"] span[data-value="' + value + '"]');
            $span.addClass('checked');
        }
    }
}

/**
 * Установка списка установленных фильтров из span.checked или input или global filterParams
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
        if (Object.keys(tabsData[tabName]).length > 1) {    // есть что-то кроме названия

            // название фильтра
            let $newShowGroup = $showGroup.clone();
            let $newName = $newShowGroup.children('.first');
            $newName.text(tabsData[tabName]['label'] + ': ');

            let textArray = {
                'value': '🔎 ',
                '_id': '👉 ',
                '_start': 'с ',
                '_end': 'по '
            };

            for (let key in textArray) {
                if (tabsData[tabName].hasOwnProperty(key)) {
                    let $newValue = $value.clone();
                    let $newValueChild = $valueChild.clone();

                    $newValue.text(textArray[key]);         // например '🔎 '
                    let text = tabsData[tabName][key];
                    if (key === '_id') {
                        text = tabsData[tabName]['nameById'];
                    }
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

            // добавляем фильтр
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
    $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': yiiParams['pjaxTimeout']});
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
    $.pjax.reload({container: "#my-pjax-container", url: url, 'timeout': yiiParams['pjaxTimeout']});
}

/**
 * Реализация механизма появления / исчезновения вкладок при наведении / снятии курсора
 */
function initCatalogTabs() {
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
}

/**
 * Назначение обработчиков событий filter-tabs
 */
function initHandlers() {
    // sendFiltersForm - pjax отправка формы

    $('.catalogTabs')
        .on('click', '.filter_button', function() {
            sendFiltersForm('#filters-form')
        })
        .on('click', '.checkboxList>span', function() {
            let name = $(this).parent().parent().attr('data-name');
            let value = $(this).attr('data-value');
            $('#filters-form input[name=' + name + '_id]').val(value);
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

// TODO version to getName
// при клике на используемый фильтр не происходит фильтрация