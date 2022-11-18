<?php


namespace app\widgets\sort;


class GridColumnSort
{
    private $params;
    private $unSortGridViewData;
    private $gridViewData;
    private $namesFromRep;
    private $columnsForWidget;

    /**
     * @param array $gridViewData
     * @param array $params
     */
    public function __construct(array $gridViewData = ['columns' => []], array $params = [])
    {
        $this->unSortGridViewData = $gridViewData;
        foreach (['name', 'class', 'role', 'writeUrl'] as $name) {
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

        $this->params = $params;
    }

    public function getGridViewData()
    {
        $this->process();
        return $this->gridViewData;
    }

    public function runWidget()
    {
        $this->process();
        return ViewRender::widget([
            'clientOptions' => [
                'params' => $this->params,
                'columns' => $this->columnsForWidget,
            ]
        ]);
    }

    public function getColumnsForWidget()
    {
        return $this->columnsForWidget;
    }

    private function process()
    {
        if (! isset($this->columnsForWidget)) {
            $this->takeColumnsFromRep();
            $this->takeColumnsFromGridViewData();
        }
    }

    private function takeColumnsFromRep()
    {
        $model = Model::findOne([
            'role' => $this->params['role'],
            'name' => $this->params['name']
        ]);
        $this->namesFromRep = [];
        if ($model) {
            $this->namesFromRep = json_decode($model->col);
        }
    }

    private function takeColumnsFromGridViewData()
    {
        $names = array_map(function ($item, $key){
            if (is_string($key)) {
                return $key;
            }
            return $this->extractColumnName($item);
        }, $this->unSortGridViewData['columns'], array_keys($this->unSortGridViewData['columns']));
        $columnsByName = array_combine($names, $this->unSortGridViewData['columns']);

        $selectedNames = array_unique(array_merge($this->namesFromRep, $this->params['required']));
        $usedNames = array_intersect($selectedNames, $names);

        $columnsByNameSort = array_map(function ($name) use ($columnsByName) {
            return $columnsByName[$name];
        }, $usedNames);

        $this->gridViewData = $this->unSortGridViewData;
        if (! empty($columnsByNameSort)) {
            $this->gridViewData['columns'] = array_values($columnsByNameSort);
        }

        $this->columnsForWidget['enabled'] = $selectedNames;
        $this->columnsForWidget['disabled'] = array_diff($names, $this->columnsForWidget['enabled']);
        $this->columnsForWidget['params'] = $this->params;
    }

    private function extractColumnName($item)
    {
        if (is_string($item)) {
            $pos = strpos($item, ':');
            if ($pos !== false) {
                $item = substr($item, 0, $pos);
            }
            return $this->findLabel($item);
        } elseif (is_array($item)) {
            if (array_key_exists('attribute', $item)) {
                return $this->findLabel($item['attribute']);
            } elseif (array_key_exists('class', $item)) {
                return $this->getShortClassName($item['class']);
            }
        }
        return 'noname';
    }

    private function findLabel($key)
    {
        if (class_exists($this->params['class'])) {
            $label = (new $this->params['class'])->getAttributeLabel($key);
        }
        return $label ?? $key;
    }

    private function getShortClassName($name)
    {
        $pos = strrpos($name, '\\');
        if ($pos !== false) {
            $name = substr($name, $pos + 1);
        }
        return $name;
    }
}