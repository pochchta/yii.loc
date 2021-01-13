<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeviceSearch represents the model behind the search form of `app\models\Device`.
 */
class DeviceSearch extends Device
{
    const DEFAULT_LIMIT_RECORDS = 20;
    const PRINT_LIMIT_RECORDS = 500;
    public $limit = self::DEFAULT_LIMIT_RECORDS;

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
            [['id', 'number', 'department_id', 'scale_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['name', 'type', 'description'], 'string', 'max' => 64],
            [['deleted'], 'default', 'value' => Device::NOT_DELETED],
            [['department_id'], 'default', 'value' => Word::ALL],
            [['scale_id'], 'default', 'value' => Word::ALL],
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
        $query = Device::find()->with('creator', 'updater', 'department', 'scale');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

        list('array' => $this->arrDepartment, 'condition' => $this->condDepartment) =
            CategoryWord::getArrFilters($params, CategoryWord::FIELD_WORD['Department']);
        list('array' => $this->arrScale, 'condition' => $this->condScale) =
            CategoryWord::getArrFilters($params, CategoryWord::FIELD_WORD['Scale']);

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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($this->condDepartment['condition'] !== NULL) {
            $query->andOnCondition(
                $this->condDepartment['condition'], $this->condDepartment['bind']
            );
        }

        if ($this->condScale['condition'] !== NULL) {
            $query->andOnCondition(
                $this->condScale['condition'], $this->condScale['bind']
            );
        }

        if ($this->deleted != Device::ALL) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
