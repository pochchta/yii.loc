<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * VerificationSearch represents the model behind the search form of `app\models\Verification`.
 */
class VerificationSearch extends Verification
{
    const COLUMN_SEARCH = ['id', 'name', 'type'];
    public $limit;
    public $last_date_start, $last_date_end, $next_date_start, $next_date_end;
    public $device_name, $device_number, $device_department;
    public $term, $term_name;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'device_id', 'period', 'created_at', 'updated_at', 'created_by', 'updated_by', 'type', 'status', 'deleted'], 'integer'],
            [['name', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['last_date_start', 'last_date_end', 'next_date_start', 'next_date_end'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['type'], 'default', 'value' => Status::ALL],
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

        if ($this->type == Verification::TYPE_VALUE['Default'] || $this->type == Verification::TYPE_VALUE['Gos']) {
            $query->andFilterWhere(['type' => $this->type]);
        }

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

    public function formName() {
        return '';
    }

    public static function getAutoCompleteOptions($attribute, $prefix = '', $autoSend = false)
    {
        if (strlen($prefix)) {
            $prefix = $prefix . '_';
        }
        $select = '';
        if ($autoSend) {
            $select = new JsExpression("function(event, ui) {
                selectAutoComplete(event, ui, '{$prefix}{$attribute}');
            }");
        }
        return [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('/device/list-auto-complete') . "', {
                        term: request.term,
                        term_name: '{$attribute}',
                    }, response);
                }"),
                'select' => $select,
                'minLength' => Yii::$app->params['minSymbolsAutoComplete'],
                'delay' => Yii::$app->params['delayAutoComplete']
            ],
            'options' => [
                'class' => 'form-control',
            ]
        ];
    }

    /** Поиск по номерам для AutoComplete
     * @return false|string
     */
    public function findNames()
    {
        $data = [];
        if (in_array($this->term_name, self::COLUMN_SEARCH)) {
            $data = Verification::find()
                ->select(["$this->term_name as value"])
                ->where(['deleted' => Status::NOT_DELETED])
                ->andOnCondition("$this->term_name LIKE :term", [':term' => $this->term . '%'])
                ->orderBy($this->term_name)
                ->limit(Yii::$app->params['maxLinesAutoComplete'])
                ->distinct()
                ->asArray()->all();
        }
        return json_encode($data);
    }

}