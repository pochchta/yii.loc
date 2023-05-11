<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WordSearch represents the model behind the search form of `app\models\Word`.
 */
class WordSearch extends Word
{
    public $limit;
    public $parent;
    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted', 'parent_id'], 'integer'],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'filter',
                'filter' => function($value) {return strtotime($value) !== false ? strtotime($value) : null;}],
            [['name', 'value', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['parent'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['limit'], 'integer', 'min' => 0, 'max' => Yii::$app->params['maxLines']],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
        $query = Word::find()->with('parent.parent');

        // add conditions that should always apply here

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

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
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

        if (strlen($this->name)) {
            $query->andFilterWhere(['like', 'name', $this->name . '%', false]);
        }

        if (strlen($this->value)) {
            $query->andFilterWhere(['like', 'value', $this->value . '%', false]);
        }

        if (strlen($this->parent)) {
            $subQueries = ['or'];
            $condition = ['like', 'name', $this->parent . '%', false];
            $subQueries[]['id'] = Word::getQueriesToGetChildrenIfParentIsVirtual($condition)[1];
            $subQueries[]['id'] = Word::getQueriesToGetChildren($condition)[1];
            $query->andFilterWhere($subQueries);
        }

        if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }
}