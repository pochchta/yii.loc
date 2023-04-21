class loadingWindow {
    /**
     * Создание и отображение окна "загрузка" поверх переданного элемента jquery
     * @param $selector jquery
     */
    static show($selector) {
        let selectorWidth = $selector.outerWidth();
        let selectorHeight = $selector.outerHeight();
        let selectorPosition = $selector.offset();
        let selectorLeft = selectorPosition.left;
        let selectorTop = selectorPosition.top;

        let $absChildren = $selector.find('.absolute:not(.hide)');  // с учетом вложенных .absolute
        for (let child of $absChildren) {
            let $child = $(child);

            // получаем позицию и размеры дочернего элемента
            let childPosition = $child.offset();
            let childWidth = $child.outerWidth();
            let childHeight = $child.outerHeight();
            
            // проверяем, перекрывает ли дочерний элемент родительский
            if (childPosition.left < selectorLeft) {
                selectorLeft = childPosition.left;
            }
            if (childPosition.top < selectorTop) {
                selectorTop = childPosition.top;
            }
            if (childPosition.left + childWidth > selectorLeft + selectorWidth) {
                selectorWidth = childPosition.left + childWidth - selectorLeft;
            }
            if (childPosition.top + childHeight > selectorTop + selectorHeight) {
                selectorHeight = childHeight + childPosition.top - selectorTop;
            }
        }

        let loading_window_id = $selector.attr('id') + '_loading_window';
        let $window = $('#' + loading_window_id);
        if (! $window.length) {
            let zIndex = 999;
            $window = $('<div>Загрузка</div>');
            $window.css('display', 'none');
            $window.css('position', 'absolute');
            $window.css('zIndex', zIndex);
            $window.css('background', '#eeeeeeb8');
            $window.attr('id', loading_window_id);

            $window.css('font-size', (selectorWidth / 15) + 'px');
            $window.css('padding', (selectorWidth / 15) + 'px');

            $window.insertAfter($selector);
        }

        $window.fadeIn("slow");

        $window.outerHeight(selectorHeight);
        $window.outerWidth(selectorWidth);
        $window.offset({top: selectorTop, left: selectorLeft})
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