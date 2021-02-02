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
    const DEFAULT_LIMIT_RECORDS = 20;
    const PRINT_LIMIT_RECORDS = 500;
    public $limit = self::DEFAULT_LIMIT_RECORDS;
    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string', 'max' => 64],
            [['name', 'type', 'department', 'position', 'scale', 'accuracy', 'number'], 'string', 'min' => 1, 'max' => 20],
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
        $query = Device::find()
            ->select(['id', 'name_id', 'type_id', 'department_id', 'position_id', 'scale_id', 'accuracy_id', 'number', 'deleted'])
            ->with('creator', 'updater', 'wordName', 'wordType', 'wordDepartment', 'wordPosition', 'wordScale', 'wordAccuracy');

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
                [':name' => $this->name . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->type)) {
            $query->andOnCondition(
                'type_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) OR '
                . 'type_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) AND deleted = :del) OR '
                . 'type_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :type AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':type' => $this->type . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->department)) {    // глубина 2
            $query->andOnCondition(
                'department_id IN (SELECT id FROM word WHERE name LIKE :department AND deleted = :del) OR '
                . 'department_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :department AND deleted = :del) AND deleted = :del)',
                [':department' => $this->department . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->position)) {      // глубина 1
            $query->andOnCondition(
                'position_id IN (SELECT id FROM word WHERE name LIKE :position AND deleted = :del)',
                [':position' => $this->position . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->scale)) {
            $query->andOnCondition(
                'scale_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) OR '
                . 'scale_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) AND deleted = :del) OR '
                . 'scale_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :scale AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':scale' => $this->scale . '%', ':del' => Status::NOT_DELETED]
            );
        }

        if (strlen($this->accuracy)) {
            $query->andOnCondition(
                'accuracy_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) OR '
                . 'accuracy_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) AND deleted = :del) OR '
                . 'accuracy_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE name LIKE :accuracy AND deleted = :del) AND deleted = :del) AND deleted = :del)',
                [':accuracy' => $this->accuracy . '%', ':del' => Status::NOT_DELETED]
            );
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
     * @param $attribute
     * @return array
     */
    public static function getAutoCompleteOptions($attribute)
    {
        if ($attribute === 'position') {
            $parent = "$('#department').val() != '' ? $('#department').val() : 'position'";
        } else {
            $parent = "'". (isset(Word::FIELD_WORD[ucfirst($attribute)]) ? $attribute : '') . "'";
        }
        return [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('list-auto-complete') . "', {
                        term: request.term,
                        parent: {$parent}
                    }, response);
                }"),
                'minLength' => 3,
                'delay' => 300
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
        $data = Device::find()
            ->select(['number as value'])
            ->where(['deleted' => Status::NOT_DELETED])
            ->andOnCondition('number LIKE :number', [':number' => $this->number . '%'])
            ->orderBy('number')
            ->limit(Yii::$app->params['maxLinesAutoComplete'])
            ->distinct()
            ->asArray()->all();
        return json_encode($data);
    }
}
