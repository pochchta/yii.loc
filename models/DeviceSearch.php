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

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'number', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => 64],
            [['name', 'type', 'department', 'position', 'scale', 'accuracy'], 'string', 'max' => 64],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
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

        if (strlen($this->name)) {
            $query->andOnCondition(
                'name_id IN (SELECT id FROM word WHERE name LIKE :name AND deleted = :del) OR '
                . 'name_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :name AND deleted = :del) AND deleted = :del) OR '
                . 'name_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :name AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':name' => '%' . $this->name . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->type)) {
            $query->andOnCondition(
                'type_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) OR '
                . 'type_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) AND deleted = :del) OR '
                . 'type_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':type' => '%' . $this->type . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->department)) {
            $query->andOnCondition(
                'department_id IN (SELECT id FROM word WHERE name LIKE :department AND deleted = :del) OR '
                . 'department_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :department AND deleted = :del) AND deleted = :del) OR '
                . 'department_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :department AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':department' => '%' . $this->department . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->position)) {
            $query->andOnCondition(
                'position_id IN (SELECT id FROM word WHERE name LIKE :position AND deleted = :del) OR '
                . 'position_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :position AND deleted = :del) AND deleted = :del) OR '
                . 'position_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :position AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':position' => '%' . $this->position . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->scale)) {
            $query->andOnCondition(
                'scale_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) OR '
                . 'scale_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) AND deleted = :del) OR '
                . 'scale_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':scale' => '%' . $this->scale . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->accuracy)) {
            $query->andOnCondition(
                'accuracy_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) OR '
                . 'accuracy_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) AND deleted = :del) OR '
                . 'accuracy_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':accuracy' => '%' . $this->accuracy . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if ($this->deleted != Status::ALL) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function formName() {
        return '';
    }
}
