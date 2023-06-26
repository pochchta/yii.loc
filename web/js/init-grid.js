$(window).on('load', function() {
    $('.print_button')
        .on('click', function() {
            let url = $(location).attr('origin') + $(this).attr('data-url') + $(location).attr('search');
            $(this).attr('href', url);
        })
    $(document)
        .on('pjax:send', function() {
            loadingWindow.show($('#page-index'));
        })
        .on('pjax:complete', function() {
            loadingWindow.hide($('#page-index'));
        })
        .on("csc:resize", function (event) {  // Увеличение высоты wrap, если .absolute.connected-sortable-columns не влазят
            for (let element of event.detail.elements) {
                let $element = $(element.target);
                let selectorHeight = $element.outerHeight();
                let selectorPosition = $element.offset();
                let selectorTop = selectorPosition.top;

                let $mainWrap = $('#main_wrap');
                let wrapHeight = $mainWrap.outerHeight();

                if (selectorHeight + selectorTop > wrapHeight) {
                    $mainWrap.outerHeight(selectorHeight + selectorTop);
                }
            }
        })
})