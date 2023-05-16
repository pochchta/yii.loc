<?php


namespace app\models;


class CatalogTabs
{
    const DEFAULT_SOURCE = 'word';
    const PREFIXES = ['device'];

    private $headerMenu, $listSource = [], $listLabel = [], $listAutoComplete = [], $menu;

    public function __construct($headerMenu)
    {
        $this->headerMenu = $headerMenu;
        $this->addSettingsButton();
    }

    /**
     * Установить источники
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
        $this->listSource = array_merge($this->listSource, $listSource);
        return $this;
    }

    /** Установить метки
     * @param $listLabel ['name' => 'label']
     * @return $this
     */
    public function setLabel($listLabel)
    {
        $this->listLabel = array_merge($this->listLabel, $listLabel);
        return $this;
    }

    /**
     * Установить параметры autoComplete
     * @param $listAutoComplete ['parentName' => ['name', ...]]
     * @return $this
     */
    public function setAutoComplete($listAutoComplete)
    {
        $this->listAutoComplete = array_merge($this->listAutoComplete, $listAutoComplete);
        return $this;
    }

    /**
     * Сформировать меню
     */
    public function buildMenu()
    {
        $this->menu = [];
        foreach ($this->headerMenu as $item) {
            $this->menu[$item]['field'] = $item;
            $explodedName = explode('_', $item);
            if (in_array($explodedName[0], self::PREFIXES)) {
                array_shift($explodedName);
                $this->menu[$item]['field'] = implode('_', $explodedName);
            }

            $source = self::DEFAULT_SOURCE;
            if (isset($this->listSource[$item])) {
                $source = $this->listSource[$item];
            }
            if ($source === self::DEFAULT_SOURCE) {
                $id = Word::getFieldWord($this->menu[$item]['field']);
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
            foreach ($this->listAutoComplete as $parentName => $fields) {
                if (in_array($item, $fields)) {
                    $this->menu[$item]['autoComplete'] = ['class' => 'ui-autocomplete-input', 'data' => ['parent' => $parentName]];
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Добавить кнопку настроек в шапку ['settingsButton' => 'Кнопка']
     */
    private function addSettingsButton()
    {
        if (! in_array('settingsButton', $this->headerMenu)) {
            $this->headerMenu[] = 'settingsButton';
            $this->listLabel['settingsButton'] = 'Настройки';
            $this->listSource['settingsButton'] = 'settingsButton';
        }
    }

    /**
     * Получить меню в виде массива
     */
    public function getMenu()
    {
        if (! isset($this->menu)) {
            $this->buildMenu();
        }
        return $this->menu;
    }

    /**
     * Получить заголовок меню
     */
    public function getHeaderMenu()
    {
        return $this->headerMenu;
    }

    /**
     * Установить заголовок меню
     * @param array $headerMenu
     */
    public function setHeaderMenu(array $headerMenu)
    {
        $this->headerMenu = $headerMenu;
    }
}