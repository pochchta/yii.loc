<?php


namespace app\widgets\csc;


use app\models\catalogTabs\CatalogTabs;

class CatalogTabsSort extends MainSort
{
    private $menu;

    public function __construct(CatalogTabs $menu, array $params = [])
    {
        $this->menu = $menu;
        $this->loadParams($params);
    }

    protected function process()
    {
        if (! isset($this->columnsForWidget)) {
            $this->takeColumnsFromRep();
            $this->sortMenu($this->namesFromRep);
        }
    }

    /**
     * @return CatalogTabs
     */
    public function getMenu()
    {
        $this->process();
        return $this->menu;
    }

    /**
     * Сортировка и фильтрация меню по $newOrder и формирование столбцов для выбора
     * @param array $newOrder ['название_поля', ...]
     */
    public function sortMenu(array $newOrder)
    {
        $headerMenu = $this->menu->getHeaderMenu();
        $newHeaderMenu = [];
        foreach ($newOrder as $newOrderItem) {
            if (in_array($newOrderItem, $headerMenu)) {
                $newHeaderMenu[] = $newOrderItem;
            }
        }

        $this->columnsForWidget['enabled'] = array_map(function ($item) {
            return $this->menu->getMenu()[$item]['label'];
        }, $newHeaderMenu);
        $this->columnsForWidget['disabled'] = array_map(function ($item) {
            return $this->menu->getMenu()[$item]['label'];
        }, array_diff($headerMenu, $newHeaderMenu));
        $this->columnsForWidget['params'] = $this->params;

        $this->menu->setHeaderMenu($newHeaderMenu);
        $this->menu->buildMenu();
    }
}