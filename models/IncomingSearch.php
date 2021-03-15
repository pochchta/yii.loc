<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IncomingSearch represents the model behind the search form of `app\models\Incoming`.
 */
class IncomingSearch extends Incoming
{
    public $limit;
    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;
    public $device_name, $device_number, $device_department;
    public $term, $term_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'device_id', 'status', 'payment', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['status'], 'default', 'value' => Status::ALL],
            [['payment'], 'default', 'value' => Status::ALL],
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

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->limit = Yii::$app->params['maxLinesIndex'];
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
        $query = Incoming::find()->with('creator', 'updater', 'device.wordName', 'device.wordDepartment');

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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

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

        foreach(['name', 'department'] as $item) {
            $depth = Word::MAX_NUMBER_PARENTS;
            if ($item == 'department') {
                $depth = Word::MAX_NUMBER_PARENTS - 1;
            }
            $field = $this->{'device_' . $item};
            if (strlen($field)) {
                list('condition' => $condition, 'bind' => $bind) = Word::getConditionByParent([
                    'parents' => [1 => $field],
                    'columnName' => "{$item}_id",
                    'depth' => $depth,
                    'withParent' => true
                ]);
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

    public function formName()
    {
        return '';
    }

}
