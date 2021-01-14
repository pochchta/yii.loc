<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategoryWordSearch represents the model behind the search form of `app\models\CategoryWord`.
 */
class CategoryWordSearch extends CategoryWord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted', 'parent_id'], 'integer'],
            [['name', 'value', 'description'], 'safe'],
            [['deleted'], 'default', 'value' => CategoryWord::NOT_DELETED],
            [['firstCategory', 'secondCategory'], 'integer'],
            [['firstCategory', 'secondCategory'], 'default', 'value' => CategoryWord::ALL],
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
        $query = CategoryWord::find()->with('parent.parent');

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($this->secondCategory != CategoryWord::ALL && $this->secondCategory != 0) {
            $query->andFilterWhere(['parent_id' => $this->secondCategory]);
        } elseif ($this->firstCategory != CategoryWord::ALL) {
            if ($this->secondCategory == '0') {
                $query->andFilterWhere(['parent_id' => $this->firstCategory]);
            } else {
                $query->andOnCondition(
                    'parent_id = :id OR parent_id IN (SELECT id FROM category_word WHERE parent_id = :id AND deleted = :del)',
                    [':id' => $this->firstCategory, ':del' => self::NOT_DELETED]
                );
            }
        }

        if ($this->deleted == CategoryWord::NOT_DELETED || $this->deleted == CategoryWord::DELETED) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
