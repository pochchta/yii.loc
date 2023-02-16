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
            let elem = document.getElementById(id);
            elem.hidden = !elem.hidden;
        })
}

$(window).on('load', function() {

    window.gettingToken = gettingData('/api/user/get-token');
    window.gettingWordVersion = gettingData('/api/word/get-version');
    window.gettingYiiParams = gettingData('/api/app/get-params');
    window.gettingYiiParams().done(function (data) {
        window.yiiParams = data;
    });

    initHideShowByToggleId();

})