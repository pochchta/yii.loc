<?php


namespace app\widgets\csc;


class GridColumnSort extends MainSort
{
    private $unSortGridViewData;
    private $gridViewData;

    /**
     * @param array $gridViewData
     * @param array $params
     */
    public function __construct(array $gridViewData = ['columns' => []], array $params = [])
    {
        $this->unSortGridViewData = $gridViewData;
        $this->loadParams($params);
    }

    protected function process()
    {
        if (! isset($this->columnsForWidget)) {
            $this->takeColumnsFromRep();
            $this->takeColumnsFromGridViewData();
        }
    }

    public function getGridViewData()
    {
        $this->process();
        return $this->gridViewData;
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

        $this->columnsForWidget['enabled'] = array_values($usedNames);
        $this->columnsForWidget['disabled'] =
            array_values(array_diff($names, $this->columnsForWidget['enabled']));
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
}