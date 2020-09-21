<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form of `app\models\AuthAssignment`.
 */
class AssignmentSearch extends AuthAssignment
{
    /**
     * {@inheritdoc}
     */

    public $username;
    public $created_at_start, $created_at_end;

    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['item_name', 'username'], 'string', 'max' => 64],
            [['item_name', 'username'], 'match', 'pattern' => '/^[\w- ]+$/i'],
            [['created_at_start', 'created_at_end'], 'integer']
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
        $query = AuthAssignment::find();
        $query->joinWith('user')->with('item');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'item_name',
                'user_id',
                'username' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC]
                ],
                'created_at'
            ],
            'defaultOrder' => ['username' => SORT_ASC]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'item_name', $this->item_name]);

        $query->andFilterWhere(['like', 'user.username', $this->username]);

        if ($this->created_at_start != '') {
            $query->andFilterWhere(['>=', 'created_at', strtotime($this->created_at_start)]);
        }

        if ($this->created_at_end != '') {
            $query->andFilterWhere(['<', 'created_at', strtotime($this->created_at_end)]);
        }

        /*        $query->joinWith(['user' => function ($q) {
                    $q->andFilterWhere(['like', 'user.username', $this->username]);
                }]);*/

        return $dataProvider;
    }
}
