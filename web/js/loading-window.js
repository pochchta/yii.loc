class loadingWindow {
    /**
     * Создание и отображение окна "загрузка" поверх переданного элемента jquery
     * @param $element js или jquery
     */
    static show($element) {
        if (! ($element instanceof jQuery)) {
            $element = $($element);
        }
        let selectorWidth = $element.outerWidth();
        let selectorHeight = $element.outerHeight();
        let selectorPosition = $element.offset();
        let selectorLeft = selectorPosition.left;
        let selectorTop = selectorPosition.top;

        let $absChildren = $element.find('.absolute:not(.hide)');  // с учетом вложенных .absolute
        for (let child of $absChildren) {
            let $child = $(child);
            if (! $child.is(':visible')) {
                continue;
            }

            // получаем позицию и размеры дочернего элемента
            let childWidth = $child.outerWidth();
            let childHeight = $child.outerHeight();
            let childPosition = $child.offset();
            let childLeft = childPosition.left;
            let childTop = childPosition.top;

            // проверяем, перекрывает ли дочерний элемент родительский
            if (childLeft < selectorLeft) {
                selectorWidth = selectorWidth + selectorLeft - childLeft;
                selectorLeft = childLeft;
            }
            if (childTop < selectorTop) {
                selectorHeight = selectorHeight + selectorTop - childTop;
                selectorTop = childTop;
            }
            if (childLeft + childWidth > selectorLeft + selectorWidth) {
                selectorWidth = childLeft + childWidth - selectorLeft;
            }
            if (childTop + childHeight > selectorTop + selectorHeight) {
                selectorHeight = childHeight + childTop - selectorTop;
            }
        }

        let loading_window_id = $element.attr('id') + '_loading_window';
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

            $window.insertAfter($element);
        }

        $window.fadeIn("slow");

        $window.outerHeight(selectorHeight);
        $window.outerWidth(selectorWidth);
        $window.offset({top: selectorTop, left: selectorLeft})
    }

    /**
     * Сокрытие окна "загрузка" над переданным элементом
     * @param $element js или jquery
     */
    static hide($element) {
        if (! ($element instanceof jQuery)) {
            $element = $($element);
        }
        let loading_window_id = $element.attr('id') + '_loading_window';
        let $window = $('#' + loading_window_id);
        if ($window.length) {
            $window.css('display', 'none');
        }
    }
}