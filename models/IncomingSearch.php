<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IncomingSearch represents the model behind the search form of `app\models\Incoming`.
 */
class IncomingSearch extends Incoming
{
    const DEFAULT_LIMIT_RECORDS = 20;
    public $limit = self::DEFAULT_LIMIT_RECORDS;
    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'device_id', 'status', 'payment', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => 64],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'string', 'max' => 64],
            [['status'], 'default', 'value' => Incoming::ALL],
            [['payment'], 'default', 'value' => Incoming::ALL],
            [['deleted'], 'default', 'value' => Incoming::NOT_DELETED]
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Incoming::find()->with('creator', 'updater', 'device');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'device_id' => $this->device_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->status != Incoming::ALL) {
            $query->andFilterWhere(['status' => $this->status]);
        }
        if ($this->payment != Incoming::ALL) {
            $query->andFilterWhere(['payment' => $this->payment]);
        }
        if ($this->deleted != Incoming::ALL) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        if ($this->created_at_start != '') {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->created_at_start)]);
        }
        if ($this->created_at_end != '') {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->created_at_end)]);
        }
        if ($this->updated_at_start != '') {
            $query->andFilterWhere(['>=', 'updated_at', strtotime($this->updated_at_start)]);
        }
        if ($this->updated_at_end != '') {
            $query->andFilterWhere(['<', 'updated_at', strtotime($this->updated_at_end)]);
        }

        return $dataProvider;
    }
}
