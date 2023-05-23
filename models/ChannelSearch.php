<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ChannelSearch represents the model behind the search form of `app\models\Channel`.
 */
class ChannelSearch extends Channel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'io', 'parent_id', 'device_id', 'type_id', 'accuracy_id', 'scale_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_id'], 'integer'],
            [['range', 'description'], 'safe'],
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
        $query = Channel::find();

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
            'number' => $this->number,
            'io' => $this->io,
            'parent_id' => $this->parent_id,
            'device_id' => $this->device_id,
            'type_id' => $this->type_id,
            'accuracy_id' => $this->accuracy_id,
            'scale_id' => $this->scale_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_id' => $this->deleted_id,
        ]);

        $query->andFilterWhere(['like', 'range', $this->range])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
