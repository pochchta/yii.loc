/**
 * Асинхронная вставка flash message после .container .breadcrumb
 * @param type success, danger
 * @param text
 */
function showMessage(text = 'Success', type = 'success') {
    if (type !== 'success') {
        type = 'danger';
    }
    $(
        '<div class="alert-' + type + ' alert fade in">' +
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
        text +
        '</div>'
    ).insertAfter('body>.wrap>.container>.breadcrumb')
}