<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\validators\DefaultValueValidator;

/**
 * VerificationSearch represents the model behind the search form of `app\models\Verification`.
 */
class VerificationSearch extends Verification
{
    public $limit;
    public $created_at_start, $created_at_end, $updated_at_start, $updated_at_end;
    public $device_name, $device_number, $device_department;
    public $device_name_id, $device_department_id;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'device_id', 'period', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type_id', 'status_id', 'deleted_id'], 'integer'],
            [['name', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['created_at_start', 'created_at_end', 'updated_at_start', 'updated_at_end'], 'filter',
                'filter' => function($value) {return strtotime($value) !== false ? strtotime($value) : null;}],
            [['type_id'], 'default', 'value' => Status::ALL],
            [['status_id'], 'default', 'value' => Verification::STATUS_ON],
            [['deleted_id'], 'default', 'value' => Status::NOT_DELETED],
            [['device_name', 'device_number', 'device_department'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['device_name_id', 'device_department_id'], 'integer'],
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

        if (strlen($this->name)) {
            $query->andFilterWhere(['like', 'name', $this->name . '%', false]);
        }

        if ($this->status_id == Verification::STATUS_OFF || $this->status_id == Verification::STATUS_ON) {
            $query->andFilterWhere(['status_id' => $this->status_id]);
        }

        if ($this->deleted_id == Status::NOT_DELETED || $this->deleted_id == Status::DELETED) {
            $query->andFilterWhere(['deleted_id' => $this->deleted_id]);
        }

        if (strlen($this->created_at_start)) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at_start]);
        }
        if (strlen($this->created_at_end)) {
            $query->andFilterWhere(['<=', 'created_at', $this->created_at_end]);
        }
        if (strlen($this->updated_at_start)) {
            $query->andFilterWhere(['>=', 'updated_at', $this->updated_at_start]);
        }
        if (strlen($this->updated_at_end)) {
            $query->andFilterWhere(['<=', 'updated_at', $this->updated_at_end]);
        }

        foreach(['name', 'department'] as $item) {
            $item_id = "{$item}_id";
            $item_with_prefix = "device_{$item}";
            $item_id_with_prefix = "device_{$item_id}";

            if (strlen($this->$item_with_prefix)) {                                             // поиск по имени
                $subQueries = Word::getQueriesToGetChildren(['like', 'name', $this->$item_with_prefix . '%', false]);
                $devQuery = Device::find()->select('id')
                    ->andFilterWhere(['deleted_id' => Status::NOT_DELETED])
                    ->andFilterWhere(Word::mergeQueriesOr($subQueries, $item_id, [0,1,2]));
                $query->andFilterWhere(['device_id' => $devQuery]);
            }

            if (strlen($this->$item_id_with_prefix)) {                                          // поиск по id
                $subQueries = Word::getQueriesToGetChildren(['id' => $this->$item_id_with_prefix]);
                $devQuery = Device::find()->select('id')
                    ->andFilterWhere(['deleted_id' => Status::NOT_DELETED])
                    ->andFilterWhere(Word::mergeQueriesOr($subQueries, $item_id, [0,1,2]));
                $query->andFilterWhere(['device_id' => $devQuery]);
            }
        }

        if (strlen($this->device_number)) {
            $query->andFilterWhere([
                'device_id' => Device::find()->select('id')
                    ->andFilterWhere(['deleted_id' => Status::NOT_DELETED])
                    ->andFilterWhere(['like', 'number', $this->device_number . '%', false])
            ]);
        }

        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }

    public function getDefaultValidators()
    {
        $validators = $this->getActiveValidators();
        $validators = array_filter($validators, function ($item) {
            return $item instanceof DefaultValueValidator;
        });
        $arrayValidators = [];
        $validators = array_walk($validators, function ($item) use (&$arrayValidators) {
            $arrayValidators[$item->attributes[0]] = $item->value;
        });
        return $arrayValidators;
    }
}