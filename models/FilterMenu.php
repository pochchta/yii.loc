<?php


namespace app\models;


class FilterMenu
{
    private $headerMenu, $listSource = [], $listLabel = [], $menu = [];
    const DEFAULT_SOURCE = 'word';

    public function __construct($headerMenu)
    {
        $this->headerMenu = $headerMenu;
    }

    /** Установить источники
     * @param $listSource ['name' => 'category']
     * word - категория word - по-умолчанию
     * category - список категорий word
     * date - дата
     * text - текстовое поле
     * deleted - список not_deleted, deleted, all
     * @return $this
     */
    public function setSource($listSource)
    {
        $this->listSource = $listSource;
        return $this;
    }

    /** Установить метки
     * @param $listLabel ['name' => 'label']
     * @return $this
     */
    public function setLabel($listLabel)
    {
        $this->listLabel = $listLabel;
        return $this;
    }

    /** Сформировать меню
     *
     */
    public function buildMenu()
    {
        foreach ($this->headerMenu as $item) {
            $source = self::DEFAULT_SOURCE;
            if (isset($this->listSource[$item])) {
                $source = $this->listSource[$item];
            }
            if ($source === self::DEFAULT_SOURCE) {
                $id = Word::getFieldWord($item);
                $this->menu[$item]['label'] = Word::LABEL_FIELD_WORD[$id];
                $this->menu[$item]['id'] = $id;
            } else {
                $this->menu[$item]['id'] = $item;
            }
            $this->menu[$item]['source'] = $source;
            $this->menu[$item]['name'] = $item;
            if (isset($this->listLabel[$item])) {
                $this->menu[$item]['label'] = $this->listLabel[$item];
            }
        }
        return $this;
    }

    /** Получить меню
     *
     */
    public function getMenu()
    {
        return $this->menu;
    }
}