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
})