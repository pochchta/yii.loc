<?php


namespace app\models;


class FilterMenu
{
    private $headerMenu, $listSource = [], $listLabel = [];
    const DEFAULT_SOURCE = 'word';

    public function __construct($headerMenu)
    {
        $this->headerMenu = $headerMenu;
    }

    public function setSource($listSource)
    {
        $this->listSource = $listSource;
        return $this;
    }

    public function setLabel($listLabel)
    {
        $this->listLabel = $listLabel;
        return $this;
    }

    /**
     * @return array ['id' => ключ или название, 'source' => тип или источник, 'name' => название, 'label' => название по-русски]
     */
    public function getMenu()
    {
        $menu = [];
        foreach ($this->headerMenu as $item) {
            $source = self::DEFAULT_SOURCE;
            if (isset($this->listSource[$item])) {
                $source = $this->listSource[$item];
            }
            if ($source === self::DEFAULT_SOURCE) {
                $menu[$item] = WordSearch::findNamesByParentId(['parent_id' => $id = Word::getFieldWord($item)]);
                $menu[$item]['label'] = Word::LABEL_FIELD_WORD[$id];
                $menu[$item]['id'] = $id;
            } else {
                $menu[$item]['id'] = $item;
            }
            $menu[$item]['source'] = $source;
            $menu[$item]['name'] = $item;
            if (isset($this->listLabel[$item])) {
                $menu[$item]['label'] = $this->listLabel[$item];
            }
        }
        return $menu;
    }
}