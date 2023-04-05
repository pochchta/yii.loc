/**
 * Класс для хранения данных фильтра
 */
class dataObj {
    statusNotDeleted = '0';
    suffixes = ['_id', '_start', '_end'];
    url = '/api/word/get-name';
    data = {};
    deferred;
    wordVersion;

    constructor() {
        this.create();
    }

    update() {
        this.updateValues();
        this.updateNamesById();
        if (this.deferred === undefined) {
            this.deferred = new $.Deferred().resolve();
        }
    }

    /**
     * Создаем заготовку объекта: есть только название и метка
     */
    create() {
        let $titles = $('#filters-form .tabs_title ul a');
        for (let title of $titles) {
            let $title = $(title);
            let name = $title.attr('data-name');
            let label = $title.text();
            this.data[name] = {'label': label};
        }
    }

    /**
     * Обновление данных полей
     * @param chosenName
     */
    updateValues(chosenName = '') {
        const self = this;

        let arrSearch = (new locSearch())
            .deleteEmptyValues()
            .deleteKey('sort')
            .getArray()
        let objectSearch = Object.fromEntries(arrSearch);
        if (objectSearch.hasOwnProperty('deleted') === false) {
            objectSearch['deleted'] = this.statusNotDeleted;
        }

        for (let tabName in this.data) {
            if (chosenName.length > 0 && chosenName !== tabName) continue;     // если выбрано 1 поле

            for (let suffix of self.suffixes.concat([''])) {
                let fieldName = suffix;
                if (suffix === '') {
                    fieldName = 'value';
                }

                if (objectSearch.hasOwnProperty(tabName + suffix)) {
                    this.setField(tabName, fieldName, decodeURI(objectSearch[tabName + suffix]));
                } else {
                    this.deleteField(tabName, fieldName);
                }

            }
        }
    }

    /**
     * Обновление названий по id (из данных страницы или через ajax)
     */
    updateNamesById() {
        const self = this;
        $.each(self.getObject(), function (tabName, tab) {
            if (tabName === 'deleted' && tab.hasOwnProperty('nameById') === false) {
                let id = tab['value'];
                let $span = $('#filters-form .tabs_content span[data-value="' + id + '"][data-source="' + tabName + '"]');
                if ($span.length) {
                    self.setField(tabName, 'nameById', $span.text());
                } else {
                    self.setField(tabName, 'nameById', 'не найдено');
                }
            } else if (tab.hasOwnProperty('_id') && tab.hasOwnProperty('nameById') === false) {    // только если еще не обновлено
                let id = tab['_id'];
                let $span = $('#filters-form .tabs_content span[data-value="' + id + '"]:not([data-source])');
                if ($span.length) {
                    self.setField(tabName, 'nameById', $span.text());
                } else {
                    if (self.deferred === undefined) {
                        self.deferred = window.gettingWordVersion().done(function (version) {
                            self.wordVersion = version;
                        });
                    }
                    self.deferred = self.deferred.then(function () {
                        return $.get(self.url, {
                            'id': id,
                            'version': self.wordVersion
                        })
                            .done(function(data) {
                                self.setField(tabName, 'nameById', data['name']);
                            })
                            .fail(function() {
                                console.error('dataObj: ' + self.url + ' : fail' )
                            });
                    })
                }

            }
        })
    }

    /**
     * Установка значения с условием (измененное поле _id сбрасывает nameById)
     * @param tabName
     * @param fieldName
     * @param value
     */
    setField(tabName, fieldName, value) {
        if (
            (tabName === 'deleted' && fieldName === 'value')
            || (fieldName === '_id')
        ) {
            delete(this.data[tabName]['nameById']);
        }
        this.data[tabName][fieldName] = value;
    }

    /**
     * Удаление значения с условием (измененное поле _id сбрасывает nameById)
     * @param tabName
     * @param fieldName
     */
    deleteField(tabName, fieldName) {
        if (tabName === 'deleted' && fieldName === 'value') {
            this.setField(tabName, fieldName, this.statusNotDeleted);
            return;
        }

        if (fieldName === '_id') {
            delete(this.data[tabName]['nameById']);
        }
        delete(this.data[tabName][fieldName]);
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
        const self = this;
        let array = [];
        $.each(this.data, function (tabName, tab) {
            let newTab = {};
            newTab['tabName'] = tabName;

            $.each(tab, function (field, value) {
                if (self.suffixes.includes(field)) {
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

    /** Получение имени для скрытого поля, в которое вставляется значение из кликнутого span
     * 'name' => 'name_id'; 'deleted' => 'deleted'
     */
    getFieldNameByTabName(tabName) {
        if (tabName === 'deleted') {
            return tabName;
        }
        return tabName + '_id';

    }

    /** Получение значения для скрытого поля
     * 'name' => tab['_id'], 'deleted' => tab['value']
     */
    getValueByTabName(tabName) {
        if (tabName === 'deleted') {
            return this.data[tabName]['value'];
        }
        return this.data[tabName]['_id'];
    }

    /** Получение подписи, например, для отображения выбранного фильтра
     * 'name':'id' => tab['nameById'], 'deleted':'value' => tab['nameById'], 'number':'value' => tab['value']
     */
    getLabelByTabName(tabName, fieldName) {
        if (
            (tabName === 'deleted' && fieldName === 'value')
            || (fieldName === '_id')
        ) {
            return this.data[tabName]['nameById'];
        }
        return this.data[tabName][fieldName];
    }

    /** Нужно ли вставлять подпись в список фильтров
     * @returns {boolean}
     */
    checkIfNameNeedsToAdd(tabName) {
        if (tabName === 'deleted') {
            return this.data[tabName]['value'] !== this.statusNotDeleted;
        }
        return Object.keys(this.data[tabName]).length > 1;
    }
}

/**
 * Реализация механизма появления / исчезновения вкладок при наведении / снятии курсора
 */
function initCatalogTabs() {
    const SMALL_TIMEOUT = 250;
    const LARGE_TIMEOUT = 1000;
    let timerArray = [];
    $('.catalogTabs')
        .on('mouseover', 'li>a:not(.current)', function() {                 // показ вкладки 1 уровня
            let id = 'name';
            if (timerArray[id] === undefined) {
                timerArray[id] = setTimeout(function() {
                    let value = $(this).attr('data-value');
                    $('.catalogTabs li>a.current').removeClass('current');
                    $(this).addClass('current');
                    $('#tabs_content1>div:not(".hide")').addClass('hide');
                    if (Number.isInteger(Number.parseInt(value))) {
                        loadDataToTab(value);
                    }
                    $('#tabs_content1>#tab' + value).removeClass('hide');
                    $('#tabs_content1').removeClass('hide');

                    timerArray[id] = undefined;
                }.bind(this), SMALL_TIMEOUT);
            }

        })
        .on('mouseleave', 'li>a:not(.current)', function() {                 // отмена "показ вкладки 1 уровня"
            let id = 'name';
            if (timerArray[id]) {
                clearTimeout(timerArray[id]);
                timerArray[id] = undefined;
            }

        })

        .on('mouseleave', function() {                                      // скрытие всего catalogTabs
            let id = 'main';
            if (timerArray[id] === undefined) {
                timerArray[id] = setTimeout(function() {
                    $('.catalogTabs li>a.current').removeClass('current');
                    $('.tabs_content').addClass('hide');

                    timerArray[id] = undefined;
                }.bind(this), LARGE_TIMEOUT);
            }
        })
        .on('mouseover', function() {                                       // отмена "скрытие всего catalogTabs"
            let id = 'main';

            if (timerArray[id]) {
                clearTimeout(timerArray[id]);
                timerArray[id] = undefined;
            }
        })

        .on('mouseover', '.checkboxList span:not(.current)', function() {   // добавление стрелки для выбранного пункта span
            let id = 'span';
            if (timerArray[id] === undefined) {
                timerArray[id] = setTimeout(function() {
                    let $currentTabsContent = $(this).parent().parent().parent();
                    if ($currentTabsContent.children('.tabs_content').length === 0) {   // достигнута ли максимальная вложенность
                        return;
                    }
                    $(this).siblings().removeClass('current');
                    $(this).addClass('current');
                    if ($(this).attr('data-source') === undefined) {
                        let $blockArrow = $currentTabsContent.children('.block_arrow');
                        $blockArrow
                            .removeClass('hide')
                            .offset({
                                'left': $(this).offset().left + $(this).outerWidth() - $blockArrow.outerWidth() - 1,
                                'top': $(this).offset().top + $(this).outerHeight() - 1,
                            });
                    }
                    $currentTabsContent.children('.tabs_content').addClass('hide');

                    timerArray[id] = undefined;
                }.bind(this), SMALL_TIMEOUT);
            }
        })
        .on('mouseleave', '.checkboxList span:not(.current)', function() {   // отмена "добавление стрелки для выбранного пункта span"
            let id = 'span';

            if (timerArray[id]) {
                clearTimeout(timerArray[id]);
                timerArray[id] = undefined;
            }
        })

        .on('mouseover', '.block_arrow', function() {                       // показ вложенной вкладки
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

        .on('mouseleave', '.tabs_content', function() {                     // сокрытие вложенной вкладки
            let id = 'subTab_' + this.id;
            if (timerArray[id] === undefined) {
                timerArray[id] = setTimeout(function() {
                    $(this).children('.block_arrow').addClass('hide');
                    $('#' + $(this).attr('id') + '>div>.checkboxList>span.current').removeClass('current');
                    $(this).children('.tabs_content').addClass('hide');

                    timerArray[id] = undefined;
                }.bind(this), LARGE_TIMEOUT);
            }
        })
        .on('mouseover', '.tabs_content', function() {                     // отмена "сокрытие вложенной вкладки"
            let id = 'subTab_' + this.id;
            if (timerArray[id]) {
                clearTimeout(timerArray[id]);
                timerArray[id] = undefined;
            }
        })
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
    let $checkboxList = $tab.children('.checkboxList').first();
    if ($checkboxList.hasClass('download') === false && $checkboxList.hasClass('success') === false) {
        $checkboxList.addClass('download');
        $checkboxList.text('Загрузка');
        window.gettingWordVersion().done(function (version) {
            let $span = $('<span class="checkbox filter-checkbox"></span>');
            $.ajax({
                cache: true,
                method: "GET",
                url: "/api/word/get-children",
                data: {
                    'parent_id': id,
                    'version': version,
                },
                success: function (listFilterName) {
                    $checkboxList.addClass('success');
                    $checkboxList.text('');
                    if (listFilterName.length ===0) {
                        $checkboxList.text('Нет вложенных элементов');
                    }
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