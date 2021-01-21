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
    const PRINT_LIMIT_RECORDS = 500;
    public $limit = self::DEFAULT_LIMIT_RECORDS;

    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;
    public $deviceName, $deviceNumber;

    public $firstDepartment;     // категории
    public $secondDepartment;
    public $thirdDepartment;
    public $firstScale;
    public $secondScale;
    public $thirdScale;

    public $arrDepartment;     // массивы для фильтров
    public $arrScale;

    public $condDepartment;    // получившееся условие для фильтра
    public $condScale;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'device_id', 'status', 'payment', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => 64],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'string', 'max' => 64],
            [['status'], 'default', 'value' => Status::ALL],
            [['payment'], 'default', 'value' => Status::ALL],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['deviceName'], 'string', 'max' => 64],
            [['deviceNumber'], 'integer'],
            [['firstDepartment', 'secondDepartment', 'thirdDepartment', 'firstScale', 'secondScale', 'thirdScale'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'firstDepartment' => 'Цеха',
            'secondDepartment' => '->',
            'thirdDepartment' => '->',
            'firstScale' => 'Шкалы',
            'secondScale' => '->',
            'thirdScale' => '->',
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
        $query = Incoming::find()->with('creator', 'updater', 'device.department', 'device.scale');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

        list('array' => $this->arrDepartment, 'condition' => $this->condDepartment) =
            Word::getArrFilters($params, Word::FIELD_WORD['Department']);
        list('array' => $this->arrScale, 'condition' => $this->condScale) =
            Word::getArrFilters($params, Word::FIELD_WORD['Scale']);

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

        if ($this->status != Status::ALL) {
            $query->andFilterWhere(['status' => $this->status]);
        }
        if ($this->payment != Status::ALL) {
            $query->andFilterWhere(['payment' => $this->payment]);
        }
        if ($this->deleted != Status::ALL) {
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

        if ($this->deviceName != '') {
            $query->andOnCondition(
                'device_id IN (SELECT id FROM device WHERE name LIKE :name AND deleted = :del)',
                [':name' => '%' . $this->deviceName . '%', ':del' => Status::NOT_DELETED]
            );
        }
        if ($this->deviceNumber != '') {
            $query->andOnCondition(
                'device_id IN (SELECT id FROM device WHERE number = :number AND deleted = :del)',
                [':number' => $this->deviceNumber, ':del' => Status::NOT_DELETED]
            );
        }

        if ($this->condDepartment['condition'] !== NULL) {
            $bind = $this->condDepartment['bind'];
            $bind[':del'] = Status::NOT_DELETED;

            $query->andOnCondition(
                'device_id IN (SELECT id FROM device WHERE ' . $this->condDepartment['condition'] . ' AND deleted = :del)',
                $bind
            );
        }

        if ($this->condScale['condition'] !== NULL) {
            $bind = $this->condScale['bind'];
            $bind[':del'] = Status::NOT_DELETED;

            $query->andOnCondition(
                'device_id IN (SELECT id FROM device WHERE ' . $this->condScale['condition'] . ' AND deleted = :del)',
                $bind
            );
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
