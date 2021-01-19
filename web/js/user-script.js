function pjaxPost(url, timeout) {
    $.pjax.reload({
        container: "#my-pjax-container",
        url: url,
        type: "POST",
        data: $("#form1").serialize(),
        timeout: timeout,
    });
}

function pjaxGet(url, timeout) {
    $.pjax.reload({
        container: "#my-pjax-container",
        url: url,
        type: "POST",
        data: $("#form1").serialize(),
        timeout: timeout,
    });
}
