/**
 * Работа с $location.attr('search')
 */
class locSearch
{
    search;
    pathname;

    constructor(search = '') {
        let $location = $(location);
        this.pathname = $location.attr('pathname');

        this.search = search;
        if (search.length === 0) {
            let attrSearch = $location.attr('search');
            if (typeof attrSearch !== 'undefined' && attrSearch.length > 0) {
                this.search = attrSearch.substr(1);
            }
        }
    }

    /**
     * Удаление пустых значений
     * @returns {locSearch}
     */
    deleteEmptyValues() {
        this.search = this.search
            .split('&')
            .filter(elem => elem.indexOf('=') + 1 !== elem.length)
            .join('&')
        return this;
    }

    /**
     * Удаление по ключу
     * @param name имя фильтра, который будет сброшен
     * @param deleteOne true сбросить один, а остальные оставить; false - наоборот
     */
    deleteKey(name = '', deleteOne = true) {
        if (name.length > 0) {
            this.search = this.search
                .split('&')
                .filter(elem => deleteOne ^ elem.includes(name + '='))
                .join('&')
        }
        return this;
    }

    /**
     * Объединение строк search
     * @param addedSearch Строка
     * @returns {locSearch}
     */
    concat(addedSearch) {
        addedSearch = addedSearch
            .split('&')
            .filter(elem => this.search.indexOf(elem.substr(0, elem.indexOf('=') + 1)) < 0)
            .join('&')
        if (addedSearch.length > 0) {
            this.search = this.search + '&' + addedSearch
        }
        return this;
    }

    getSearch() {
        return this.search;
    }

    getUrl() {
        return this.pathname + '?' + this.search;
    }
}