function gettingData (url) {
    let getting;

    return function () {
        if (getting === undefined || getting.state() === 'rejected') {
            return getting = $.get(url)
                .fail(function() {
                        console.error('gettingData: ' + url + ' : ' + getting.state())
                    }
                );
        }
        return getting;
    }
}

function initHideShowByToggleId() {
    $(document)
        .on('click', function(event) {              // hide/show by toggleId
            let id = event.target.dataset.toggleId;
            if (!id) return;
            $('#' + id).toggle();
        })
}

$(window).on('load', function() {
    window.gettingVersion = {};
    window.gettingVersion.word = gettingData('/api/word/get-version');
    window.gettingVersion.device = gettingData('/api/device/get-version');
    window.gettingVersion.verification = gettingData('/api/verification/get-version');

    window.gettingToken = gettingData('/api/user/get-token');
    window.gettingYiiParams = gettingData('/api/app/get-params');
    window.gettingRulesAutoComplete = gettingData('/api/auto-complete/get-rules');
    window.gettingYiiParams().done(function (data) {
        window.yiiParams = data;
    });

    initHideShowByToggleId();
    addAutoCompleteOptions();
})

function addAutoCompleteOptions() {
    window.gettingYiiParams().done(function (params) {
        window.gettingRulesAutoComplete().done(function (rules) {
            let $inputs = $('#active-form input.ui-autocomplete-input, #filters-form input.ui-autocomplete-input');

            for (let input of $inputs) {
                let $input = $(input);
                let source = 'word';
                let fieldName = $input.attr('name');
                let parentName = $input.attr('data-parent');        // # 'device' or 'device_form'

                if (
                    rules.hasOwnProperty(parentName)
                    && rules[parentName].hasOwnProperty(fieldName)
                    && rules[parentName][fieldName].hasOwnProperty('source')
                    && rules[parentName][fieldName]['source'] === 1
                ) {
                    source = parentName.split('_')[0];            // если источник собственный, то обрезаем, например, '_form'
                }

                window.gettingVersion[source]().done(function (version) {

                    $input.autocomplete({
                        source: function (request, response) {
                            $.getJSON('/api/word/get-auto-complete', {
                                name: request.term,
                                field: $input.attr('name'),
                                parent: $input.attr('data-parent'),
                                version: version,
                            }, response);
                        },
                        minLength: params['minSymbolsAutoComplete'],
                        delay: params['delayAutoComplete'],
                        select: function (event, ui) {
                            onSelectAutoComplete(event, ui)
                        },
                        appendTo: ".catalogTabs"
                    });

                })
            }
        })
    })
}