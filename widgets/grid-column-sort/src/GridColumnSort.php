<?php


namespace app\widgets\sort;


class GridColumnSort
{
    private $params;
    private $unSortGridViewData;
    private $gridViewData;
    private $columnsFromGridViewData;
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
            $this->takeColumnsFromGridViewData();
            $this->sort();
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
        $this->columnsFromGridViewData = [];
        foreach ($this->unSortGridViewData['columns'] as $item) {
            if (is_string($item)) {
                $this->columnsFromGridViewData[] = $item;
            } elseif (is_array($item)) {
                if (array_key_exists('attribute', $item)) {
                    $this->columnsFromGridViewData[] = $item['attribute'];
                } elseif (! array_key_exists('class', $item)) {
                    $this->columnsFromGridViewData[] = 'noname';
                }
            }
        }
    }

    private function sort()
    {
        $this->columns = [];
        $this->gridViewData = $this->unSortGridViewData;
    }
}