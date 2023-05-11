<?php


namespace app\widgets\csc;


use app\models\CatalogTabs;

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
            $this->sortMenu();
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
     * Сортировка и фильтрация меню по $namesFromRep и формирование столбцов для выбора
     */
    public function sortMenu()
    {
        $menu = $this->menu->getMenu();
        $names = array_map(function ($item) {
            return $item['label'];
        }, $menu);
        $keysByName = array_combine($names, array_keys($menu));

        $selectedNames = array_unique(array_merge($this->namesFromRep, $this->params['required']));     // настройки + required
        $usedNames = array_intersect($selectedNames, $names);                                           // что используется на самом деле
        $newHeaderMenu = array_map(function ($item) use ($keysByName) {
            return $keysByName[$item];
        }, $usedNames);                                                                                 // заголовок меню

        $allLabels = array_unique(array_merge($names, $this->params['required']));                      // для виджета сортировки

        $this->columnsForWidget['enabled'] = array_intersect($this->namesFromRep, $names);
        $disabled = array_diff($allLabels, $this->columnsForWidget['enabled']);
        sort($disabled);
        $this->columnsForWidget['disabled'] = array_values($disabled);

        $this->columnsForWidget['params'] = $this->params;

        $this->menu->setHeaderMenu($newHeaderMenu);
        $this->menu->buildMenu();
    }
}