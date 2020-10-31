<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VerificationSearch represents the model behind the search form of `app\models\Verification`.
 */
class VerificationSearch extends Verification
{
    const DEFAULT_LIMIT_RECORDS = 20;
    const PRINT_LIMIT_RECORDS = 500;
    public $limit = self::DEFAULT_LIMIT_RECORDS;
    public $last_date_start, $last_date_end, $next_date_start, $next_date_end;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'device_id', 'period', 'created_at', 'updated_at', 'created_by', 'updated_by', 'status', 'deleted'], 'integer'],
            [['name', 'type', 'description'], 'string', 'max' => 64],
            [['last_date_start', 'last_date_end', 'next_date_start', 'next_date_end'], 'string', 'max' => 64],
            [['status'], 'default', 'value' => Verification::STATUS_ON],
            [['deleted'], 'default', 'value' => Verification::NOT_DELETED]
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
        $query = Verification::find()->with('creator', 'updater', 'device');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

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
            'period' => $this->period,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($this->status == Verification::STATUS_OFF || $this->status == Verification::STATUS_ON) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if ($this->deleted == Verification::NOT_DELETED || $this->deleted == Verification::DELETED) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        if ($this->last_date_start != '') {
            $query->andFilterWhere(['>=', 'last_date', strtotime($this->last_date_start)]);
        }
        if ($this->last_date_end != '') {
            $query->andFilterWhere(['<', 'last_date', strtotime($this->last_date_end)]);
        }
        if ($this->next_date_start != '') {
            $query->andFilterWhere(['>=', 'next_date', strtotime($this->next_date_start)]);
        }
        if ($this->next_date_end != '') {
            $query->andFilterWhere(['<', 'next_date', strtotime($this->next_date_end)]);
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
