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

    public $firstDepartment;
    public $secondDepartment;
    public $thirdDepartment;
    public $firstScale;
    public $secondScale;
    public $thirdScale;

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

/*        $dataProvider->setSort([                      // нужен join
            'attributes' => [
                'name',
                'number',
                'type',
                'department.name' => [
                    'asc' => ['department.name' => SORT_ASC],
                    'desc' => ['department.name' => SORT_DESC],
                    'label' => 'Цех',
                    'default' => SORT_ASC
                ],
                'id_scale',
                'deleted'
            ]
        ]);*/

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

        if ($this->department_id != Word::ALL) {
            $query->andOnCondition(
                'department_id = :id OR department_id IN (SELECT id FROM word WHERE word.parent_id = :id)',
                [':id' => $this->department_id]
            );
        }
        if ($this->scale_id != Word::ALL) {
            $query->andFilterWhere(['scale_id' => $this->scale_id]);
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
