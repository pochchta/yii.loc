<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeviceSearch represents the model behind the search form of `app\models\Device`.
 */
class DeviceSearch extends Device
{
    public $limit;
    public $kind_id, $group_id, $type_id, $name_id, $state_id, $department_id, $crew_id;
    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_id'], 'integer'],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'filter',
                'filter' => function($value) {return strtotime($value) !== false ? strtotime($value) : null;}],
            [['description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['position', 'number'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['group', 'type', 'name', 'department', 'crew', 'kind', 'state'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['group_id', 'type_id', 'name_id', 'department_id', 'crew_id', 'kind_id', 'state_id'], 'integer'],
            [['deleted_id'], 'default', 'value' => Status::NOT_DELETED],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->limit = Yii::$app->params['maxLinesIndex'];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Device::find()
            ->with('creator', 'updater', 'wordKind', 'wordName.parent.parent', 'wordState', 'wordDepartment', 'wordCrew');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if (strlen($this->created_at_start)) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at_start]);
        }
        if (strlen($this->created_at_end)) {
            $query->andFilterWhere(['<=', 'created_at', $this->created_at_end]);
        }
        if (strlen($this->updated_at_start)) {
            $query->andFilterWhere(['>=', 'updated_at', $this->updated_at_start]);
        }
        if (strlen($this->updated_at_end)) {
            $query->andFilterWhere(['<=', 'updated_at', $this->updated_at_end]);
        }

        if (strlen($this->number)) {
            $query->andFilterWhere(['like', 'number', $this->number . '%', false]);
        }

        if (strlen($this->position)) {
            $query->andFilterWhere(['like', 'position', $this->position . '%', false]);
        }

        foreach(['kind', 'name', 'type', 'group', 'state', 'department', 'crew'] as $item) {
            $item_id = "{$item}_id";

            if (strlen($this->$item)) {                                                         // поиск по имени
                $subQueries = Word::getQueriesToGetChildren(['like', 'name', $this->$item . '%', false]);
                $query->andFilterWhere(Word::mergeQueriesOr($subQueries, $item_id, [0,1,2]));
            }

            if (strlen($this->$item_id)) {                                                     // поиск по id
                $subQueries = Word::getQueriesToGetChildren(['id' => $this->$item_id]);
                $query->andFilterWhere(Word::mergeQueriesOr($subQueries, $item_id, [0,1,2]));
            }
        }

        if ($this->deleted_id != Status::ALL) {
            $query->andFilterWhere(['deleted_id' => $this->deleted_id]);
        }

        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }
}