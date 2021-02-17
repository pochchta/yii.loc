<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * DeviceSearch represents the model behind the search form of `app\models\Device`.
 */
class DeviceSearch extends Device
{
    const COLUMN_SEARCH = ['id', 'number'];
    const DEFAULT_LIMIT_RECORDS = 20;
    const PRINT_LIMIT_RECORDS = 500;
    public $limit = self::DEFAULT_LIMIT_RECORDS;

    public $term, $term_name;

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['name', 'type', 'department', 'position', 'scale', 'accuracy', 'number'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
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
        $query = Device::find()
            ->select(['id', 'name_id', 'type_id', 'department_id', 'position_id', 'scale_id', 'accuracy_id', 'number', 'deleted', 'created_at', 'updated_at'])
            ->with('creator', 'updater', 'wordName', 'wordType', 'wordDepartment', 'wordPosition', 'wordScale', 'wordAccuracy');

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
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description . '%', false]);
        $query->andFilterWhere(['like', 'number', $this->number . '%', false]);

        foreach(['name', 'type', 'department', 'position', 'scale', 'accuracy'] as $item) {
            $depth = 3;
            if ($item == 'department') {
                $depth = 2;
            } elseif ($item == 'position') {
                $depth = 1;
            }
            if (strlen($this->$item)) {
                list('condition' => $condition, 'bind' => $bind) =
                    Word::getConditionLikeName("{$item}_id", $this->$item, $depth, true);
                $query->andOnCondition($condition, $bind);
            }
        }

        if ($this->deleted != Status::ALL) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }

    /**
     * @param $attribute string
     * @param $prefix string префикс для атрибута при поиске GET параметра и селектора
     * @return array
     */
    public static function getAutoCompleteOptions($attribute, $prefix = '')
    {
        if ($attribute === 'position') {
            $parent = "$('#department').val() != '' ? $('#department').val() : 'position'";
        } else {
            $parent = "'". (isset(Word::FIELD_WORD[ucfirst($attribute)]) ? $attribute : '') . "'";
        }
        return [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('/device/list-auto-complete') . "', {
                        term: request.term,
                        term_name: '{$attribute}',
                        term_parent: {$parent},
                    }, response);
                }"),
                'select' => new JsExpression("function(event, ui) {
                    selectAutoComplete(event, ui, '{$prefix}_{$attribute}');
                }"),
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
            $data = Device::find()
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