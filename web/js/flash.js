class flash {
    /**
     * Асинхронная вставка flash message в .container
     * @param type success, danger
     * @param text
     */
    static add(text = 'Success', type = 'success') {
        if (type !== 'success') {
            type = 'danger';
        }
        let $container = $('body>.wrap>.container');
        let $message = $(
            '<div class="alert-' + type + ' alert fade in">' +
            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
            text +
            '</div>'
        );
        let $lastAlert = $container.children('.alert').last();
        let $breadcrumb = $container.children('.breadcrumb').last();

        if ($lastAlert.length) {
            $message.insertAfter($lastAlert);
        } else if ($breadcrumb.length) {
            $message.insertAfter($breadcrumb);
        } else {
            $container.prepend($message);
        }
    }
}
