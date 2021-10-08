<?php


namespace app\models;


class FilterMenu
{
    private $headerMenu, $listSource = [], $listLabel = [], $menu = [], $filterParams = [];
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

    /** Сформировать меню
     *
     */
    public function loadMenu()
    {
        foreach ($this->headerMenu as $item) {
            $source = self::DEFAULT_SOURCE;
            if (isset($this->listSource[$item])) {
                $source = $this->listSource[$item];
            }
            if ($source === self::DEFAULT_SOURCE) {
                $this->menu[$item] = WordSearch::findNamesByParentId(['parent_id' => $id = Word::getFieldWord($item)]);
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

    /** Сформировать список примененных фильтров по источнику
     * @param array $sources массив источников
     * @param array $params параметры запроса
     * @return FilterMenu
     */
    public function loadFilterParams($sources, $params)
    {
        $arrayId = [];
        foreach ($sources as $source) {
            if ($source === 'word') {
                foreach ($params as $key => $item) {
                    if (strlen($item)) {
                        if (in_array($key, Word::FIELD_WORD)) {
                            $arrayId[$key] = $item;
                        }
                    }
                }
            }
        }
        $list = Word::find()->select(['id', 'name as label'])->where(['id' => $arrayId])->asArray()->all();
        foreach ($list as $key => $filter) {
            $this->filterParams[$filter['id']]['label'] = $filter['label'];
            $this->filterParams[$filter['id']]['name']= array_search($filter['id'], $arrayId);
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

    /** Получить список примененных фильтров по источнику, сформированный в self::loadFilterParams()
     *
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }
}