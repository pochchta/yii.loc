window.onload = function() {
    if ($("div").is(".incoming-index")) {
        const TIME_FOR_FILTER = 3000;
        let filter_selector = "#grid_id-filters input, #grid_id-filters select";
        let filter_text_and_select = "#grid_id-filters :text, #grid_id-filters select";
        let filter_date = "#grid_id-filters input[type=date]";
        let timerFilter = new timerForFilter(TIME_FOR_FILTER, applyFilter);
        let keyCode = 0;
        let enterPressed = false;
        $(document)
            .off("change.yiiGridView keydown.yiiGridView", filter_selector)
            .on('pjax:complete', function() {
                $(document)
                    .off("change.yiiGridView keydown.yiiGridView", filter_selector);
                keyCode = 0;
            })
            .on ("change keydown", filter_text_and_select, function(event) {
                if (event.type === "keydown") {
                    keyCode = event.keyCode;
                    if (keyCode !== 13) {
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
            .on ("change keydown", filter_date, function(event) {
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