<?php


namespace app\widgets\sort;


class GridColumnSort
{
    private $params;
    private $unSortGridViewData;
    private $gridViewData;
    private $columnsFromRep;
    private $columns;

    /**
     * @param array $gridViewData
     * @param array $params
     */
    public function __construct(array $gridViewData, array $params)
    {
        $this->unSortGridViewData = $gridViewData;
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
                'columns' => $this->columns,
            ]
        ]);
    }

    private function process()
    {
        if (! isset($this->columns)) {
            $this->takeColumnsFromRep();
            $this->takeColumnsFromGridViewData();
        }
    }

    private function takeColumnsFromRep()
    {
        $json = Model::findOne([
            'role' => $this->params['role'],
            'name' => $this->params['name']
        ])->col;
        $this->columnsFromRep = json_decode($json);
    }

    private function takeColumnsFromGridViewData()
    {
        $columnsService = array_filter($this->unSortGridViewData['columns'], function ($item) {
            return $this->filterColumnName($item) === null;
        });

        $columnsUnSort = array_filter($this->unSortGridViewData['columns'], function ($item) {
            return $this->filterColumnName($item) !== null;
        });

        $columnsKeys = array_map(function ($item){
            return $this->filterColumnName($item);
        }, $columnsUnSort);

        $columnsUnSortWithKeys = array_combine($columnsKeys, $columnsUnSort);

        $columnNames = array_filter($this->columnsFromRep, function ($name) use ($columnsUnSortWithKeys){
            return key_exists($name, $columnsUnSortWithKeys);
        });

        $columnsSort = array_map(function ($name) use ($columnsUnSortWithKeys) {
            return $columnsUnSortWithKeys[$name];
        }, $columnNames);

        $this->gridViewData = $this->unSortGridViewData;
        $this->gridViewData['columns'] = array_merge($columnsSort, $columnsService);

        $this->columns['enable'] = $columnNames;
        $this->columns['disable'] = array_diff($columnsKeys, $this->columns['enable']);

    }

    private function filterColumnName($item)
    {
        if (is_string($item)) {
            return $item;
        } elseif (is_array($item)) {
            if (array_key_exists('attribute', $item)) {
                return $item['attribute'];
            }/* elseif (! array_key_exists('class', $item)) {
                return 'noname';
            }*/
        }
    }
}