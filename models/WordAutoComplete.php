<?php

namespace app\models;

use Yii;
use yii\base\Model;

class WordAutoComplete extends Model
{
    const COLUMN_SEARCH = ['id', 'name', 'value'];

    private $query;
    public $term, $term_name, $term_parent, $term_category;
    private $depth = 1;
    private $withParent = false;

    public function rules()
    {
        return [
            [['term', 'term_name', 'term_parent', 'term_category'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->query = Word::find();
    }

    public function getJsonList()
    {
        $data = $this->query
            ->select(['name as value'])
            ->andFilterWhere(['deleted' => Status::NOT_DELETED])
            ->orderBy('name')
            ->limit(Yii::$app->params['maxLinesAutoComplete'])
//            ->distinct()
            ->asArray()
            ->all();

        return json_encode($data);
    }

    public function addConditionByName($params = [])
    {
        $term = isset($params['term']) ? $params['term'] : $this->term;
        $this->query
            ->andFilterWhere(['like', 'name', $term . '%', false]);
        return $this;
    }

    public function addConditionByParentName($params = [])
    {
        $term_parent = $depth = $withParent = NULL;
        foreach(['term_parent', 'depth', 'withParent'] as $item) {
            $$item = isset($params['item']) ? $params['item'] : $this->$item;
        }
        list('condition' => $condition, 'bind' => $bind) =
            Word::getConditionLikeName('parent_id', $term_parent, $depth, $withParent);
        $this->query
            ->andOnCondition($condition, $bind);
        return $this;
    }

    public function addConditionByParentId($params = [])
    {
        $term_parent = $depth = $withParent = NULL;
        foreach(['term_parent', 'depth', 'withParent'] as $item) {
            $$item = isset($params['item']) ? $params['item'] : $this->$item;
        }
        list('condition' => $condition, 'bind' => $bind) =
            Word::getConditionById('parent_id', $term_parent, $depth, $withParent);
        $this->query
            ->andOnCondition($condition, $bind);
        return $this;
    }

    public function addConditionByColumnName($params = [])
    {
        $term =  $term_name = NULL;
        foreach(['term', 'term_name'] as $item) {
            $$item = isset($params['item']) ? $params['item'] : $this->$item;
        }
        if (in_array($term_name, self::COLUMN_SEARCH)) {
            $this->query
                ->andFilterWhere(['like', $term_name, $term . '%', false]);
        } else {
            $this->query
                ->where('0=1');
        }
        return $this;
    }

    public function formName()
    {
        return '';
    }
}
