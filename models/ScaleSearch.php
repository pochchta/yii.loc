<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScaleSearch represents the model behind the search form of `app\models\Scale`.
 */
class ScaleSearch extends Scale
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['value', 'description'], 'string', 'max' => 64],
            [['deleted'], 'default', 'value' => Scale::NOT_DELETED]

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
        $query = Scale::find()->with('creator', 'updater');

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($this->deleted == Device::NOT_DELETED || $this->deleted == Device::DELETED) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }
}
