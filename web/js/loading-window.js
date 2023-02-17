class loadingWindow {
    /**
     * Создание и отображение окна "загрузка" поверх переданного элемента jquery
     * @param $selector jquery
     */
    static show($selector) {
        let loading_window_id = $selector.attr('id') + '_loading_window';
        let $window = $('#' + loading_window_id);
        if (! $window.length) {
            let zIndex = 999;
            $window = $('<div>Загрузка</div>');
            $window.css('position', 'absolute');
            $window.css('zIndex', zIndex);
            $window.css('background', '#eeeeeeb8');
            $window.attr('id', loading_window_id);

            let selectorWidth = $selector.width();
            $window.css('font-size', (selectorWidth / 15) + 'px');
            $window.css('padding', (selectorWidth / 15) + 'px');

        }
        let pos = $selector.position();
        $window.css('top', pos.top + 'px');
        $window.css('left', pos.left + 'px');

        $window.css('width', $selector.css('width'));
        $window.css('height', $selector.css('height'));
        $window.css('display', 'block');

        $window.insertAfter($selector);
    }

    /**
     * Сокрытие окна "загрузка" над переданным элементом
     * @param $selector jquery
     */
    static hide($selector) {
        let loading_window_id = $selector.attr('id') + '_loading_window';
        let $window = $('#' + loading_window_id);
        if ($window.length) {
            $window.css('display', 'none');
        }
    }
}