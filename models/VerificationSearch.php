<?php

namespace app\models;

use Yii;
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
    public $device_name, $device_number, $device_department;
    public $term, $term_name;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'device_id', 'period', 'created_at', 'updated_at', 'created_by', 'updated_by', 'status', 'deleted'], 'integer'],
            [['name', 'type', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['last_date_start', 'last_date_end', 'next_date_start', 'next_date_end'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['status'], 'default', 'value' => Verification::STATUS_ON],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['device_name', 'device_number', 'device_department'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['term', 'term_name'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']]
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
        $query = Verification::find()->with('creator', 'updater', 'device.wordName', 'device.wordDepartment');

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'device_id' => $this->device_id,
            'period' => $this->period,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($this->status == Verification::STATUS_OFF || $this->status == Verification::STATUS_ON) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
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

        foreach(['name', 'department'] as $item) {
            $depth = 3;
            if ($item == 'department') {
                $depth = 2;
            }
            $field = $this->{'device_' . $item};
            if (strlen($field)) {
                list('condition' => $condition, 'bind' => $bind) =
                    Word::getConditionLikeName("{$item}_id", $field, $depth, true);
                $query->andOnCondition("device_id IN (SELECT id FROM device WHERE $condition)", $bind);
            }
        }

        if ($this->device_number != '') {
            $query->andOnCondition(
                'device_id IN (SELECT id FROM device WHERE number LIKE :number AND deleted = :not_del)',
                [':number' => $this->device_number . '%', ':not_del' => Status::NOT_DELETED]
            );
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
