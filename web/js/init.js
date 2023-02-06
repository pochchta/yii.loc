const PJAX_TIMEOUT = 5000;

$(window).on('load', function() {
    $(document)
        .on('click', function(event) {              // hide/show by toggleId
            let id = event.target.dataset.toggleId;
            if (!id) return;
            let elem = document.getElementById(id);
            elem.hidden = !elem.hidden;
        })
})