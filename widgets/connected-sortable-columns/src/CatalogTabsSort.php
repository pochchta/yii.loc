<?php


namespace app\widgets\csc;


use app\models\catalogTabs\CatalogTabs;
use Yii;

class CatalogTabsSort
{
    private $params;
    private $menu;
    private $namesFromRep;
    private $columnsForWidget;

    public function __construct(CatalogTabs $menu, array $params = [])
    {
        $this->menu = $menu;
        foreach (['name', 'class', 'role', 'write_url', 'read_url', 'token'] as $name) {
            if (! isset($params[$name])) {
                $params[$name] = '';
            }
        }
        if ($params['name'] === '') {
            $params['name'] = $this->getShortClassName($params['class']);
        }
        if (! isset($params['required'])) {
            $params['required'] = [];
        }

        $params['widget_name'] = basename(get_class($this));

        $this->params = $params;
    }

    public function runWidget()
    {
        $this->process();
        return ViewRender::widget([
            'clientOptions' => [
                'columns' => $this->columnsForWidget,
            ]
        ]);
    }

    private function process()
    {
        if (! isset($this->columnsForWidget)) {
            $this->takeColumnsFromRep();
            $this->sortMenu($this->namesFromRep);
        }
    }

    /**
     * Запросить настройки сортировки по профилю
     */
    private function takeColumnsFromRep()
    {
        $model = Model::findOne([
            'role' => $this->params['role'],
            'name' => $this->params['name'],
            'widget_name' => $this->params['widget_name'],
        ]);
        $this->namesFromRep = [];
        if ($model) {
            $this->namesFromRep = json_decode($model->col);
        }
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

        $this->columnsForWidget['enabled'] = array_values($newHeaderMenu);
        $this->columnsForWidget['disabled'] =
            array_values(array_diff($headerMenu, $this->columnsForWidget['enabled']));
        $this->columnsForWidget['params'] = $this->params;

        $this->menu->setHeaderMenu($newHeaderMenu);
    }

    /**
     * @return CatalogTabs
     */
    public function getMenu()
    {
        $this->process();
        return $this->menu;
    }

    private function getShortClassName($name)
    {
        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }
        return $name;
    }

    /**
     * @return array
     */
    public static function getListProfileView()
    {
        $keys = array_keys(Yii::$app->authManager->getRoles());
        $roles = array_combine($keys, $keys);
        return array_merge(
            ['default' => 'По умолчанию'],
            $roles
        );
    }
}