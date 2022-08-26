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
        $names = array_map(function ($item){
            return $this->extractColumnName($item);
        }, $this->unSortGridViewData['columns']);
        $hashes = array_map(function ($item){
            return md5(json_encode($item));
        }, $this->unSortGridViewData['columns']);
        $columnsWithHash = array_combine($hashes, $this->unSortGridViewData['columns']);
        $namesWithHash = array_combine($hashes, $names);

        $usedHashesFromRep = array_filter($this->columnsFromRep, function ($hash) use ($namesWithHash) {
            return key_exists($hash, $namesWithHash);
        });
        $usedNamesFromRep = array_map(function ($hash) use ($namesWithHash) {
            return $namesWithHash[$hash];
        }, $usedHashesFromRep);
        $usedNamesWithHashFromRep = array_combine($usedHashesFromRep, $usedNamesFromRep);
        $usedNamesWithHashFromParams = array_filter($namesWithHash, function ($name) {
            return in_array($name, $this->params['notDisabled']);
        });
        $usedNamesWithHash = array_merge($usedNamesWithHashFromRep, $usedNamesWithHashFromParams);

        $columnsSort = array_map(function ($hash) use ($columnsWithHash) {
            return $columnsWithHash[$hash];
        }, array_keys($usedNamesWithHash));

        $this->gridViewData = $this->unSortGridViewData;
        if (! empty($columnsSort)) {
            $this->gridViewData['columns'] = $columnsSort;
        }

        $this->columns['enabled'] = $usedNamesWithHash;
        $this->columns['disabled'] = array_diff($namesWithHash, $this->columns['enabled']);
        $this->columns['params'] = $this->params;
    }

    private function extractColumnName($item)
    {
        if (is_string($item)) {
            return $item;
        } elseif (is_array($item)) {
            if (array_key_exists('attribute', $item)) {
                return $item['attribute'];
            } elseif (array_key_exists('class', $item)) {
                return '№';
            }
        }
        return 'service';
    }
}