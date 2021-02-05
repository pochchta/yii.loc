window.onload = function() {
    if ($("div").is(".incoming-index")) {
        let filter_selector = "#grid_id-filters input, #grid_id-filters select";
        let timerFilter = new timerForFilter(3000, applyFilter);
        let keyCode = 0;
        $(document)
            .off("change.yiiGridView keydown.yiiGridView", filter_selector)
            .on('pjax:complete', function() {
                $(document)
                    .off("change.yiiGridView keydown.yiiGridView", filter_selector)
            })
            .on ("keydown", filter_selector, function(event) {
                let typeName = $(this).attr("type");
                keyCode = event.keyCode;
                if (keyCode === 13) {
                    if (typeName === "date") {
                        timerFilter.stop(true);
                    }
                    keyCode = 0;
                    $(this).blur();
                }
            })
            .on ("change", filter_selector, function() {
                let typeName = $(this).attr("type");
                if (typeName === "date") {
                    if (keyCode === 0) {
                        applyFilter();
                    } else {
                        timerFilter.stop().start();
                    }
                    keyCode = 0;
                } else {
                    applyFilter();
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

    start()
    {
        this.flagComplete = false;
        this.timerId = setTimeout(this.process.bind(this), this.time);
        return this;
    }

    stop(forceCall = false)
    {
        if (this.flagComplete === false) {
            clearTimeout(this.timerId);
            if (forceCall === true) {
                this.func();
            }
            this.flagComplete = true;
        }
        return this;
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