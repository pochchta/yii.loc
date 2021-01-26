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
    public $firstName;
    public $secondName;
    public $thirdName;
    public $firstType;
    public $secondType;
    public $thirdType;
    public $firstPosition;
    public $secondPosition;
    public $thirdPosition;
    public $firstAccuracy;
    public $secondAccuracy;
    public $thirdAccuracy;

    public $arrDepartment;     // массивы для фильтров
    public $arrScale;
    public $arrName;
    public $arrType;
    public $arrPosition;
    public $arrAccuracy;

    public $condDepartment;    // получившееся условие для фильтра
    public $condScale;
    public $condName;
    public $condType;
    public $condPosition;
    public $condAccuracy;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'name_id', 'type_id', 'number', 'department_id', 'scale_id', 'position', 'accuracy', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => 64],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['name_id', 'type_id', 'department_id', 'scale_id', 'position', 'accuracy'], 'default', 'value' => Status::ALL],
            [['firstDepartment', 'secondDepartment', 'thirdDepartment', 'firstScale', 'secondScale', 'thirdScale'], 'integer'],
            [['firstName', 'secondName', 'thirdName', 'firstType', 'secondType', 'thirdType'], 'integer'],
            [['firstPosition', 'secondPosition', 'thirdPosition', 'firstAccuracy', 'secondAccuracy', 'thirdAccuracy'], 'integer'],
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
            'firstName' => 'Название',
            'secondName' => '->',
            'thirdName' => '->',
            'firstType' => 'Тип',
            'secondType' => '->',
            'thirdType' => '->',
            'firstPosition' => 'Позиция',
            'secondPosition' => '->',
            'thirdPosition' => '->',
            'firstAccuracy' => 'Класс точности',
            'secondAccuracy' => '->',
            'thirdAccuracy' => '->',
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
        $query = Device::find()->with(
            'creator', 'updater', 'wordName', 'wordType', 'wordDepartment', 'wordPosition', 'wordScale', 'wordAccuracy'
        );
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

        $this->getArrFilters($params);

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

        $query->andFilterWhere(['like', 'description', $this->description]);

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

        if ($this->deleted != Status::ALL) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function getArrFilters (& $params) {
        list('array' => $this->arrDepartment, 'condition' => $this->condDepartment) =
            Word::getArrFilters($params, Word::FIELD_WORD['Department']);
        list('array' => $this->arrScale, 'condition' => $this->condScale) =
            Word::getArrFilters($params, Word::FIELD_WORD['Scale']);
        list('array' => $this->arrName, 'condition' => $this->condName) =
            Word::getArrFilters($params, Word::FIELD_WORD['Name']);
        list('array' => $this->arrType, 'condition' => $this->condType) =
            Word::getArrFilters($params, Word::FIELD_WORD['Type']);
        list('array' => $this->arrPosition, 'condition' => $this->condPosition) =
            Word::getArrFilters($params, Word::FIELD_WORD['Position']);
        list('array' => $this->arrAccuracy, 'condition' => $this->condAccuracy) =
            Word::getArrFilters($params, Word::FIELD_WORD['Accuracy']);
    }

    public function formName() {
        return '';
    }
}
